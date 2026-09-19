/*
 * Checks a JSON calculator and computes the answer its page will ship with.
 *
 * Run this before publishing anything. It compiles the formula through the
 * same runner the sandbox worker uses, runs it over the field defaults, and
 * writes the result back into the definition as default_result. That is the
 * answer the server renders into the HTML a crawler indexes and a visitor sees
 * in the moment before the sandbox replies, so computing it rather than typing
 * it is the only arrangement where the two cannot disagree.
 *
 * It then runs the formula again over a shifted set of figures and over an
 * empty one, because a formula that only works on the numbers its author had
 * in mind is the most common way one of these breaks, and both of those cases
 * reach a visitor within about a minute of the page going live.
 *
 * Usage: node tools/json-calculator.js <definition.json> [--write]
 */
const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
eval(fs.readFileSync(path.join(root, 'assets/js/formula-kit.js'), 'utf8'));
eval(fs.readFileSync(path.join(root, 'assets/js/formula-runner.js'), 'utf8'));

const KIT = globalThis.CalculatorrKit;
const RUNNER = globalThis.CalculatorrRunner;

const file = process.argv[2];
const write = process.argv.includes('--write');

if (!file) {
  console.error('Usage: node tools/json-calculator.js <definition.json> [--write]');
  process.exit(2);
}

const definition = JSON.parse(fs.readFileSync(file, 'utf8'));
const problems = [];

/* The vector the server will render, which is every field's default whether or
   not it is currently on screen. A formula reads the hidden imperial inputs as
   readily as the visible metric ones, so leaving them out here would compute a
   different answer from the one the browser produces. */
function vector(fields, mode) {
  const v = {};
  (fields || []).forEach(f => {
    if (f.type === 'repeater') {
      const row = {};
      (f.row || []).forEach(c => {
        row[c.id] = mode === 'empty' ? '' : String(mode === 'shift' ? shift(c.default) : (c.default === undefined ? '' : c.default));
      });
      v[f.id] = Array.from({ length: f.rows || 3 }, () => Object.assign({}, row));
      return;
    }
    if (f.type === 'select' || f.type === 'segmented') {
      const keys = Object.keys(f.options || {});
      v[f.id] = (mode === 'shift' && keys.length) ? keys[keys.length - 1] : String(f.default === undefined ? '' : f.default);
      return;
    }
    v[f.id] = mode === 'empty' ? '' : String(mode === 'shift' ? shift(f.default) : (f.default === undefined ? '' : f.default));
  });
  return v;
}

function shift(d) {
  const n = parseFloat(d);
  if (!isFinite(n)) { return d === undefined ? '' : d; }
  return Math.round((n * 1.7 + 3) * 100) / 100;
}

let compiled;
try {
  compiled = RUNNER.compile(definition.formula || '', KIT);
} catch (e) {
  console.error('The formula will not compile: ' + e.message);
  process.exit(1);
}

function run(mode) {
  try {
    return { ok: true, result: RUNNER.clean(compiled(vector(definition.fields, mode), KIT)) };
  } catch (e) {
    return { ok: false, error: e.message };
  }
}

const main = run('default');

if (!main.ok) { problems.push('on its own defaults the formula threw: ' + main.error); }
else if (!main.result) { problems.push('on its own defaults the formula returned nothing to show'); }
else if (!main.result.value) { problems.push('on its own defaults the formula returned no headline value'); }

['shift', 'empty'].forEach(mode => {
  const r = run(mode);
  if (!r.ok) { problems.push(`with ${mode === 'shift' ? 'different figures' : 'every field blank'} the formula threw: ${r.error}`); }
});

/* Every id the formula reads has to be a field somebody can fill in, and every
   field somebody can fill in ought to reach the formula. The second half is a
   warning rather than an error, because a field can legitimately exist to
   drive another field's visibility. */
const ids = (definition.fields || []).map(f => f.id);
const read = new Set();
String(definition.formula || '').replace(/\bv\.([A-Za-z_][A-Za-z0-9_]*)/g, (m, id) => { read.add(id); return m; });

read.forEach(id => {
  if (!ids.includes(id)) { problems.push(`the formula reads v.${id} and there is no field called ${id}`); }
});

const unread = ids.filter(id => !read.has(id));

if (problems.length) {
  console.error(`${definition.slug || file}:`);
  problems.forEach(p => console.error('  ' + p));
  process.exit(1);
}

if (write) {
  definition.default_result = {
    label: main.result.label,
    value: main.result.value,
    rows: main.result.rows.map(r => ({ label: r.label, value: r.value })),
  };
  if (main.result.sub) { definition.default_result.sub = main.result.sub; }
  if (main.result.note) { definition.default_result.note = main.result.note; }

  fs.writeFileSync(file, JSON.stringify(definition, null, 2) + '\n');
}

console.log(`${definition.slug}: ${main.result.label} = ${main.result.value}`);
main.result.rows.forEach(r => console.log(`  ${r.label}: ${r.value}`));
if (unread.length) { console.log(`  note: no v.<id> reference to ${unread.join(', ')}`); }
console.log(write ? '  default_result written' : '  run again with --write to store that as default_result');
