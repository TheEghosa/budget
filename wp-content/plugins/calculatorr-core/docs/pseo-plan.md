# The build plan: 107 calculators to a programmatic site

Written 19 September 2026, against the opportunity workbook of the same date.

## What the workbook actually says

Thirty-six representative queries were tested and the top eight results classed
for each. Twenty-seven came back open, nine mixed, none closed. Not one
government page appeared anywhere in the 36, and only four university results
turned up at all, three of those on subdomains that look like spam rather than
real university pages. That is an unusually soft competitive picture, and it is
the single strongest argument for doing this at all.

What the workbook cannot tell us is how many people search these queries. The
Ahrefs plan on this account refuses every data request with an insufficient
plan error, which I confirmed again today rather than taking on trust, and the
site's own usage counter has nothing in it because the site is days old. So
every demand figure in the workbook is judgment, it is labelled as judgment,
and this plan inherits that. Where a decision turns on demand I have said so
rather than dressing a guess as a number.

## The honest constraint, which is not build speed

The workbook totals 27,979 pages. The site has 107. Going from one to the other
is a 260-fold increase on a domain with no history, and doing it quickly is the
most reliable way to get the whole site treated as low quality rather than to
rank any of it.

So the release schedule below is paced by indexation rather than by how fast I
can build. I can build faster than Google will accept pages, which means my
throughput is not the number that matters. What matters is the share of
published pages that actually get indexed, and we hold at each gate until that
number is healthy before opening the next.

That is the most important sentence in this document. Phase 1 alone is 4,901
pages and it is roughly three months of release, not three weeks. Phase 3's
17,034 pages is a decision for next year, made on data we will have by then and
do not have now.

## Three page types, because 28,000 WordPress pages would not work

The site today has one page type. A calculator is a config plus a formula, it
gets a row in `wp_posts`, and the registry loads every config on every request.
That is fine at 107 and it would fall over long before 28,000, because the
registry's `glob` would be reading tens of thousands of files to answer a single
page view.

So programmatic pages get their own machinery and never touch the registry.

**Type one, the calculator.** What exists now. An interactive tool with 900 to
1,500 words underneath it, one WordPress page each, defined in JSON and
published over the API. These are the hubs, and the plan adds roughly forty more
so that every cluster has a real tool at its centre. Target around 150.

**Type two, the answer page.** `/concrete/20x20-slab/` and its 27,000 siblings.
No WordPress post at all. A rewrite rule matches the URL, a resolver checks the
variables against the allowed list so that `/concrete/999x999-slab/` returns a
404 rather than inventing a page, and the renderer looks up one precomputed row
from a custom table. Between 300 and 600 words, most of it computed rather than
written.

**Type three, the index.** `/concrete/slab-sizes/`, which lists every child and
is what passes crawl equity down into the set. One per template, paginated where
the set is large, and linked from the calculator that owns the cluster.

## Where the numbers come from, and why they cannot drift

Every answer page is computed at build time rather than at request time, using
the same sandbox runner and helper kit the interactive calculators already use.
The build tool expands the variable lists, runs the template's formula once per
combination, and hands the rows to the API in batches. The renderer then does a
single indexed lookup and prints the answer straight into the HTML.

This buys three things at once. The answer is in the source for a crawler with
no JavaScript needed, which is what these pages live or die on. There is no
per-request computation, so 28,000 pages cost the same as one. And because the
same formula produces both the static answer and whatever the interactive
calculator beside it shows when somebody changes a number, the two cannot
disagree, which is the failure that would otherwise be invisible until a reader
noticed.

## The uniqueness rule, enforced rather than promised

Thousands of pages differing by one number is the definition of thin content,
and the workbook makes the point itself by giving every template a column for
what makes each page unique. Treating that as a requirement means checking it,
so the build tool will refuse to publish a template whose rows do not produce
genuinely different pages.

The mechanism is conditional copy. A 4 by 4 foot slab page says you can mix this
by hand in a wheelbarrow. A 30 by 40 page says this is well past hand mixing and
here is what ordering ready-mix involves. Same template, different advice,
because the number crossed a threshold. Every template must carry at least three
such branches, each of which fires on some rows and not others, and the build
tool counts the distinct rendered bodies across the whole set and stops if there
are too few.

## Categories

The ten we have stay exactly as they are, because moving a calculator changes
its URL and every redirect is a small tax on something that currently works.
Five new ones get added, and each opens only when it has at least three real
calculators and its first template ready, since a category page listing two
things looks abandoned.

| New category | URL | Clusters it houses | Hub calculators needed |
|---|---|---|---|
| Vehicles & Driving | `/vehicle-calculators-online/` | C08 EV charging, C09 driving cost | EV charging time, EV charging cost, fuel cost per trip, car depreciation |
| Food & Events | `/party-calculators-online/` | C06 party quantities, part of C11 | Party food quantity, drinks for a party, tent size, catering per head |
| Pets & Animals | `/pet-calculators-online/` | C12 pet feeding and aquarium | Dog food portion, aquarium volume and weight, cat calorie needs |
| Garden & Outdoor | `/garden-calculators-online/` | C13 planting | Plants per square foot, raised bed soil, seed spacing |
| Sports & Gear | `/sports-calculators-online/` | C10 gear sizing, part of C05 | Bike frame size, running pace, snowboard size, race time predictor |

