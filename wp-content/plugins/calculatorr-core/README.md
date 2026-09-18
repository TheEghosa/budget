# Calculatorr Core

The WordPress plugin behind calculatorr.com. Every calculator is one config
file under `calculators/`, and a single shared runtime renders all of them,
which means adding the hundred and first calculator is a config file rather
than a new template.

## Why a plugin rather than Elementor pages

Elementor Free has no Theme Builder, since that is a Pro feature, so there is
no way inside Elementor to build one reusable template that a hundred
calculator pages all inherit. Hand-building each page in the Elementor canvas
works for the first handful and then stops scaling, because every design
change would have to be repeated a hundred times by hand.

So the split is deliberate. This plugin owns the calculator pages and renders
them from config, exposing each calculator as a shortcode and as a real
Elementor widget, which does work on Elementor Free. Elementor itself is left
to do what it is genuinely good at, meaning the homepage, the category hubs
and the ordinary marketing pages.

## Current state

This is the foundation only, and it is not yet a working calculator. What
exists today is the plugin bootstrap and the registry that loads calculator
configs and serves the ten use-case categories. Both pass `php -l`.

Still to build, in the order they are needed:

| Piece | File | What it does |
|---|---|---|
| Renderer | `includes/class-renderer.php` | Turns one config into the full page body: inputs, result panel, explainer, FAQ, related links |
| Schema | `includes/class-schema.php` | Emits FAQPage, BreadcrumbList and WebApplication JSON-LD |
| Ad slots | `includes/class-ads.php` | Three filterable slots so ad code drops in without touching templates |
| Page sync | `includes/class-pages.php` | Creates the category parent page and each calculator child page, giving `/health/bmi-calculator/` from page hierarchy |
| Admin | `includes/class-admin.php` | The screen that runs the page sync |
| Elementor widget | `includes/class-elementor.php` | Registers the calculator as a native widget |
| Styles | `assets/css/tokens.css`, `assets/css/calculator.css` | Design tokens first, so a design change is a token change rather than a rebuild |
| Runtime | `assets/js/calculator.js`, `assets/js/formulas.js` | One renderer plus one formula per calculator, keyed by slug |

The design these files implement is the canvas built earlier in the session,
covering the calculator page template, its mobile layout, the homepage, a
category hub and the foundations sheet.

## Blocked on

Three facts about the target install, because the template inherits from the
theme and the host decides whether a custom plugin can be uploaded at all:
the host, the active theme, and the PHP version.
