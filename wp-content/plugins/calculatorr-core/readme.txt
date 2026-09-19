=== Calculatorr Core ===
Contributors: calculatorr
Tags: calculator, tools, seo, elementor
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.9.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

One hundred and five calculators with the SEO built in, as a shortcode and an
Elementor widget.

== Description ==

Calculatorr Core powers calculatorr.org. Every calculator is a config file and
a formula, so the templates and the runtime stay untouched as the site grows,
and adding the hundred and sixth is a config file rather than a new page.

What it does:

* 105 calculators across 10 use-case categories.
* Keyword-led URLs, generated from page hierarchy rather than rewrite rules.
* Title, description, canonical, Open Graph and Twitter tags written from the
  same config that builds the calculator, so a page cannot target one keyword
  and claim another. It stands down automatically when Yoast, Rank Math,
  AIOSEO, SEOPress or The SEO Framework is active.
* Structured data as one connected graph: Organization, WebSite, breadcrumbs,
  the tool itself, the FAQs, and an ItemList on every category hub.
* A share panel that renders the result as a branded image on the visitor's
  own device, in wide and square formats, and a link that reopens the
  calculator with their figures already in it.
* Three advertising slots that reserve their height so a late-loading unit
  cannot shift the page.
* An admin panel to switch any calculator off, retitle it, paste ad code and
  read the error log.

== Installation ==

1. Upload the zip through Plugins, Add New, Upload Plugin.
2. Activate it.
3. Go to Calculatorr and run the page sync, which creates a WordPress page for
   every calculator under its category parent.

Elementor is optional. With it active, a Calculator widget appears in the
panel with a dropdown of all 105. Without it, the `[calculatorr slug="..."]`
shortcode does the same job.

== Frequently Asked Questions ==

= Does this need Elementor Pro? =

No. The custom widget works on Elementor Free. The plugin owns the calculator
pages precisely because Elementor Free has no Theme Builder, so there is no way
inside Elementor to give a hundred pages one reusable template.

= Will it fight my SEO plugin? =

No. It detects the common ones and hands them its titles and descriptions
through their own filters rather than writing duplicate tags.

= Where does the calculation happen? =

In the visitor's browser. Nothing is sent to the server, so salaries, weights
and health measurements never leave the device.

== Changelog ==

= 1.9.0 =
Calculators can now be defined entirely in JSON and published over the REST
API, so a new tool no longer waits on a new copy of the plugin. A definition
carries its own fields, copy, questions and formula, and the registry folds it
in beside the PHP configs so nothing downstream can tell the two apart.

The formula runs in a Web Worker with no DOM and no network rather than on the
page, and the helpers it is written against moved into a shared kit that both
the worker and the shipped formulas load, so the two cannot drift. Eighty-one
of the shipped formulas were lifted into that sandbox unedited and produce
byte-identical answers, which is the test that keeps it honest.

Also fixes the Elementor widget, which enqueued the runtime without its
configuration, so a formula that threw inside a widget was never reported.

= 1.8.1 =
Ten pages rewritten to the content standard, averaging just over a thousand
words each with nineteen reference tables between them: mm to inches, feet to
metres, inches to feet, Celsius to Fahrenheit, margin, percent change, percent
off, circumference, GST and fractions.

= 1.8.0 =
Long-form content can now be published over the REST API, so rewriting a page
no longer means shipping a new build. docs/content-standard.md sets out what a
calculator page owes its reader, and the first three pages are rewritten to
it: mm to inches, Celsius to Fahrenheit and margin, each around 1100 words
with reference tables, formulas and worked examples.

= 1.7.4 =
Every calculator now offers at least four onward links. Thirty-one named
fewer than three, and the list is topped up from the calculator's own
category, walking from its own position so the incoming links spread evenly
rather than every page pointing at the same two. tools/seo-audit.php checks
titles, descriptions, canonicals, headings, structured data, content depth and
internal linking across every page the plugin renders.

= 1.7.3 =
Calculators with five fields or more lay their form out in two columns once
the card has room, which takes about 300px off the page and brings the answer
and the buttons into the first screen on a laptop. The address bar is no
longer rewritten as you type, and the link Copy link produces now carries only
the fields you filled in rather than every field on the page including the
blank and hidden ones. Two calculators were giving two elements the same HTML
id, which made their imperial labels focus the hidden metric inputs.

= 1.7.2 =
The share panel is no longer cut off at the card's edge, and opens away from
the side of the screen rather than off it. The homepage search can be worked
with the arrow keys and announces how many calculators matched. The sticky
answer bar stays away while the panel is showing its example, since there is
nothing yet to follow up the page. Reduced motion now covers the category and
home pages as well as the calculator itself. The shareable image is square
and only square: the wide one had to be chosen, and the code drew it by
default while the panel said square was selected.

= 1.7.1 =
The logo mark sits beside the wordmark rather than above it, in the header and
the footer both. The answer panel now opens showing the worked answer to the
numbers the fields carry as placeholders, muted and labelled as an example,
instead of an empty dash beside filled-looking fields. On a laptop-height
screen the title band tightens so the buttons come up into the first screen,
while a tall monitor keeps the spacing the design specifies.