The clusters that map onto categories we already have need no structural work at
all. Building materials belongs in Construction & DIY, pay conversions in
Finance, date maths in Time & Date, and home electrical in Construction & DIY
beside the voltage drop calculator that is already there.

Two existing calculators sit in the wrong place and are worth one redirect each
once the new categories exist. The dog age calculator is filed under Date, Time
& Work, and the tire size calculator under Unit Conversion. Both are obvious
once you see the new homes, and neither is urgent.

## Release schedule, gated

Each release opens only when the previous one has settled. The gate is the share
of that release indexed in Search Console, checked no sooner than fourteen days
after publication, and the bar is seventy per cent. A release that misses the bar
is a signal to improve the pages already out rather than to publish more.

| Release | Templates | Pages | Running total | Opens when |
|---|---|---|---|---|
| R1 | P01 concrete slabs, P02 pavers, P03 mulch by area | 361 | 361 | Engine is built and verified |
| R2 | P20 party quantities, P16 walking, P17 steps, P18 pace | 971 | 1,332 | R1 at 70% indexed |
| R3 | P11, P12, P15 date and age | 2,107 | 3,439 | R2 at 70%, and the answer box check below |
| R4 | P09 salary to hourly, P21 amps, P22 running cost, P24 wire gauge, P27 generator | 870 | 4,309 | R3 at 70% |
| R5 | P35 lumens, P36 rug, P38 and P39 tents, P32 bike size, P45 planting | 592 | 4,901 | R4 at 70% |
| Phase 2 | Fifteen templates | 6,044 | 10,945 | Reviewed against real Search Console data |
| Phase 3 | Three templates, mostly state variants | 17,034 | 27,979 | A separate decision, next year |

R1 through R5 sum to exactly the workbook's 4,901 Phase 1 pages, which is the
check that nothing has been double counted or dropped between the two documents.

### One thing I need from you before R3

The workbook flags a risk it could not test, and it is the largest single risk
in the plan. If Google answers "90 days from today" directly in an answer box,
the clicks on 2,107 date pages collapse even though the organic results are
weak. The evidence was gathered through a search tool rather than a live Google
page, so answer boxes and AI Overviews were invisible to it, and they are
invisible to me here too.

Open Google yourself and search for "90 days from today", "45 days from today"
and "how old am i if i was born in 1990". If a calculator widget or a direct
answer sits above the results, R3 drops to the bottom of the plan and C06 and
C07 move up. That check takes two minutes and decides whether roughly a month of
work is worth doing.

## Daily cadence

Two lanes run every working day, and they do not compete for the same hours
because one of them is mostly publishing rather than writing.

**Lane one, five existing rewrites a day.** Ninety-five pages remain of the 107,
since twelve are already written to the standard. At five a day that is nineteen
working days, so just under four weeks. If the copy arrives from an outside
writer in the format specified in the briefs, my part is validation and
publishing, which is minutes for the whole batch rather than hours. If it does
not arrive, I write them, and five a day is then most of a session.

**Lane two, the build.** One substantial block a day, which is the engine first
and then a template or two, or a pair of new hub calculators when a category
needs opening.

| Days | Lane one | Lane two |
|---|---|---|
| 1 to 2 | 10 rewrites | The engine: template schema, storage, build tool, REST route, rewrite rules, resolver, renderer, sitemaps |
| 3 to 5 | 15 rewrites | R1 built and released, 361 pages, sitemaps submitted |
| 6 to 10 | 25 rewrites | R2 built and held at the gate; Food & Events and Sports & Gear hub calculators |
| 11 to 15 | 25 rewrites | R2 released if the gate passes; R3 built; Vehicles hub calculators |
| 16 to 19 | 20 rewrites, queue empty | R3 released or reordered on the answer box finding; Pets and Garden hubs |
| 20 onward | Content refreshes on whatever Search Console shows is underperforming | R4 and R5, then the Phase 2 review |

The rewrite queue is ordered in the brief, thinnest page first inside each
demand tier, because within a band of similar demand the page with the biggest
gap is the one worth the day. Day one is the square root, area, volume, date and
hours calculators, which sit between 241 and 292 rendered words against a
standard of 900 or more.

## What I need built before any of this ships

The engine is six pieces and none of them is large on its own.

A template definition format, which is the existing JSON calculator schema plus
a URL pattern, the variable lists, and the conditional copy blocks. A build tool
that expands the variables, runs the formula per row through the sandbox runner,
counts the distinct rendered bodies and refuses a template that fails the
uniqueness rule. A custom database table with an index on the URL key, because a
row per page in `wp_posts` is what we are avoiding. A REST route that accepts
rows in batches, since 11,781 rows will not fit in one request. Rewrite rules
generated from each template's URL pattern, with a resolver that validates the
variables against the allowed lists and returns a real 404 for anything else. A
sitemap generator that paginates, because a sitemap file caps at 50,000 URLs and
Search Console is happier with smaller ones anyway.

Two days of work, and everything after it is content rather than code.
