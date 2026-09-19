# Job one: rewriting the copy under 95 existing calculators

Each calculator on calculatorr.org has an interactive tool at the top and
explanatory copy beneath it. The tool is finished and is not your concern. The
copy beneath is mostly 250 to 400 words of filler that answers nothing, and
replacing it is the job.

Read `00-how-this-works.md` first, because the rules there are not repeated here.

## What good looks like

The calibration page is the concrete calculator, live at
`https://calculatorr.org/construction-calculators-online/concrete-calculator/`.
It runs to 1,192 words with reference tables, and every section answers a
question somebody would actually ask after getting a number. Read it before you
write anything, because a description of a standard is never as clear as the
thing itself.

Two more written to the same standard, so you can see the range: the BMR
calculator under Health, and the auto loan calculator under Loans. The auto loan
page is the better model for anything with money in it, and the BMR page for
anything where the formula is contested.

## Length

Nine hundred to fifteen hundred words of body copy, and go to three thousand
when the subject genuinely carries it. A mortgage has more to say than a
millimetre conversion, and padding the conversion to match produces exactly the
filler this exercise is removing.

Length is not the target. It is what happens when you answer the real questions.
If you reach 1,400 words and the next paragraph would still be answering
something a reader would plausibly ask, keep going. If you reach 700 and the next
paragraph would restate the third one in different words, stop and cut the third
one instead.

## Shape

Five to eight sections. Fewer than five is almost certainly thin, and more than
eight means the headings have stopped being signposts and become a list.

Write the headings as the questions people ask, because that is how they arrive
and it is what makes a heading scannable. "How much area does a cubic yard of
concrete cover?" earns its place. "Coverage" does not.

Order the sections the way curiosity runs. How the number is worked out comes
first, because somebody who has just been handed a figure wants to know where it
came from. What to do with it comes next. What would change it comes after that.
What the calculator deliberately does not cover comes last, and it is the
section most pages skip and most readers need.

At least two sections must carry a reference table, because tables are what make
these pages worth linking to and they are the part competitors leave out. A table
of the same calculation across a range of inputs is almost always the right one:
the loan at five terms, the conversion at ten common values, the material at six
room sizes.

## The file you send

One file per calculator, named `<slug>.json`, where the slug is the one in the
queue below. The shape is exactly this and nothing else:

```json
{
  "explainer": [
    {
      "heading": "How the number is worked out",
      "body": "Two or three paragraphs, separated by \n\n.",
      "formula": "Optional. Simple HTML allowed, for example x &divide; y<sup>2</sup>",
      "example": "Optional. One worked example with real numbers.",
      "steps": ["Optional.", "An ordered list.", "Each item a full sentence."],
      "table": {
        "caption": "Optional but wanted on at least two sections",
        "head": ["Column", "Headings"],
        "rows": [["cell", "cell"], ["cell", "cell"]]
      }
    }
  ],
  "faqs": [
    { "q": "A question somebody actually types", "a": "Two to four sentences answering it properly." }
  ]
}
```

`heading` and `body` are required on every section. Everything else is optional,
and a section using all four extras is usually a section that should have been
two.

Every row in a table must have exactly as many cells as the header, because a
short row renders as a table with a hole in it and a long one silently loses its
last cell. The publisher refuses a ragged table and names the row, so this is
worth checking before you send.

Four to six questions. Write the ones people type, not the ones that make the
page look complete, and answer each properly rather than in one line. A question
worth asking is worth three sentences.

Do not include any other top-level key. The publisher refuses unknown keys
rather than dropping them silently, because a dropped key means somebody wrote
something that went nowhere and never found out.

## The queue

Ordered thinnest page first inside each demand tier. The word counts are what
the page renders today, including its tables and headings, so a page at 241 has
almost nothing on it.

Demand tiers are judgment rather than measurement. The Ahrefs plan on this
account returns no volume data, and the site is too new for its own usage
counter to say anything, so the ordering is a reasoned guess about which head
terms matter most. If you have volume data of your own, reorder freely and tell
us, because a real number beats our judgment.

### Day 1

| Slug | Category | Words now |
|---|---|---|
| `square-root-calculator` | Math | 241 |
| `area-calculator` | Geometry | 272 |
| `volume-calculator` | Geometry | 272 |
| `date-calculator` | Time | 276 |
| `hours-calculator` | Time | 292 |

### Day 2

| Slug | Category | Words now |
|---|---|---|
| `average-calculator` | Math | 309 |
| `amortization-calculator` | Loans | 311 |
| `time-card-calculator` | Time | 312 |
| `age-calculator` | Time | 358 |
| `tip-calculator` | Business | 364 |

### Day 3

| Slug | Category | Words now |
|---|---|---|
| `discount-calculator` | Business | 371 |
| `square-footage-calculator` | Construction | 376 |
| `sales-tax-calculator` | Business | 405 |
| `gpa-calculator` | Education | 418 |
| `take-home-pay-calculator` | Finance | 423 |

### Day 4

