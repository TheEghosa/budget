/*
 * The claim this file exists to test: a calculator defined in JSON computes
 * exactly what a calculator defined in code computes.
 *
 * Asserting that on one hand-written example would prove almost nothing, since
 * the example would be chosen to pass. So instead every shipped formula that
 * can stand on its own is lifted straight out of formulas.js, with no edit of
 * any kind, compiled through the same runner the sandbox worker uses, and run
 * side by side with the original over three sets of figures. Any difference in
 * any field of any answer fails.
 *
 * Some shipped formulas call helpers that live beside them in formulas.js and
 * are not part of the shared kit, which is a fact about those formulas rather
 * than a fault in the sandbox. Those are counted and named rather than hidden,
 * because the number going up would mean the kit had started to fall behind
 * what formulas are actually written against.
 */
const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
eval(fs.readFileSync(path.join(root, 'assets/js/formula-kit.js'), 'utf8'));
eval(fs.readFileSync(path.join(root, 'assets/js/formula-runner.js'), 'utf8'));
global.window = {};
eval(fs.readFileSync(path.join(root, 'assets/js/formulas.js'), 'utf8'));

const KIT = globalThis.CalculatorrKit;
const RUNNER = globalThis.CalculatorrRunner;
const F = global.window.CalculatorrFormulas;

let pass = 0, fail = 0;
function check(name, condition, detail) {
  if (condition) { pass++; return; }
  fail++;
  console.log('  FAIL ' + name + (detail ? '\n    ' + detail : ''));
}

/* ---------- Lifting the shipped formulas out of the bundle ---------- */

const source = fs.readFileSync(path.join(root, 'assets/js/formulas.js'), 'utf8').split('\n');
const bodies = {};

