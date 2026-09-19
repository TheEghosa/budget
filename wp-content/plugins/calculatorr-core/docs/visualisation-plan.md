# Going after drawings

Written 19 September 2026, after fourteen SERP checks turned up roughly seventy
programmatic calculator sites competing for the same queries this site was about
to chase. This document is the answer to the question that research left open,
which is what calculatorr.org can do that those seventy cannot.

## Why a drawing, and not just a better number

Four reasons, and the first one matters more than the other three together.

**A drawing cannot be absorbed.** Every query I tested came back with a complete
answer in the search summary before I opened a single page. Google can quote
"4.94 cubic yards" and it can quote "1,649 kcal a day", because those are facts
that fit in a sentence. It cannot render your scale drawing of a 4 by 8 bed with
thirty-two tomato plants in it, or the herringbone pattern that explains why
that patio needs fourteen per cent more pavers than a running bond. Everything
else this site publishes is absorbable. A drawing is the one page type where
the answer has to stay on the page, and that is a structural advantage rather
than a clever one.

**Image search and Pinterest are not saturated the way web search is.** Those
seventy sites are all competing for the same ten blue links. Almost none of them
produce a genuinely useful, unique image per page, so a second index surface is
sitting there largely unclaimed. This matters more than usual here, because
Pinterest is where people plan patios, gardens, nurseries and weddings, and
those are exactly the queries where a drawing is the answer.

**People link to drawings.** Nobody has ever linked to a page because it told
them 4.94 cubic yards. They link to the tool that drew their patio, because
sending someone the link is easier than explaining it. On a domain with no
history, links are the constraint, and this is the only page type on the site
that plausibly earns them.

**It is the difference between a tool and a splinter site.** The seventy farms
are, almost without exception, ugly. You already have a real design system, and
a drawing is where that stops being decoration and starts being the product.

## The filter: the screenshot test

Before building any visualiser, apply this and be strict about it.

**Could you screenshot the drawing alone, with no page around it, send it to
someone, and would it answer their question?**

If yes, build it. If no, the drawing is decoration and you are better off
spending the day on something else. Most calculators fail this test and that is
fine, because it is a test of the problem rather than of the calculator. BMI
fails it. A loan payment fails it. A unit conversion fails it. The number is the
answer in all three, and drawing a bar next to it adds nothing a reader needs.

What passes are the problems where the question is really about space, shape,
fit, pattern or sequence. Where does the rug go. What does herringbone look like
at this size. How many tomato plants fit in a four by eight bed and where do
they go. Will the sofa turn that corner. What does a six in twelve roof pitch
actually look like from the side.

This filter is what stops the drawing programme becoming the thing it is
supposed to replace, which is a large number of pages that all look the same.

## The standard

Seven rules. Every one of them exists because the alternative is what the
competition already ships.

**True scale, always.** If the drawing says twelve feet, the shape is twelve
feet at whatever the drawing's scale is. Competitors routinely draw a rug and a
bed at whatever sizes looked balanced, which makes the picture worse than
useless because it looks authoritative and is not.

**Dimensioned on the drawing itself.** Arrows and numbers on the drawing, not
in a caption underneath it, because the screenshot test only passes if the
numbers travel with the picture.

**Legible at 320 pixels wide and at A4 print.** Those are the two sizes that
actually get used, one on a phone in a garden centre and one printed and taken
to the garage. A drawing that only works at desktop width has failed both.

**Correct in both themes.** The site has a dark mode and the drawing has to
follow it, which means colours come from the design tokens rather than being
written into the SVG.

**A text equivalent.** Every drawing carries a `<title>` and a `<desc>`, and the
page carries the same information in prose somewhere. That is an accessibility
requirement first, and it is also what lets a crawler understand a picture it
cannot read.

**A caption that is a complete sentence.** "4 by 8 raised bed, thirty-two
tomato plants at one per square foot" rather than "Fig. 1".

**Deterministic.** The same inputs produce byte-identical SVG every time, so
drawings can be cached, diffed in review, and regenerated without churn.

## The engine

One PHP class, `Calculatorr_Draw`, holding the primitives every drawing needs:
a units-to-pixels transform set once per drawing, scaled rectangles and circles,
dimension lines with arrowheads and labels, a repeating grid, hatching, text
placement that avoids collisions, and a legend. Everything else is composition.

The drawing is rendered server side into the HTML, for the same reason the
calculators already server-render their answer: it has to be in the source for a
crawler, and it has to be there before JavaScript for a reader. SVG suits that
perfectly, since it is text, it scales, it themes through CSS variables, and it
prints at full resolution.

