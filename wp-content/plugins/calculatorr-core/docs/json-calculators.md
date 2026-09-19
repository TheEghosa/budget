# Building a calculator in JSON

Until now a calculator meant two files in the plugin: a PHP config under
`calculators/` and a function in `assets/js/formulas.js`. Adding one meant
building a zip, uploading it and reactivating, which is a reasonable price for
a handful of tools and a bad one for a site whose whole purpose is to keep
adding them.

A JSON definition carries everything in a single document, and it can be
published straight over the REST API. The registry folds it in beside the PHP
configs through the same defaults and the same admin overrides, so by the time
the renderer, the schema generator or the sitemap sees a config there is
nothing left to tell the two apart.

## The shape of a definition

```json
{
  "slug": "bmr-calculator",
  "title": "BMR Calculator",
  "category": "health",
  "description": "One sentence, used as the meta description when none is given.",
  "fields": [ ... ],
  "formula": "return { label: 'Result', value: decimals( num( v.amount ), 2 ) };",
  "explainer": [ ... ],
  "faqs": [ ... ],
  "related": [ "tdee-calculator", "bmi-calculator" ],
  "disclaimer": "Optional, rendered below the calculator.",
  "default_result": { "label": "Result", "value": "12.34", "rows": [] }
}
```

`slug`, `title`, `category`, `description`, `fields`, `formula` and
`default_result` are required. `keyword`, `h1`, `meta_title`,
`meta_description`, `explainer`, `faqs`, `related`, `sources` and `disclaimer`
are optional, and the registry fills in sensible versions of the first four
from the title when they are missing.

An unrecognised key is refused rather than dropped, because dropping it
silently means somebody writes a definition using a feature that does not
exist, watches it publish, and never finds out that half of what they wrote
went nowhere.

## Fields

Six types, which are the six the renderer can draw: `number`, `text`, `date`,
`select`, `segmented` and `repeater`.

```json
{ "id": "weight", "label": "Weight", "type": "number", "suffix": "kg",
  "default": "70", "min": "0", "show_when": { "units": "metric" } }
```

The `id` becomes the property name the formula reads as `v.weight`, so it has
to be a plain identifier: a letter first, then letters, digits or underscores.
A hyphen would produce a field the formula has no way to name.

`show_when` hides a field until another field holds a given value, which is how
the metric and imperial versions of one measurement stay out of each other's
way. Two fields may deliberately share an `id` when their `show_when` clauses
differ, because then whichever one is showing feeds the same name into the
formula. Sharing an `id` while both are visible is refused, since one of them
would silently win and nobody could tell which.

`select` and `segmented` need an `options` object, and their `default` has to
be one of its keys. A `repeater` needs a `row` of cells, each of which can be
`number`, `text` or `select`, and it arrives at the formula as an array of
objects so it can be iterated naturally.

## The formula

The `formula` is the **body** of a JavaScript function, not a whole function.
It receives the field values as `v` and returns the object the panel paints:

```js
var kg = num( v.weight );
var m = num( v.height ) / 100;

return {
  label: 'Body mass index',
  value: m > 0 ? decimals( kg / ( m * m ), 1 ) : '—',
  sub: 'optional line under the number',
  bar: [ { pct: 60, color: ACCENT }, { pct: 40, color: NEUTRAL } ],
  rows: [ { label: 'Category', value: 'Healthy weight', emphasis: false } ],
  note: 'optional line under the rows'
};
```

Everything in `assets/js/formula-kit.js` is available as a local, so a formula
reads exactly like one of the shipped hundred and five: `num`, `money`,
`money2`, `decimals`, `years`, `share`, `toKg`, `toCm`, `addDays`, `fmtDate`,
`parseDate`, `parseClock`, `clockText`, `hhmm`, `monthlyPayment`, `listOf`,
`gcd`, `simplify`, `fractionText`, and the three colour constants `ACCENT`,
`WARN` and `NEUTRAL`. `Math`, loops, conditionals, strings and dates all work
normally, because a formula that could not do those could not replace a coded
one.

### Where it runs

Not on the page. The renderer prints the source beside the calculator and the
runtime posts it to a Web Worker, which loads the helper kit and then removes
`fetch`, `XMLHttpRequest`, `importScripts`, `WebSocket`, `EventSource`,
`navigator`, the storage APIs and everything else that reaches outside itself
before the body is compiled. A worker has no DOM to begin with. What is left
can do arithmetic, which is all a formula has ever needed.

A formula that does not return within two seconds has its worker terminated and
replaced, so a loop written with the wrong exit condition costs one answer
rather than the visitor's tab.

The validator also refuses a formula that names any of those globals. That is a
second line rather than the first, and mostly it is a courtesy: the sandbox is
what actually stops anything reaching the network, and a name check can be
written around by anyone determined to. It stays because the realistic failure
is a formula written against the wrong mental model, and being told at the
point of writing beats failing silently in front of a visitor.

## The worked answer

`default_result` is what the server renders into the HTML before any script has
loaded. A crawler indexes it and a visitor reads it in the moment before the
sandbox replies, so it has to be the real answer for the field defaults rather
than something plausible typed in by hand.

Never write it yourself. Run:

```
node tools/json-calculator.js content/definitions/<slug>.json --write
```

That compiles the formula through the same runner the worker uses, runs it over
the field defaults, and writes the result back into the file. It also runs the
formula over a shifted set of figures and over an empty one, because a formula
that only works on the numbers its author had in mind is the commonest way one
of these breaks and both of those cases reach a visitor within a minute of the
page going live.

## Publishing

```
curl -u "$WP_USER:$WP_PASS" -X POST \
  -H 'Content-Type: application/json' \
  --data-binary @content/definitions/bmr-calculator.json \
  "$WP_SITE/wp-json/calculatorr/v1/calculator/bmr-calculator"
```

The route is administrator-only. It validates, stores the definition in the
database, reloads the registry and creates the page, then reports back how many
fields, sections and questions landed and what the page's URL is.

`GET` reads one back, `DELETE` removes the database copy, and
`/calculatorr/v1/calculators` lists what exists and where each one is stored.

A write replaces rather than merges, unlike the content route. A definition
describes one tool as a whole, and merging a partial one would leave a
calculator whose fields came from today and whose formula came from last week.

## Database now, repository next build

Definitions load from two places. `calculators/json/*.json` ships with the
plugin, and the database holds what has been published since. **Files win.**

So the working rhythm is: publish over REST to put a calculator live today,
commit the same file to `calculators/json/` so the next build ships it, and
the database copy stands aside the moment that build lands. Nobody has to
remember to delete anything, and the live definition never changes in the
handover.

The REST response says which of the two is serving each slug, so a write that
lands behind a shipped file reports itself rather than leaving somebody
believing a calculator was updated when it was not.

## Before you publish

```
node tools/json-calculator.js content/definitions/<slug>.json --write
php tests/test-integrity.php
node tests/test-sandbox.js
php tools/build-preview.php /tmp/preview
node tools/check-in-browser.js /tmp/preview <slug> height=175 weight=85
```

The last one is the only check that covers the thing end to end. The parity
test proves the arithmetic and the render test proves the markup, but neither
starts a worker, and the worker is where a JSON calculator actually computes.
It also probes the sandbox directly, driving the worker the way a compromised
page would, and reports anything a formula can still reach.

The content still has to meet `docs/content-standard.md`. A calculator that is
quicker to publish is not a licence to publish a thinner one.