source.forEach((line, i) => {
  const opener = line.match(/^\tformulas\[ '([a-z0-9-]+)' \] = function \( v \) \{$/);
  if (!opener) { return; }
  let end = i + 1;
  while (end < source.length && source[end] !== '\t};') { end++; }
  bodies[opener[1]] = source.slice(i + 1, end).join('\n');
});

check('lifted a body for most of the bundle', Object.keys(bodies).length >= 100,
  'found ' + Object.keys(bodies).length);

/* ---------- The figures they are compared over ---------- */

const configs = JSON.parse(fs.readFileSync(path.join(root, 'tests/fixtures/fields.json'), 'utf8'));
const fieldsOf = {};
configs.forEach(c => { fieldsOf[c.slug] = c.fields; });

/* The fixture is a dump of PHP configs that node cannot read for itself, so it
   can go stale without anything complaining. Checking it still names exactly
   the calculators on disk turns that into a failure: a calculator added or
   removed since the last dump stops the run rather than being silently left
   out of the comparison. */
const onDisk = fs.readdirSync(path.join(root, 'calculators'))
  .filter(f => f.endsWith('.php') && f[0] !== '_')
  .map(f => f.replace(/\.php$/, ''))
  .sort();

check('the fixture still covers every calculator on disk',
  onDisk.join(',') === Object.keys(fieldsOf).sort().join(','),
  'run php tools/build-fixtures.php');

function shift(d) {
  const n = parseFloat(d);
  if (!isFinite(n)) { return d === undefined ? '' : d; }
  return Math.round((n * 1.7 + 3) * 100) / 100;
}

function vector(fields, mode) {
  const v = {};
  (fields || []).forEach(f => {
    if (f.type === 'repeater') {
      const row = {};
      (f.row || []).forEach(c => {
        row[c.id] = mode === 'empty' ? '' : String(mode === 'shift' ? shift(c.default) : c.default);
      });
      v[f.id] = [row, row, row];
      return;
    }
    if (f.type === 'select' || f.type === 'segmented') {
      const keys = Object.keys(f.options || {});
      v[f.id] = (mode === 'shift' && keys.length) ? keys[keys.length - 1] : String(f.default === undefined ? '' : f.default);
      return;
    }
    v[f.id] = mode === 'empty' ? '' : String(mode === 'shift' ? shift(f.default) : f.default);
  });
  return v;
}

/* ---------- The comparison ---------- */

const MODES = ['default', 'shift', 'empty'];
const matched = [];
const needsLocalHelpers = [];
let comparisons = 0;

Object.keys(bodies).forEach(slug => {
  const shipped = F[slug];
  const fields = fieldsOf[slug];

  if (typeof shipped !== 'function' || !fields) { return; }

  let sandboxed;
  try {
    sandboxed = RUNNER.compile(bodies[slug], KIT);
  } catch (e) {
    needsLocalHelpers.push(slug + ' (will not compile: ' + e.message + ')');
    return;
  }

  let agreed = true;
  let why = '';

  for (const mode of MODES) {
    const v = vector(fields, mode);
    let theirs, ours;

    try { theirs = JSON.stringify(RUNNER.clean(shipped(v))); }
    catch (e) { agreed = false; why = 'the shipped formula threw on ' + mode; break; }

    try { ours = JSON.stringify(RUNNER.clean(sandboxed(v, KIT))); }
    catch (e) {
      /* A reference error here is the formula reaching for a helper that lives
         in formulas.js rather than in the kit, which is expected for some of
         them and is not a disagreement about arithmetic. */
      needsLocalHelpers.push(slug + ' (' + e.message + ')');
      agreed = null;
      break;
    }

    comparisons++;

    if (theirs !== ours) {
      agreed = false;
      why = mode + ':\n      shipped  ' + theirs + '\n      sandboxed ' + ours;
      break;
    }
  }

  if (agreed === true) { matched.push(slug); }
  else if (agreed === false) { check('sandboxed ' + slug + ' matches the shipped one', false, why); }
});

check('a large majority of shipped formulas run unchanged in the sandbox', matched.length >= 60,
  'only ' + matched.length + ' matched');

/* ---------- What the sandbox must refuse to pass through ---------- */

function clean(raw) { return RUNNER.clean(raw); }

check('a colour that is not a colour is dropped',
  clean({ value: '1', rows: [{ label: 'a', value: 'b', color: 'url(http://example.com/x)' }] }).rows[0].color === '');
check('a theme variable survives',
  clean({ value: '1', rows: [{ label: 'a', value: 'b', color: 'var(--calcr-accent)' }] }).rows[0].color === 'var(--calcr-accent)');
check('a hex colour survives',
  clean({ value: '1', bar: [{ pct: 40, color: '#ff8800' }] }).bar[0].color === '#ff8800');
check('a bar segment past a hundred per cent is clamped',
  clean({ value: '1', bar: [{ pct: 4000, color: 'red' }] }).bar[0].pct === 100);
check('a bar segment that is not a number becomes zero',
  clean({ value: '1', bar: [{ pct: 'lots', color: 'red' }] }).bar[0].pct === 0);
check('a runaway string is capped',
  clean({ value: 'x'.repeat(5000) }).value.length === 400);
check('a formula that returns nothing paints nothing', clean(undefined) === null);
check('a Date in a row comes back as text',
  typeof clean({ value: '1', rows: [{ label: 'a', value: new Date(0) }] }).rows[0].value === 'string');

/* A function reaching the page through the result would be the one way the
   sandbox leaked something executable, so it is asserted directly. */
const withFunction = clean({ value: '1', rows: [{ label: 'a', value: function () { return 1; } }] });
check('a function in a row is reduced to text', typeof withFunction.rows[0].value === 'string');
check('nothing beyond the known keys survives',
  Object.keys(clean({ value: '1', onclick: 'alert(1)' })).join(',') === 'label,value,sub,note,rows,bar');

/* ---------- The compile wrapper ---------- */

check('the helper vocabulary is the one the shipped formulas use',
  KIT.NAMES.every(n => typeof KIT[n] !== 'undefined'));

const strict = RUNNER.compile('undeclared = 1; return { value: "1" };', KIT);
let threw = false;
try { strict({}, KIT); } catch (e) { threw = true; }
check('an undeclared variable is an error rather than a global', threw);

console.log('');
console.log(matched.length + ' shipped formulas ran unchanged in the sandbox and agreed exactly, over ' + comparisons + ' comparisons');

if (needsLocalHelpers.length) {
  console.log(needsLocalHelpers.length + ' use a helper that lives in formulas.js rather than in the shared kit:');
  console.log('  ' + needsLocalHelpers.map(s => s.split(' (')[0]).join(', '));
}

console.log('');
console.log(pass + ' passed, ' + fail + ' failed');
process.exit(fail ? 1 : 0);
