# calculatorr.com: The 100-Calculator Roadmap

This is the build inventory for calculatorr.com, sorted into the ten use-case
categories the design was built around. Every entry comes from the supplied
keyword set rather than from guesswork, which is the important difference from
the first version of this document: the earlier hundred were my judgment, and
these hundred are demand.

The assignment below was generated and checked programmatically, so all one
hundred keywords appear exactly once, none were dropped and none were invented.
Where a keyword did not fit the design, it is flagged rather than forced.


## URL structure

Every calculator sits under its use-case folder, so the category is visible in
the URL itself and the topical signal reaches Google before the page is even
crawled:

```
calculatorr.com/<category>/<calculator-slug>/
```

Each category slug also resolves to a hub page listing everything in it, since
those hubs are what pass authority down to the individual tools and what new
backlinks should point at.


## How the hundred actually distribute

The design assumed ten categories of ten. The real keyword set does not
distribute that way, and pretending otherwise would produce one hub page with
twenty-one entries and another with four.

| Category | Pages | URL |
|---|---:|---|
| Math & Numbers | 21 | `/math/` |
| Home & DIY | 13 | `/home-diy/` |
| Health & Body | 11 | `/health/` |
| Date, Time & Work | 11 | `/time/` |
| Business & Shopping | 10 | `/business/` |
| Geometry & Shapes | 9 | `/geometry/` |
| Finance & Retirement | 8 | `/finance/` |
| Unit Conversion | 7 | `/convert/` |
| Loans & Debt | 5 | `/loans/` |
| School & Study | 4 | `/education/` |
| No natural home | 1 | not applicable |

The imbalance is worth acting on rather than living with, and the single change
that fixes most of it is splitting the maths group, which is explained in that
section below.


---

## Math & Numbers  `/math/`

By far the largest cluster, and the one that most changes the plan. Twenty-one of your hundred are maths, which is more than the homepage grid was designed to show in a single tile and more than one hub page can list without becoming a wall. These split cleanly along intent into everyday arithmetic on one side and algebra, calculus and statistics on the other, which is the argument for making them two hubs rather than one.

| # | Calculator | URL |
|---:|---|---|
| 1 | Percentage Calculator | `/math/percentage-calculator/` |
| 2 | Percentage Increase Calculator | `/math/percentage-increase-calculator/` |
| 3 | Percentage Decrease Calculator | `/math/percentage-decrease-calculator/` |
| 4 | Percent Change Calculator | `/math/percent-change-calculator/` |
| 5 | Fraction Calculator | `/math/fraction-calculator/` |
| 6 | Decimal to Fraction Calculator | `/math/decimal-to-fraction-calculator/` |
| 7 | Ratio Calculator | `/math/ratio-calculator/` |
| 8 | Proportion Calculator | `/math/proportion-calculator/` |
| 9 | Average Calculator | `/math/average-calculator/` |
| 10 | Long Division Calculator | `/math/long-division-calculator/` |
| 11 | Square Root Calculator | `/math/square-root-calculator/` |
| 12 | Roman Numeral Converter | `/math/roman-numeral-converter/` |
| 13 | Quadratic Formula Calculator | `/math/quadratic-formula-calculator/` |
| 14 | Derivative Calculator | `/math/derivative-calculator/` |
| 15 | Integral Calculator | `/math/integral-calculator/` |
| 16 | System of Equations Calculator | `/math/system-of-equations-calculator/` |
| 17 | Slope Calculator | `/math/slope-calculator/` |
| 18 | Standard Deviation Calculator | `/math/standard-deviation-calculator/` |
| 19 | GCF Calculator | `/math/gcf-calculator/` |
| 20 | LCM Calculator | `/math/lcm-calculator/` |
| 21 | Factor Calculator | `/math/factor-calculator/` |

**Recommended split.** Twelve of these are everyday arithmetic (percentage, fraction, ratio, proportion, average, long division, square root, roman numerals and the percentage variants) and nine are algebra, calculus and statistics (quadratic, derivative, integral, systems, slope, standard deviation, GCF, LCM, factor). Splitting them into `/math/` and `/algebra/` gives two hubs of twelve and nine instead of one of twenty-one, and separates a school-age audience from a general one.

---

## Home & DIY  `/home-diy/`

Materials estimating, and commercially the most underrated group here. Someone working out how much concrete to order is minutes away from ordering concrete, which is why these pages monetise better than their search volume suggests. They also share a single input pattern, since almost all of them are length times width times depth with a wastage allowance on top, so building the second one costs a fraction of the first.

