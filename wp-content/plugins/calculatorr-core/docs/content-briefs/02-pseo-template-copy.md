# Job two: template copy, where one file becomes hundreds of pages

This is the harder job and the more valuable one. You write one file, and it
renders as every page in a set: 171 concrete slab sizes, 740 party quantity
pages, 1,000 date pages. Everything you write has to stay true across every
value the variables take, which is a different discipline from writing one page
well.

Read `00-how-this-works.md` first. Its rules apply here and are not repeated.

## How a template page is built

Three things combine. The **variables** are what changes between pages, the
width and length of a slab or the number of guests at a party. The **computed
values** are what the calculator works out from those variables, and they arrive
as slots you drop into your prose. Your **copy** is the rest, and it is what
turns a number into an answer.

A slot is written `{name}` and is replaced with the computed value for that
page. So this:

> A {w} by {l} foot slab needs {yards4} cubic yards of concrete at four inches
> thick, which is {bags80_4} eighty pound bags.

becomes this on `/concrete/20x20-slab/`:

> A 20 by 20 foot slab needs 4.94 cubic yards of concrete at four inches thick,
> which is 223 eighty pound bags.

and this on `/concrete/8x10-slab/`:

> An 8 by 10 foot slab needs 0.99 cubic yards of concrete at four inches thick,
> which is 45 eighty pound bags.

Slots are already formatted. `{yards4}` arrives as `4.94` and `{bags80_4}` as
`223`, so never write `{yards4} cubic yards rounded up`, because the rounding
has happened and saying so reads as though it has not.

## The rule that decides whether this works: conditional sections

If every page says the same thing with different numbers, the set is thin
content and will be treated as such however many pages it has. So the copy has
to actually change, not just the figures in it.

Every section and every question may carry a `when` condition. The section
appears only on pages where the condition is true. That is the mechanism by
which a small slab page and a large slab page give genuinely different advice:

```json
{ "heading": "You can mix this by hand",
  "body": "At {bags80_4} bags you are within reach of ...",
  "when": "bags80_4 <= 25" }
```

```json
{ "heading": "Order ready-mix rather than bagging this",
  "body": "At {yards4} cubic yards you are well past ...",
  "when": "yards4 >= 3" }
```

**Every template needs at least three conditional sections, and each one must
fire on some pages and not others.** The build tool counts how many distinct
page bodies the template produces across its whole set, and refuses to publish a
template whose pages are too similar. A template that fails comes straight back,
so it is worth thinking about the branches before you write the prose.

Good branches come from real thresholds in the subject. A slab crosses the point
where hand mixing stops being sensible. A party crosses the point where one oven
will not do it. A walk crosses the point where you need water with you. A salary
crosses a tax bracket. Look for the point where the advice genuinely changes and
put the branch there, because a branch at an arbitrary number is padding wearing
a condition.

### Writing a condition

Conditions are deliberately simple, since anything more expressive would be a
programming language and this is copy. A condition is one or more comparisons
joined by `and` or `or`:

```
area >= 300
bags80_4 <= 25
guests > 50 and guests <= 150
food == "pulled pork"
miles >= 10 or minutes >= 180
```

Left side is a variable or a computed value, and you can only use names listed
for that template. Operators are `==`, `!=`, `<`, `<=`, `>`, `>=`. Strings go in
double quotes. There are no brackets and no negation, because a condition that
needs them is a sign the section should have been two sections.

## Length, which is different here

Three hundred to six hundred words per rendered page, and remember that what you
write is not what renders. A template with twelve sections of which seven are
conditional might render four hundred words on one page and six hundred on
another, and both are right.

Do not pad. These pages win by answering one specific question faster and more
completely than the forum thread currently ranking for it, and a reader who
wanted an essay would not have typed a number into Google.

## The file you send

One file per template, named `<template-id>.json`, for example `P01.json`.

```json
{
  "template": "P01",
  "title": "How Much Concrete for a {w}x{l} Slab? {yards4} Cubic Yards",
  "meta_description": "A {w} by {l} foot concrete slab needs {yards4} cubic yards at 4 inches, or {bags80_4} eighty pound bags. Bag counts at 4, 5 and 6 inches, plus cost.",
  "h1": "Concrete for a {w} by {l} foot slab",
  "answer": "One or two sentences that answer the query outright, with the headline number in them. This is what a search result snippet and an answer box will take, so it has to stand alone.",
  "sections": [
    {
      "heading": "A heading, which may contain slots",
      "body": "Paragraphs separated by \n\n.",
      "when": "optional condition",
      "table": {
        "caption": "Optional",
        "head": ["Depth", "Cubic yards", "80 lb bags"],
        "rows": "computed:depth_table"
      }
    }
  ],
  "faqs": [
    { "q": "A question, which may contain slots", "a": "The answer.", "when": "optional" }
  ]
}
```

