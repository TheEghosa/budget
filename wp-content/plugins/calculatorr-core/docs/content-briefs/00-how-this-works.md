# Copy hand-off: how to send work back so it publishes without rework

You are writing for **calculatorr.org**, a site of free calculators. Everything
you write is published by a script, straight into the live site, with no human
retyping it. That one fact drives every rule below, because a file that does not
parse is a file that does not publish, and a file that parses but invents a key
publishes something nobody asked for.

There are two jobs, and they have separate briefs.

**Job one** is rewriting the explanatory copy under calculators that already
exist. Ninety-five of them, five a day, and the brief is
`01-existing-calculator-rewrites.md`.

**Job two** is writing the copy skeleton for programmatic page templates, where
one thing you write becomes a few hundred pages. The brief is
`02-pseo-template-copy.md`, and it is the harder of the two because the copy has
to stay true across every value the variables take.

## The rules that apply to both jobs

Write in natural prose. Vary sentence length deliberately, and never let three
sentences in a row land at roughly the same length, because that is what makes
writing read like a machine wrote it. Connect ideas with because, so, which
means, since, otherwise. Do not write runs of short declarative fragments each
closed with a full stop.

Every section needs at least one sentence carrying a subordinate clause, and
every instruction needs its reason attached. "Order ten per cent extra" is worse
than "order ten per cent extra, because cuts and breakages on a tiled floor
routinely eat that much and a second delivery costs more than the spare boxes."

Use active voice and second person. The reader is one person trying to get a
number, not an audience.

**Never use em dashes.** Commas, colons and parentheses do the same work. This
is not a preference, it is a hard rule, and a file containing one comes back.

No AI tics. Nothing "delves into", nothing is "a testament to", nothing is "in
today's fast-paced world", no "it's important to note that", no "unlock the
power of". If a sentence could open a LinkedIn post, rewrite it.

Write for a person who has just been given a number and wants to know whether
they can trust it and what to do with it. That is the whole job. A page that
restates the calculator in words has failed, because the reader can already see
the calculator.

## Numbers are the thing you must not get wrong

Every figure you write gets checked against the calculator that produced it, and
a mismatch stops the batch. So do not write a figure you have not worked out.

If a section needs an example, do the arithmetic and show it. If you cannot
verify a claim about the world, either leave it out or write it as a range with
its basis named. "Most councils allow this" is unverifiable and unusable.
"Bag yields are printed on the bag, and the common US sizes are 60 lb at 0.45
cubic feet and 80 lb at 0.60" is checkable and useful.

Where a figure changes with time, a tax rate or an electricity price or an
average interest rate, say what it is as of when, or phrase the sentence so the
figure sits in the calculator rather than in your prose.

## What you send back

One file per item, named exactly as the brief says, as valid JSON. Not Markdown
with JSON in it, not JSON inside a code fence, not a document describing the
JSON. The file itself.

Validate it before sending. `python3 -m json.tool yourfile.json` will tell you in
a second whether it parses, and a file that does not parse is the single most
common reason a batch comes back.

Escape correctly. Newlines inside a string are `\n`. Quotation marks inside a
string are `\"`. Do not use smart quotes or typographic apostrophes anywhere,
because they travel badly through the publishing chain and show up as mojibake
on the live page.

HTML allowed inside body text: `<strong>`, `<em>`, `<sup>`, `<sub>`, `<code>`,
`<a href>`. Anything else is stripped. Do not write headings as HTML, because
the heading is its own field and the renderer wraps it.

Send the files in a zip, or as a folder, in one batch per day. Partial batches
are fine and half-finished files are not, since a file that is present is
assumed to be finished.
