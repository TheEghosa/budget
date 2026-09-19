# What a calculator page owes its reader

Every calculator on this site answers a question somebody typed into a search
box, and the tool answers it in about four seconds. The writing underneath has
to earn the rest of the visit, which means it has to tell that person something
they could not have worked out by looking at the number they just got.

This is the standard for rewriting the pages we have and for every calculator
we add after them. The concrete calculator is the page it was written from, so
when a rule here sounds abstract, go and read that one.

## How long, and why that long

Aim for 900 to 1500 words of body copy, and go to 3000 when the subject
genuinely carries it. A mortgage has more to say than a millimetre conversion,
and padding the conversion out to match would produce exactly the filler this
standard exists to prevent.

The number is a floor on usefulness rather than a target in itself. If a page
reaches 1400 words and every paragraph is still answering something a reader
would plausibly ask, keep going. If it reaches 700 and the next paragraph would
be restating the third one in different words, stop and cut the third one
instead.

Length is not the goal. It is what happens when you answer the real questions,
and the reason we write it down is that the pages we started with averaged
under 250 words, which was not enough to answer even one.

Measure with `php tools/seo-audit.php`, which counts the words of the page as
rendered, including the table cells and the headings and excluding the
navigation, the promo block and the related cards. That is the number to
quote, because counting prose alone undersells a page carrying two reference
tables and counting the whole document flatters every page equally with the
same footer.

For calibration: the concrete calculator lands at 1192 words by that measure,
and it is the page this standard describes.

## The shape

A page is a sequence of sections, each with a heading, a body, and whichever of
the extras the section has earned. The schema supports a formula, a numbered
list of steps, a worked example and a reference table, and a section that uses
all four is usually a section that should have been two.

Five to eight sections is the working range. Fewer than five and the page is
almost certainly thin. More than eight and the headings have stopped being
signposts and started being a list.

Write the headings as the questions people ask, because that is how they arrive
and it is what makes a heading scannable. "How much area does a cubic yard of
concrete cover?" earns its place. "Coverage" does not.

Order the sections the way curiosity runs. How the number is worked out comes
first, because somebody who has just been given a figure wants to know where it
came from. What to do with it comes next. What would change it comes after
that. What the calculator deliberately does not cover comes last, and it is the
section most pages skip and most readers need.

## Tables

A table earns its place when a reader would otherwise have to run the
calculator five times to see a pattern. Coverage by thickness, bag counts by
volume, tax rates by band, common sizes and their conversions: these are
lookups, and a lookup in prose is a paragraph nobody finishes.

Keep a table to three or four columns and five to ten rows. Give it a caption
that says what the table is for rather than what it contains, because "what
depth to pour for each job" tells a reader whether to stop and read it while
"thickness and coverage" only tells them what the columns are called.

Do not tabulate something with two values in it. That is a sentence.

## Formulas and worked examples

Give the formula whenever one exists, written the way it would be said aloud
rather than in algebra where the two differ. `Cubic yards = (Length ft × Width
ft × Thickness in ÷ 12) ÷ 27` is readable. `V = lwd/324` is correct and useless
to almost everybody who lands on the page.

Follow a formula with a worked example using numbers a real person would have.
A kitchen that is 12 by 14, a loan of £240,000, a 5k in 28 minutes. Round
numbers make the arithmetic easy to follow and make the example feel like a
demonstration rather than a situation.

Show the steps when the order matters or when one step is where people go
wrong. The concrete page numbers its steps because the unit change buried in
the middle of it is the single thing everybody gets wrong.

## Voice

Write in active voice, to one person, as though you knew the thing and they
asked you about it.

Vary the sentence length on purpose. Three sentences of the same length in a
row is the rhythm that makes writing feel generated, and it is the most
reliable tell there is.

Join ideas with because, so, which means, since, otherwise. A run of short
declarative sentences each closed with a full stop reads like a specification,
and a specification is not what somebody wants after being handed a number.

Pair an instruction with its reason every time. "Order ten percent extra"
is an instruction somebody will ignore. "Order ten percent extra, because
concrete cannot be topped up once the pour starts setting and a second batch
leaves a cold joint" is one they will follow.

Never use an em dash. Commas, colons and brackets do the same work and do not
announce themselves.

Do not open with a definition of the thing the page is named after. Somebody
searching for a body fat calculator knows what body fat is, and a first
paragraph explaining it is a paragraph telling them they are in the wrong place.

Say what the calculator will not tell them. Every tool on this site simplifies
something, and naming the simplification is what separates a page worth reading
from a page worth closing. It is also, in practice, the section that earns the
most trust.

## Before it ships

Read the last paragraph aloud. If it sounds like a summary of the page rather
than the end of a thought, cut it and let the page end on the last real point.

Check that every claim with a number in it is either arithmetic the reader can
follow or a figure with a source. Nothing on this site should assert a
threshold, a rate or a rule of thumb that a reader cannot check.

Check that the page would still be useful to somebody who never touched the
calculator. That is the test the search engine is effectively applying, and it
is a good test regardless of the search engine.

## Where it lives and how it ships

Write the content as JSON in `content/calculators/<slug>.json`. Two things
then become possible that are awkward otherwise. `php tools/apply-content.php`
merges it into the calculator's config file, checking the result still parses
and reverting if it does not, so a hundred files can be updated without
hand-editing nested PHP. The same JSON can be posted to
`/wp-json/calculatorr/v1/content/<slug>`, which updates a live site without
shipping a new build, and that is what makes rewriting a hundred pages
practical rather than theoretical.

The route validates against the shape the renderer reads rather than storing
what it is sent. A section without a body, a question without an answer, or a
table with a row narrower than its header are all refused with a message
naming the section, because a broken page is worse than an unwritten one.
