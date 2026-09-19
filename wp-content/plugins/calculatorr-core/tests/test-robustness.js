/*
 * Every formula, against the inputs a real visitor produces by accident:
 * empty fields, zero, negatives, text in a number box, and numbers far
 * outside any sensible range.
 *
 * A formula may legitimately return a dash or a note for nonsense input. What
 * it may not do is throw, or hand back NaN, Infinity or "undefined" for the
 * page to print at somebody.
 *
 * Usage: node tests/test-robustness.js
 */
const fs = require('fs');
const path = require('path');
const { execFileSync } = require('child_process');

const root = path.join(__dirname, '..');
global.window = {};
eval(fs.readFileSync(path.join(root, 'assets/js/formula-kit.js'), 'utf8'));
eval(fs.readFileSync(path.join(root, 'assets/js/formulas.js'), 'utf8'));
const F = global.window.CalculatorrFormulas;

const spec = JSON.parse(execFileSync('php', [path.join(root, 'tools/build-defaults.php')], { encoding: 'utf8' }));

const CASES = {
  empty:    () => '',
  zero:     () => '0',
  negative: () => '-5',
  text:     () => 'abc',
  huge:     () => '999999999999',
  tiny:     () => '0.0000001',
  spaces:   () => '   ',
  comma:    () => '1,000,000',
};

/* "undefined" standing alone is a legitimate answer: the slope of a vertical
   line genuinely is undefined. What is never legitimate is the JavaScript
   value leaking into a longer string, so the check looks for it embedded
   rather than on its own. */
const BAD = /NaN|Infinity|\[object|\S\s*undefined|undefined\s*\S/;
let failures = [];
let runs = 0;

function inspect(slug, caseName, r) {
  runs++;
  if (r === undefined || r === null) { failures.push(`${slug} [${caseName}] returned nothing`); return; }
  const parts = [String(r.label || ''), String(r.value === undefined ? '' : r.value), String(r.note || '')];
  (r.rows || []).forEach(row => parts.push(String(row.label), String(row.value)));
  parts.forEach(text => {
    if (BAD.test(text)) failures.push(`${slug} [${caseName}] produced "${text}"`);
  });
  (r.bar || []).forEach(seg => {
    if (!isFinite(seg.pct) || seg.pct < 0 || seg.pct > 100.01) {
      failures.push(`${slug} [${caseName}] bar segment at ${seg.pct}%`);
    }
  });
}

spec.forEach(item => {
  const fn = F[item.slug];
  if (!fn) { failures.push(`${item.slug}: no formula`); return; }

  // Baseline with the real defaults first.
  try { inspect(item.slug, 'defaults', fn(item.values)); }
  catch (e) { failures.push(`${item.slug} [defaults] threw ${e.message}`); }

  Object.keys(CASES).forEach(caseName => {
    const vals = {};
    Object.keys(item.values).forEach(k => {
      vals[k] = Array.isArray(item.values[k])
        ? item.values[k].map(row => {
            const out = {}; Object.keys(row).forEach(c => out[c] = CASES[caseName]()); return out;
          })
        : CASES[caseName]();
    });
    try { inspect(item.slug, caseName, fn(vals)); }
    catch (e) { failures.push(`${item.slug} [${caseName}] threw ${e.message}`); }
  });

  // Also: every field blanked except one, which is what a half-filled form is.
  Object.keys(item.values).forEach(keep => {
    const vals = {};
    Object.keys(item.values).forEach(k => vals[k] = (k === keep ? item.values[k] : ''));
    try { inspect(item.slug, `only ${keep}`, fn(vals)); }
    catch (e) { failures.push(`${item.slug} [only ${keep}] threw ${e.message}`); }
  });
});

console.log(`${runs} formula runs across ${spec.length} calculators\n`);
failures.slice(0, 200).forEach(f => console.log('  FAIL ' + f));
if (failures.length > 200) console.log(`  ... and ${failures.length - 200} more`);
console.log(`\n${failures.length} failures`);
process.exit(failures.length ? 1 : 0);