`title`, `meta_description`, `h1`, `answer` and `sections` are required, and
`faqs` is strongly wanted because these queries are questions.

The `title` must stay under about 60 characters once the slots are filled at
their longest values, and the `meta_description` under about 155. Check the
longest case rather than the example, since `{w}` can be 4 or 50.

A table's `rows` can either be written out as literal cells, the same as job
one, or set to `"computed:<name>"` where the template spec below lists a
computed table. A computed table is filled by the calculator, which is how a
table stays correct across every page without you writing 171 of them.

## Templates ready to write now

These are the first two releases, 1,332 pages between them. The rest follow as
each template's calculator is built, because the slot list comes from the
calculator and promising you slots that do not exist yet would waste your time.

---

### P01 concrete slabs, 171 pages

`/concrete/{w}x{l}-slab/`, for example `/concrete/20x20-slab/`.

Widths and lengths come from 4, 5, 6, 8, 10, 12, 14, 15, 16, 18, 20, 24, 25, 30,
32, 36, 40 and 50 feet, paired so that width is never greater than length, which
is why there are 171 pages rather than 324.

| Slot | What it is | Example at 20x20 |
|---|---|---|
| `{w}` `{l}` | The two dimensions in feet | `20` `20` |
| `{area}` | Square feet | `400` |
| `{yards4}` `{yards5}` `{yards6}` | Cubic yards at 4, 5 and 6 inches | `4.94` `6.17` `7.41` |
| `{bags80_4}` `{bags60_4}` | Bags at 4 inches, rounded up, no waste added | `223` `297` |
| `{bags80_6}` `{bags60_6}` | Bags at 6 inches | `334` `445` |
| `{trucks}` | Ready-mix truck loads at 10 cubic yards | `1` |
| `{rebar}` | Linear feet of rebar on a 16 inch grid | `640` |
| `{depth_table}` | Computed table: depth, cubic yards, 80 lb bags, 60 lb bags | |

Branch ideas that carry real advice: under about 25 bags you can mix by hand;
over roughly 3 cubic yards ready-mix is cheaper and far less work; over 10 cubic
yards you are into more than one truck and need to think about pour sequence; a
slab over 20 feet in either direction needs a control joint discussion.

---

### P02 pavers, 171 pages

`/pavers/{w}x{l}-patio/`, same dimension pairs as P01.

| Slot | What it is | Example at 12x12 |
|---|---|---|
| `{w}` `{l}` `{area}` | Dimensions and square feet | `12` `12` `144` |
| `{pavers_4x8}` `{pavers_6x6}` `{pavers_6x9}` `{pavers_8x8}` `{pavers_12x12}` `{pavers_16x16}` | Count for each common paver size, before waste | `648` `576` `384` `324` `144` `81` |
| `{base_yards}` | Cubic yards of base gravel at 4 inches | `1.78` |
| `{sand_yards}` | Cubic yards of bedding sand at 1 inch | `0.44` |
| `{edging}` | Linear feet of edging | `48` |
| `{paver_table}` | Computed table: paver size, count, pallets | |

Branch ideas: a patio under about 100 square feet is a weekend job for one
person; over about 400 square feet the base preparation becomes the real work
and a plate compactor hire pays for itself; a run longer than 20 feet needs a
slope discussion for drainage.

---

### P03 mulch by area, 19 pages

`/mulch/{area}-square-feet/`, where area is 50, 100, 150, 200, 250, 300, 400,
500, 600, 750, 800, 1000, 1200, 1500, 2000, 2500, 3000, 4000 or 5000.

| Slot | What it is | Example at 200 |
|---|---|---|
| `{area}` | Square feet | `200` |
| `{yards2}` `{yards3}` `{yards4}` | Cubic yards at 2, 3 and 4 inches | `1.23` `1.85` `2.47` |
| `{bags2}` `{bags3}` `{bags4}` | Two cubic foot bags at each depth | `17` `25` `34` |
| `{depth_table}` | Computed table: depth, cubic yards, bags | |

Branch ideas: under about 25 bags, bagged mulch from a garden centre is easier;
over roughly 3 cubic yards, bulk delivery is dramatically cheaper per yard and
the arithmetic is worth showing; above 2,000 square feet you are ordering by the
truckload and should talk about where it gets tipped.

---

### P20 party food and drink, 740 pages

`/party/{food}-for-{n}-people/`, for example `/party/pulled-pork-for-50-people/`.

Thirty-seven foods and drinks crossed with twenty guest counts from 10 to 300.
This is the most underserved set in the whole plan: the live results for "how
much pulled pork for 50 people" are six forum threads out of eight, which means
nobody has written a decent page for it.