| # | Calculator | URL |
|---:|---|---|
| 1 | Concrete Calculator | `/home-diy/concrete-calculator/` |
| 2 | Square Footage Calculator | `/home-diy/square-footage-calculator/` |
| 3 | Gravel Calculator | `/home-diy/gravel-calculator/` |
| 4 | Stair Calculator | `/home-diy/stair-calculator/` |
| 5 | Board Foot Calculator | `/home-diy/board-foot-calculator/` |
| 6 | Mulch Calculator | `/home-diy/mulch-calculator/` |
| 7 | Cubic Yard Calculator | `/home-diy/cubic-yard-calculator/` |
| 8 | Pool Volume Calculator | `/home-diy/pool-volume-calculator/` |
| 9 | Topsoil Calculator | `/home-diy/topsoil-calculator/` |
| 10 | Deck Calculator | `/home-diy/deck-calculator/` |
| 11 | Tile Calculator | `/home-diy/tile-calculator/` |
| 12 | Paint Calculator | `/home-diy/paint-calculator/` |
| 13 | Voltage Drop Calculator | `/home-diy/voltage-drop-calculator/` |

---

## Health & Body  `/health/`

Body composition, fertility and training, with fertility unusually well represented in your list. Every page in this group needs the medical disclaimer that is already built into the plugin, and the fertility calculators need it most, because a cycle prediction presented with too much confidence is the kind of error people make real decisions on.

| # | Calculator | URL |
|---:|---|---|
| 1 | Ovulation Calculator | `/health/ovulation-calculator/` |
| 2 | Pregnancy Calculator | `/health/pregnancy-calculator/` |
| 3 | Period Calculator | `/health/period-calculator/` |
| 4 | BAC Calculator | `/health/bac-calculator/` |
| 5 | Steps to Miles Calculator | `/health/steps-to-miles-calculator/` |
| 6 | One Rep Max Calculator | `/health/one-rep-max-calculator/` |
| 7 | Bra Size Calculator | `/health/bra-size-calculator/` |
| 8 | Water Intake Calculator | `/health/water-intake-calculator/` |
| 9 | Body Fat Calculator | `/health/body-fat-calculator/` |
| 10 | Macro Calculator | `/health/macro-calculator/` |
| 11 | Body Surface Area Calculator | `/health/body-surface-area-calculator/` |

---

## Date, Time & Work  `/time/`

Two distinct jobs sit in this group and it is worth knowing which is which. Dates and durations are ordinary utility, while time cards, work hours and overtime are payroll tools whose visitors are employees checking their own pay. The second set is more commercially valuable and deserves the better internal linking.

| # | Calculator | URL |
|---:|---|---|
| 1 | Time Calculator | `/time/time-calculator/` |
| 2 | Date Calculator | `/time/date-calculator/` |
| 3 | Hours Calculator | `/time/hours-calculator/` |
| 4 | Time Duration Calculator | `/time/time-duration-calculator/` |
| 5 | Military Time Converter | `/time/military-time-converter/` |
| 6 | Chronological Age Calculator | `/time/chronological-age-calculator/` |
| 7 | Dog Age Calculator | `/time/dog-age-calculator/` |
| 8 | Business Days Calculator | `/time/business-days-calculator/` |
| 9 | Time Card Calculator | `/time/time-card-calculator/` |
| 10 | Work Hours Calculator | `/time/work-hours-calculator/` |
| 11 | Overtime Calculator | `/time/overtime-calculator/` |

---

## Business & Shopping  `/business/`

Retail and seller maths, mixing shopper-facing tools like tip and percent off with seller-facing ones like margin, eBay fees and CPM. The seller tools carry much higher advertising rates despite lower volume, so they earn their place even though tip will out-traffic all of them combined.

| # | Calculator | URL |
|---:|---|---|
| 1 | Tip Calculator | `/business/tip-calculator/` |
| 2 | Discount Calculator | `/business/discount-calculator/` |
| 3 | Percent Off Calculator | `/business/percent-off-calculator/` |
| 4 | Markup Calculator | `/business/markup-calculator/` |
| 5 | Margin Calculator | `/business/margin-calculator/` |
| 6 | Sales Tax Calculator | `/business/sales-tax-calculator/` |
| 7 | VAT Calculator | `/business/vat-calculator/` |
| 8 | GST Calculator | `/business/gst-calculator/` |
| 9 | eBay Fee Calculator | `/business/ebay-fee-calculator/` |
| 10 | CPM Calculator | `/business/cpm-calculator/` |

---

## Geometry & Shapes  `/geometry/`

Area, volume and triangles, sitting close enough to the maths group that the two need careful internal linking to avoid competing for the same queries. One of these needs a decision before it is built, which is noted below.

| # | Calculator | URL |
|---:|---|---|
| 1 | Area Calculator | `/geometry/area-calculator/` |
| 2 | Volume Calculator | `/geometry/volume-calculator/` |
| 3 | Cubic Feet Calculator | `/geometry/cubic-feet-calculator/` |
| 4 | Cylinder Volume Calculator | `/geometry/cylinder-volume-calculator/` |
| 5 | Circumference Calculator | `/geometry/circumference-calculator/` |
| 6 | Triangle Calculator | `/geometry/triangle-calculator/` |
| 7 | Right Triangle Calculator | `/geometry/right-triangle-calculator/` |
| 8 | Pythagorean Theorem Calculator | `/geometry/pythagorean-theorem-calculator/` |
| 9 | Distance Calculator | `/geometry/distance-calculator/` |