= 1.7.0 =
One design across the whole site. The category pages are rebuilt to the same
grammar as the homepage, every page type now shares one gutter rather than the
homepage running to the edge while the calculator pages stopped short of it,
the logo carries its mark again, and the input fields are a single box instead
of the two the theme's own form styling was drawing inside them.

= 1.6.0 =
* The answer follows you up the page on a phone: a sticky bar carrying the live figure and a Breakdown button that scrolls to the full result.
* It only appears when it earns its place. It watches the figure rather than the panel, stays up while a field has focus, rides the visual viewport so an on-screen keyboard cannot bury it, and never shows at 768px and wider where the result already sits beside the inputs.
* Exactly one live region is active at a time, so a screen reader does not read every keystroke twice.
* The result panel gained the sentence the design puts under the number, saying what the figure is an answer to.
* The ten design artboards are vendored into the plugin as rendered markup, so the reference cannot drift away from the build.

= 1.5.0 =
* The palette is now generated from the design system's tokens.json rather than hand-written, with two corrections applied in the generator so regenerating cannot undo them: amber gets a light-mode value, and text-subtle clears the hover surface it was failing on.
* Dark is the base palette, a device asking for light gets light, and the switch overrides both.
* The names the site already used survive as aliases into the new system, so twelve hundred lines of stylesheet keep working and can move across one component at a time.
* A calculator page now promotes only calculators from its own category, instead of padding a block headed "More loans & debt tools" with the 401(k) and Age calculators.
* A category hub no longer promotes calculators it is already listing.
* Contrast is computed in the test suite, so an edited token that breaks a ratio fails the build.

= 1.4.1 =
* The long description and the FAQ answers are edited with the rich editor WordPress ships rather than a plain box, so the copy can carry bold, links, lists, headings and tables, with a Text tab for raw HTML.
* Explainer copy now runs its shortcodes, so a section can carry another calculator inside the prose instead of printing the shortcode as text. A calculator that names itself renders once rather than recursing.

= 1.4.0 =
* The dashboard now leads with usage: calculations per day, the most used calculators, and every calculator in a table with a sparkline each. It counts a calculation actually running rather than a page view, once per calculator per page load, and stores no visitor data.
* The per-calculator editor now covers the long description and the common questions as well as the meta, so the copy that earns a ranking can be written without a code change. A section's numbered steps or reference table are carried through untouched, so editing the prose cannot delete them.
* Primary and secondary keywords are editable per calculator, the secondary ones as a comma separated list for your own targeting rather than for a meta tag no search engine has read this century.
* The Header and footer tab is gone and its three boxes now live at the foot of Advertising, which is where the rest of the ad setup already was. The boxes stay because AdSense serves nothing until its loader is in the head.
* Saving an override now rebuilds the registry, so a read straight after a save returns the new text rather than the previous copy.

= 1.3.0 =
* A Design tab holding the palette, the fonts, the content width and a custom CSS box. Everything on it is a stored value written into the page as a custom property override, so a look-and-feel change reaches all 118 pages on the next request with no upload.
* The same values can be read and written over the REST API, one at a time, so the design can be adjusted without opening the admin at all.
* The webfont stylesheet is a setting too, so changing the fonts no longer means changing code.
* Values are checked against the shape they claim to be. A colour that is not a colour is dropped rather than written into a stylesheet where it would silently break the rule around it.
* Fields left at their default emit nothing, so an untouched install runs exactly as the stylesheet ships.

= 1.2.1 =
* Reset now starts disabled and switches on the moment anything changes, because with blank fields there was nothing to reset on a fresh page and a live-looking button that did nothing read as broken.
* Reset also restores repeater rows, which it had never touched: added rows and their figures were left sitting there on the GPA and time card calculators.
* The shareable image is square by default, with wide still offered.

= 1.2.0 =
* One palette across the whole site. The homepage carried its own light colours while the chrome followed the theme, so in dark mode the headings went white on white and the footer band turned into a white slab.
* A light and dark switch in the header, remembered between visits, with the system setting still the starting point for anyone who never touches it.
* Calculators open blank with the usual figure as a placeholder, so nobody has to clear somebody else's numbers first. The result panel says what to do until there is something to show, and copying and sharing stay switched off until then.
* The header wordmark inverts in dark mode rather than disappearing into the header.
* Category cards no longer inherit the theme's underline on every link.

= 1.1.0 =
* The site header, navigation and footer now carry the calculatorr design, so the pages no longer sit inside an unstyled theme shell.
* The page heading moved below the breadcrumb where the design puts it, and the theme's duplicate heading is suppressed rather than hidden.
* WordPress core's second canonical tag is removed on calculator pages, leaving one.
* The homepage gets its own meta description, Open Graph tags and a social card image, which were missing entirely.
* Settings can be read and written over the REST API, and a status route reports what the install actually has.

= 1.0.0 =
* First release. 105 calculators, the SEO layer, sharing, and the admin panel.