| Slug | Category | Words now |
|---|---|---|
| `compound-interest-calculator` | Finance | 475 |
| `bmi-calculator` | Health | 501 |
| `mortgage-payment-calculator` | Loans | 534 |
| `percentage-calculator` | Math | 536 |
| `concrete-calculator` | Construction | 1192 |

### Day 5

| Slug | Category | Words now |
|---|---|---|
| `decimal-to-fraction-calculator` | Math | 223 |
| `percentage-decrease-calculator` | Math | 226 |
| `long-division-calculator` | Math | 235 |
| `lcm-calculator` | Math | 239 |
| `unit-converter` | Conversion | 240 |

### Day 6

| Slug | Category | Words now |
|---|---|---|
| `proportion-calculator` | Math | 245 |
| `triangle-calculator` | Geometry | 246 |
| `right-triangle-calculator` | Geometry | 249 |
| `weight-converter` | Conversion | 250 |
| `military-time-converter` | Time | 258 |

### Day 7

| Slug | Category | Words now |
|---|---|---|
| `ratio-calculator` | Math | 258 |
| `time-duration-calculator` | Time | 259 |
| `gcf-calculator` | Math | 260 |
| `slope-calculator` | Math | 261 |
| `business-days-calculator` | Time | 266 |

### Day 8

| Slug | Category | Words now |
|---|---|---|
| `time-calculator` | Time | 266 |
| `cubic-feet-calculator` | Geometry | 267 |
| `cylinder-volume-calculator` | Geometry | 268 |
| `markup-calculator` | Business | 270 |
| `distance-calculator` | Geometry | 272 |

### Day 9

| Slug | Category | Words now |
|---|---|---|
| `work-hours-calculator` | Time | 274 |
| `cpm-calculator` | Business | 278 |
| `pythagorean-theorem-calculator` | Geometry | 279 |
| `quadratic-formula-calculator` | Math | 280 |
| `cumulative-gpa-calculator` | Education | 288 |

### Day 10

| Slug | Category | Words now |
|---|---|---|
| `steps-to-miles-calculator` | Health | 290 |
| `vat-calculator` | Business | 291 |
| `overtime-calculator` | Time | 292 |
| `percentage-increase-calculator` | Math | 303 |
| `future-value-calculator` | Finance | 309 |

### Day 11

| Slug | Category | Words now |
|---|---|---|
| `pay-raise-calculator` | Finance | 315 |
| `mortgage-payoff-calculator` | Loans | 318 |
| `ebay-fee-calculator` | Business | 319 |
| `water-intake-calculator` | Health | 320 |
| `roth-ira-calculator` | Finance | 321 |

### Day 12

| Slug | Category | Words now |
|---|---|---|
| `one-rep-max-calculator` | Health | 322 |
| `macro-calculator` | Health | 323 |
| `personal-loan-calculator` | Loans | 324 |
| `body-fat-calculator` | Health | 326 |
| `standard-deviation-calculator` | Math | 330 |

### Day 13

| Slug | Category | Words now |
|---|---|---|
| `pregnancy-calculator` | Health | 339 |
| `cubic-yard-calculator` | Construction | 344 |
| `401k-calculator` | Finance | 362 |
| `heloc-calculator` | Loans | 363 |
| `ovulation-calculator` | Health | 367 |

### Day 14

| Slug | Category | Words now |
|---|---|---|
| `mulch-calculator` | Construction | 389 |
| `gravel-calculator` | Construction | 408 |
| `tile-calculator` | Construction | 424 |
| `paint-calculator` | Construction | 452 |
| `stair-calculator` | Construction | 468 |

### Day 15

| Slug | Category | Words now |
|---|---|---|
| `deck-calculator` | Construction | 493 |
| `tdee-calculator` | Health | 565 |
| `system-of-equations-calculator` | Math | 243 |
| `roman-numeral-converter` | Math | 254 |
| `love-calculator` | Math | 269 |

### Day 16

| Slug | Category | Words now |
|---|---|---|
| `factor-calculator` | Math | 273 |
| `tire-size-calculator` | Conversion | 276 |
| `boat-loan-calculator` | Loans | 277 |
| `chronological-age-calculator` | Time | 287 |
| `ap-score-calculator` | Education | 290 |

### Day 17

| Slug | Category | Words now |
|---|---|---|
| `dog-age-calculator` | Time | 290 |
| `derivative-calculator` | Math | 292 |
| `dividend-calculator` | Finance | 296 |
| `cd-calculator` | Finance | 297 |
| `molarity-calculator` | Education | 305 |

### Day 18

| Slug | Category | Words now |
|---|---|---|
| `body-surface-area-calculator` | Health | 307 |
| `integral-calculator` | Math | 307 |
| `bra-size-calculator` | Health | 310 |
| `period-calculator` | Health | 326 |
| `military-pay-calculator` | Finance | 334 |

### Day 19

| Slug | Category | Words now |
|---|---|---|
| `pool-volume-calculator` | Construction | 379 |
| `bac-calculator` | Health | 386 |
| `board-foot-calculator` | Construction | 395 |
| `topsoil-calculator` | Construction | 398 |
| `voltage-drop-calculator` | Construction | 501 |