**Decision needed on `distance-calculator`.** The keyword is genuinely ambiguous: it can mean the distance between two coordinates, which is geometry, or the driving distance between two places, which is travel. The two need different tools and rank for different queries. Check the live results before building it, because guessing wrong here means building the wrong calculator entirely.

---

## Finance & Retirement  `/finance/`

Pay and retirement rather than general money management, which is a narrower group than the original design assumed. Military pay is the outlier worth noticing, since it is a niche with real volume and almost no competition from the big calculator sites.

| # | Calculator | URL |
|---:|---|---|
| 1 | Take Home Pay Calculator | `/finance/take-home-pay-calculator/` |
| 2 | Pay Raise Calculator | `/finance/pay-raise-calculator/` |
| 3 | Military Pay Calculator | `/finance/military-pay-calculator/` |
| 4 | 401(k) Calculator | `/finance/401k-calculator/` |
| 5 | Roth IRA Calculator | `/finance/roth-ira-calculator/` |
| 6 | CD Calculator | `/finance/cd-calculator/` |
| 7 | Dividend Calculator | `/finance/dividend-calculator/` |
| 8 | Future Value Calculator | `/finance/future-value-calculator/` |

---

## Unit Conversion  `/convert/`

Straightforward unit work, and the group with the most long-tail leverage on the site. A single well-built converter can capture hundreds of keywords if the common unit pairs are generated as their own pages, so the seven here are better understood as seven templates than as seven pages.

| # | Calculator | URL |
|---:|---|---|
| 1 | Celsius to Fahrenheit Calculator | `/convert/celsius-to-fahrenheit-calculator/` |
| 2 | Weight Converter | `/convert/weight-converter/` |
| 3 | Unit Converter | `/convert/unit-converter/` |
| 4 | mm to Inches Calculator | `/convert/mm-to-inches-calculator/` |
| 5 | Inches to Feet Calculator | `/convert/inches-to-feet-calculator/` |
| 6 | Feet to Meters Calculator | `/convert/feet-to-meters-calculator/` |
| 7 | Tire Size Calculator | `/convert/tire-size-calculator/` |

---

## Loans & Debt  `/loans/`

Smaller than the original design assumed, because your list leans toward payoff and amortisation rather than toward shopping for a loan. That is a better position to be in, since payoff intent means the visitor already has the debt and is looking for a way out, which is a motivated reader.

| # | Calculator | URL |
|---:|---|---|
| 1 | Amortization Calculator | `/loans/amortization-calculator/` |
| 2 | Mortgage Payoff Calculator | `/loans/mortgage-payoff-calculator/` |
| 3 | Personal Loan Calculator | `/loans/personal-loan-calculator/` |
| 4 | HELOC Calculator | `/loans/heloc-calculator/` |
| 5 | Boat Loan Calculator | `/loans/boat-loan-calculator/` |

---

## School & Study  `/education/`

The thinnest group at four pages, and one of the four does not really belong. Molarity is chemistry coursework rather than grading, and it sits here only because school is the context both share.

| # | Calculator | URL |
|---:|---|---|
| 1 | GPA Calculator | `/education/gpa-calculator/` |
| 2 | Cumulative GPA Calculator | `/education/cumulative-gpa-calculator/` |
| 3 | AP Score Calculator | `/education/ap-score-calculator/` |
| 4 | Molarity Calculator | `/education/molarity-calculator/` |

Molarity is placed here for want of anywhere better. If more science keywords arrive in a later batch, pull it out into its own group rather than leaving chemistry filed under grades.

---

## No natural home  

One keyword has no home in the current design, and forcing it into a category would make that category worse.

| # | Calculator | URL |
|---:|---|---|
| 1 | Love Calculator | not assigned |

`love-calculator` is a novelty tool with no relationship to any other page on the site. There are two honest options. Either drop it, or open a Fun category and commit to filling it, since novelty calculators do rank and do attract links but look out of place as a single orphan next to mortgage and body fat. Placing it inside an existing category would weaken that category's topical signal for no gain.


---

## What is already built, and how it overlaps

Ten calculators were built before this list arrived. Five of them are on it
exactly: percentage, tip, discount, GPA and sales tax. Two more are the same
tool under a different name, since the age calculator answers
`chronological-age-calculator` and the compound interest calculator answers
`future-value-calculator`, so both should be re-slugged to match the keyword
rather than rebuilt.

Three are not on your list at all: BMI, mortgage payment and TDEE. They are
genuinely high volume, so the recommendation is to keep them and treat the site
as a hundred and three rather than throw away working pages. If you would rather
hold the line at a hundred, they are the three to drop.

That leaves ninety-three still to build.

