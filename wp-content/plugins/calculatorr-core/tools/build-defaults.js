/*
 * Computes each calculator's default result from its own formula and writes
 * calculators/_defaults.php.
 *
 * Running this after any formula change keeps the server-rendered answer and
 * the JavaScript answer in step. They drifted once already, when a rounding
 * bug was fixed in the formulas but the stored defaults were left alone.
 *
 * Usage: php tools/build-defaults.php > /tmp/in.json && node tools/build-defaults.js /tmp/in.json
 */
const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
global.window = {};
eval(fs.readFileSync(path.join(root, 'assets/js/formula-kit.js'), 'utf8'));
eval(fs.readFileSync(path.join(root, 'assets/js/formulas.js'), 'utf8'));
const F = global.window.CalculatorrFormulas;

const items = JSON.parse(fs.readFileSync(process.argv[2], 'utf8'));
const problems = [];
const results = {};

items.forEach(item => {
  const fn = F[item.slug];
  if (!fn) { problems.push(`${item.slug}: no formula`); return; }
  let r;
  try { r = fn(item.values); }
  catch (e) { problems.push(`${item.slug}: threw ${e.message}`); return; }
  if (!r || r.value === undefined) { problems.push(`${item.slug}: returned nothing`); return; }
  results[item.slug] = {
    label: String(r.label || 'Result'),
    value: String(r.value),
    rows: (r.rows || []).map(x => ({ label: String(x.label), value: String(x.value) })),
    note: String(r.note || ''),
  };
});

if (problems.length) { console.error(problems.join('\n')); process.exit(1); }

const esc = s => "'" + String(s).replace(/\\/g, '\\\\').replace(/'/g, "\\'") + "'";
let php = `<?php
/**
 * Generated file. Do not edit by hand.
 *
 * Each calculator's server-rendered default result, computed from its own
 * formula by tools/build-defaults.js so the HTML a crawler indexes can never
 * disagree with what the script produces in the browser.
 */

if ( ! defined( 'ABSPATH' ) ) {
\texit;
}

return array(
`;

Object.keys(results).sort().forEach(slug => {
  const r = results[slug];
  php += `\t${esc(slug)} => array(\n`;
  php += `\t\t'label' => ${esc(r.label)},\n`;
  php += `\t\t'value' => ${esc(r.value)},\n`;
  php += `\t\t'note'  => ${esc(r.note)},\n`;
  php += `\t\t'rows'  => array(\n`;
  r.rows.forEach(row => {
    php += `\t\t\tarray( 'label' => ${esc(row.label)}, 'value' => ${esc(row.value)} ),\n`;
  });
  php += `\t\t),\n\t),\n`;
});

php += ');\n';
fs.writeFileSync(path.join(root, 'calculators/_defaults.php'), php);
console.log(`wrote _defaults.php for ${Object.keys(results).length} calculators`);