| Slot | What it is | Example |
|---|---|---|
| `{food}` | The food, in lower case | `pulled pork` |
| `{Food}` | Capitalised for headings | `Pulled pork` |
| `{n}` | Guest count | `50` |
| `{total}` | Total cooked quantity with unit | `16.7 lb` |
| `{raw}` | Raw weight needed where cooking loses weight | `27.8 lb` |
| `{per_person}` | The serving basis | `5.3 oz cooked` |
| `{shrink}` | Cooking loss as a percentage, where it applies | `40` |
| `{unit}` | The unit the food is bought in | `pounds` |

There is deliberately no cost slot. Food prices vary by region and by month and
we have no price data we could stand behind, so cost lives in the interactive
calculator where the reader enters their own price per pound.

Because one template covers thirty-seven very different foods, the `when`
conditions on `food` are how this stays honest. Pulled pork needs a cooking
shrinkage section that makes no sense for bottled beer, and wine needs a
bottles-per-guest section that makes no sense for brisket. Group the foods:
smoked meats, grilled meats, sides, desserts, soft drinks, beer, wine, spirits.
Write a section per group with `when` conditions naming the foods in it.

Guest count branches matter too. Under 25 people one oven and one cook manages
it; over 100 you are hiring or borrowing equipment and the page should say what;
over 200 the advice is genuinely about logistics rather than quantities.

The workbook asks specifically that Nigerian party foods are included in this
set, and jollof rice, suya, small chops, moi moi, puff puff and pepper soup all
have real search demand and essentially no decent English-language quantity
pages. Treat them as first-class members of the food list rather than an
afterthought, and get the serving norms from Nigerian catering practice rather
than by scaling an American number.

---

### P16 walking time, 60 pages

`/walk/{miles}-miles/`, half a mile to 30 miles in half mile steps.

| Slot | What it is | Example at 6 |
|---|---|---|
| `{miles}` `{km}` | Distance both ways | `6` `9.7` |
| `{slow}` `{average}` `{brisk}` | Walking time at 2.5, 3.1 and 4 mph | `2h 24m` `1h 56m` `1h 30m` |
| `{steps_short}` `{steps_tall}` | Step count at a short and a tall stride | `15,840` `11,879` |
| `{cal_low}` `{cal_high}` | Calorie range by body weight | `380` `610` |
| `{pace_table}` | Computed table: pace, speed, time | |

Branches: under two miles this is a walk rather than an outing; past five miles
water and footwear start to matter; past ten miles it is a hike and needs food
and a plan; past a marathon distance the advice is training rather than walking.

---

### P17 steps to miles, 50 pages

`/steps/{n}-steps-to-miles/`, 1,000 to 50,000 in thousands.

| Slot | What it is | Example at 10000 |
|---|---|---|
| `{n}` | Steps | `10000` |
| `{miles}` `{km}` | At an average stride | `4.7` `7.6` |
| `{miles_short}` `{miles_tall}` | At a 5'0" and a 6'2" stride | `3.9` `5.4` |
| `{minutes}` | Walking time at an average pace | `92` |
| `{cal_low}` `{cal_high}` | Calorie range | `300` `500` |
| `{height_table}` | Computed table: height, stride, miles | |

The live results here are mixed rather than open, so this page has to be better
than the ones ranking rather than merely present. The thing they all get wrong
is treating stride as a constant, so lead with the height table and make the
point that the same 10,000 steps is 3.9 miles for one person and 5.4 for
another.

---

### P18 running pace, 121 pages

`/pace/{mm-ss}-per-mile/`, from 5:00 to 15:00 per mile in five second steps.

| Slot | What it is | Example at 8:00 |
|---|---|---|
| `{pace}` | Pace per mile | `8:00` |
| `{pace_km}` | Pace per kilometre | `4:58` |
| `{mile}` `{k5}` `{k10}` `{half}` `{full}` | Finish times at that pace | `8:00` `24:51` `49:43` `1:44:53` `3:29:45` |
| `{mph}` | Speed | `7.5` |
| `{race_table}` | Computed table: distance, finish time, splits | |

Branches: a pace under about 6:00 is competitive and the page should talk about
what sustaining it takes; between 8:00 and 10:00 is where most recreational
runners live and the useful content is about holding it; over 12:00 the honest
framing is run-walk, which is a legitimate strategy and is usually written about
condescendingly.

---

## What comes after

Nine more templates are specified once their calculators exist: salary to
hourly, appliance amps, appliance running cost, wire gauge, generator sizing,
room lumens, rug size, tent capacity and plants per square foot. The date and
age templates, which are the largest single set at 2,107 pages, are on hold
until somebody checks whether Google answers those queries directly, because if
it does the whole set is worth far less than its page count suggests.
