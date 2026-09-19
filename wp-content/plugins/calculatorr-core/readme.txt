=== Calculatorr Core ===
Contributors: calculatorr
Tags: calculator, tools, seo, elementor
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.2.0
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