Three outputs per page, from one drawing:

| Output | Size | What it is for |
|---|---|---|
| Inline SVG | responsive | The page itself, themed, printable, accessible |
| Social PNG | 1200 x 630 | Open Graph, link previews |
| Pinterest PNG | 1000 x 1500 | Pinterest, which is the point |

The two PNGs are rendered at build time with the headless Chromium already in
the toolchain, so each page has a real image URL rather than something generated
in the browser. That is what makes them indexable, which is the entire reason
for producing them.

Each PNG carries the site's mark and the page URL in a corner, because these
images will be screenshotted and reposted without attribution otherwise, and the
watermark is the only part of the traffic loop you control.

## What to build, ranked

Ranked on the screenshot test first, then on how badly the incumbents do it,
then on whether the audience is somewhere a picture travels.

**1. Square foot garden planting grids.** A bed size crossed with a crop gives a
printable grid showing exactly how many plants and where they go. Forty crops by
eight bed sizes is 320 pages, the drawing is unambiguously the answer, and
garden planning is one of Pinterest's largest categories. Demand is seasonal,
which is an argument for building it now rather than in March.

**2. Rug size under a bed or table.** An overhead drawing to scale showing the
rug, the furniture on it, and the walking clearance around it, with the common
rug sizes drawn as alternatives. This is a small cluster and an extremely high
quality one, because the question is genuinely visual and every page currently
ranking for it is a retailer describing it in words.

**3. Paver and tile pattern layouts.** Running bond, herringbone at 45 and 90,
basketweave, stack bond, each drawn at the reader's actual patio size with the
count and the waste factor that pattern demands. The waste difference between
patterns is real, material, and almost never explained, and it cannot be
explained without a picture.

**4. Event and marquee floor plans.** Tables and chairs laid out in a given
space, with the aisles and the dance floor. Wedding planning is Pinterest's
heartland and the incumbents are event rental companies with text tables.

**5. Scale comparison.** How big is a thousand square feet, drawn against a
tennis court, a parking space, a double garage and a typical bedroom. Small
cluster, unusually linkable, and the sort of thing that gets reposted.

Below those, worth building once the first five are proven: roof pitch and stair
elevations, wall framing layouts, deck board plans, and cut and nesting layouts.
Those are all strong on the screenshot test and weaker on distribution, because
a builder is less likely to pin something than a person planning a garden.

## Distribution, which matters more than SEO for the first six months

The domain is days old. It will not outrank a two year old farm on quality
alone, and pretending otherwise is how six months gets wasted. So the drawings
have to reach people through channels where quality wins immediately.

Pinterest is the main one, and it is why the 1000 by 1500 render is not
optional. Every visualiser page produces a pin, and you already have a pin copy
process that can take it from there.

Reddit is the second, and it works exactly once per community if the tool is
genuinely good and the post is honest about who built it. r/DIY, r/gardening,
r/woodworking, r/landscaping and the wedding planning subreddits each have a
real appetite for free tools and a very short fuse for marketing.

Image search is the third, and it is passive. It follows from the PNGs existing,
having sensible filenames, and sitting in an image sitemap.

Search is the fourth and the slowest, and it compounds. Treat it as the thing
that pays off in month nine rather than the thing that justifies month one.

## Cadence, and an honest word about it

A good drawing is not a template fill. It is design work, and the first version
of each new drawing type takes a day or two to get right, after which every page
in that cluster is nearly free because the generator does the work.

So the shape is roughly one new visualiser type per week, with its cluster
behind it, rather than a page count per day. Ten of them over ten weeks, each
genuinely good, is worth more than three hundred mediocre ones, because the
whole premise is that quality is the only axis where the competition is weak.

Week one is the engine and the first drawing together, because a drawing engine
designed without a drawing in front of it will be wrong.

## Kill conditions

Stop and reconsider if any of these are true after the first three visualisers
have been live for six weeks.

The Pinterest images are not being saved. That is the fastest signal available
and it arrives within days rather than months, which is precisely why the
distribution plan leads with it.

The drawings are not being screenshotted or linked. If nobody sends them to
anybody, the screenshot test was applied too loosely.

The pages are being indexed but the drawings are not appearing in image search.
That usually means a technical fault rather than a strategic one, so check the
image sitemap and the filenames before concluding anything.

## What this replaces

The programmatic plan in `docs/pseo-plan.md` assumed scale was the lever. The
research says it is not, because seventy sites got there first. Treat that
document as superseded for anything past its first release, and keep it only for
the engine design, which is still correct and is reused here.

The 95 content rewrites are unaffected and remain the highest confidence work on
the board.
