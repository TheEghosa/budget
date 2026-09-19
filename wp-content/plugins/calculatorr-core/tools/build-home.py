#!/usr/bin/env python3
"""
Builds the calculatorr.org homepage and the all-calculators hub from the live
calculator inventory and the design system's artwork.

The homepage is page content rather than a plugin template, because it is a
marketing page the owner should be able to open and edit. Everything it needs
travels with it: the markup, its own stylesheet, and the artwork inline, since
a category grid is ten icons and a card row is six motifs and thirty extra
requests to draw one page is thirty too many.

Colours come from the design system's custom properties with a literal only as
a floor, so the page follows the light and dark switch with the rest of the
site. Writing the literals as the primary value is exactly what once left this
block stranded in light mode while the chrome around it went dark.

Usage: python3 tools/build-home.py <inventory.json> <out-dir> [assets-dir]
"""

import html
import json
import pathlib
import re
import sys

ASSETS = pathlib.Path(__file__).resolve().parent.parent / 'design' / 'assets'

# The three literals that differ between the light and dark copy of a motif.
THEMED = {
    '#4fb8a5': 'var(--c-brand-graphic)',
    '#e8a33a': 'var(--c-accent)',
    '#eef1f4': 'var(--c-text-primary)',
}

# Category key -> icon, blurb. The blurbs are the site's own, not the spec's
# placeholders, because the site has had them all along.
CATEGORIES = {
    'financial-calculators-online': ('category-financial', 'Financial', 'Savings, interest, retirement and take-home pay'),
    'loan-calculators-online': ('category-loans', 'Loans', 'Repayments, affordability and payoff plans'),
    'health-calculators-online': ('category-health', 'Health &amp; Body', 'BMI, calories, macros and training zones'),
    'math-calculators-online': ('category-math', 'Math', 'Percentages, fractions, averages and solvers'),
    'geometry-calculators-online': ('category-geometry', 'Geometry', 'Area, volume, triangles and surface area'),
    'unit-conversion-calculators-online': ('category-conversion', 'Unit Conversion', 'Length, weight, temperature and cooking'),
    'business-calculators-online': ('category-business', 'Business &amp; Shopping', 'Margin, markup, break-even, tax and tips'),
    'construction-calculators-online': ('category-construction', 'Construction &amp; DIY', 'Paint, flooring, concrete, roofing and fencing'),
    'time-and-date-calculators-online': ('category-time', 'Time &amp; Date', 'Age, durations, business days and time zones'),
    'grade-calculators-online': ('category-grade', 'Grade &amp; Study', 'GPA, grades, reading time and word counts'),
}

# The six cards under "Most used today": slug, its motif, and its own icon.
# A calculator with no icon of its own falls back to its category's, because
# the design supplies ten tool icons and the site has a hundred and five
# calculators, and an empty tile on every card is worse than a shared one.
FEATURED = [
    ('percentage-calculator', 'percentage', None),
    ('mortgage-payment-calculator', 'mortgage-payment-home', 'tool-mortgage-payment'),
    ('bmi-calculator', 'bmi', 'tool-bmi'),
    ('age-calculator', 'age', 'tool-age'),
    ('tip-calculator', 'tip', 'tool-tip'),
    ('compound-interest-calculator', 'compound-interest', 'tool-compound-interest'),
]

# The chips under the search, named by slug so a rename fails the build rather
# than shipping a dead link.
POPULAR = [
    ('percentage-calculator', 'Percentage'),
    ('mortgage-payment-calculator', 'Mortgage'),
    ('bmi-calculator', 'BMI'),
    ('age-calculator', 'Age'),
    ('tip-calculator', 'Tip'),
    ('compound-interest-calculator', 'Compound interest'),
]

STATS = [
    ('105', 'calculators and converters'),
    ('10', 'use cases, from loans to grades'),
    ('0', 'sign-ups, accounts or paywalls'),
    ('1 tap', 'to copy or share any result'),
]


def esc(text):
    return html.escape(str(text), quote=True)


def art(kind, name):
    """One piece of the design system's artwork, tokenised for the theme."""
    path = ASSETS / kind / (name + '.svg')

    if not path.exists():
        return ''

    svg = path.read_text().strip()
    svg = svg.replace(' xmlns="http://www.w3.org/2000/svg"', '')

    for literal, token in THEMED.items():
        svg = re.sub(literal, token, svg, flags=re.I)

    return svg


def icon(name, fallback=''):
    return art('icons', name) or art('icons', fallback)


def motif(name):
    return art('thumbnails/dark', name)


def tile(svg, size, klass='ch-tile'):
    """The signature element: a rounded teal square holding a stroke icon.

    The radius is 0.29 of the tile, which is the ratio the design system gives
    with a worked table, and the glyph is half the tile.
    """
    return (
        '<span class="%s" style="--tile:%dpx;--tile-radius:%dpx;--tile-glyph:%dpx" aria-hidden="true">%s</span>'
        % (klass, size, round(size * 0.29), round(size * 0.5), svg)
    )


CSS = """
.ch{--ch-page:var(--c-bg-page,#111418);--ch-raised:var(--c-bg-raised,#14181d);
 --ch-card:var(--c-surface-card,#181c22);--ch-sunken:var(--c-surface-sunken,#1c2128);
 --ch-hover:var(--c-surface-hover,#232932);--ch-line:var(--c-border-default,#262c34);
 --ch-line-strong:var(--c-border-strong,#2f363f);--ch-hover-line:var(--c-border-hover,#3a424c);
 --ch-ink:var(--c-text-primary,#eef1f4);--ch-2nd:var(--c-text-secondary,#b7c0c9);
 --ch-muted:var(--c-text-muted,#9ea7b1);--ch-subtle:var(--c-text-subtle,#8a949e);
 --ch-brand:var(--c-brand,#4fb8a5);--ch-on-brand:var(--c-on-brand,#0d1714);
 --ch-glyph:var(--c-tile-glyph,#13171c);--ch-soft:var(--c-brand-soft-text,#7fd3c2);
 --ch-tint:var(--c-brand-tint,rgba(79,184,165,.10));--ch-tint-line:var(--c-brand-tint-border,rgba(79,184,165,.24));
 --ch-accent:var(--c-accent,#e8a33a);--ch-on-accent:var(--c-on-accent,#14171c);
 --ch-result:var(--c-result-bg,#152320);--ch-result-line:var(--c-result-border,#244039);
 --ch-display:var(--calcr-font-display,'Space Grotesk',ui-sans-serif,system-ui,sans-serif);
 --ch-body:var(--calcr-font-body,'Source Sans 3',ui-sans-serif,system-ui,sans-serif);
 --ch-gutter:var(--calcr-gutter,clamp(24px,8.34vw,120px));
 font-family:var(--ch-body);color:var(--ch-ink);background:var(--ch-page);
 width:100%;max-width:100%;box-sizing:border-box;overflow-x:clip;
 -webkit-font-smoothing:antialiased}
.ch *,.ch *::before,.ch *::after{box-sizing:border-box}
.ch a{color:var(--ch-brand);text-decoration:none}
.ch a.ch-cat,.ch a.ch-tool{color:var(--ch-ink)}
body:has(.ch) .page-header{display:none}
/* These pages paint their own edge-to-edge bands, so the theme's content
   container is released for them rather than escaped with width:100vw. The
   viewport unit is the thing worth avoiding: it counts the scrollbar where the
   companion percentage margin does not, and the resulting gap compounds into a
   sideways shift. The same rule lives in the plugin's site.css, and is repeated
   here so the page is right on its own, before any plugin update reaches the
   server. Whichever arrives first, the declarations are identical, so there is
   nothing for them to disagree about. */
body:has(.ch) .site-main,
body:has(.ch) .page-content{max-width:none;width:100%;margin-inline:0;padding-inline:0}
.ch h1,.ch h2,.ch h3{color:var(--ch-ink);font-family:var(--ch-display);letter-spacing:-.025em}

/* The tile: a rounded teal square with a half-size glyph in it. */
.ch-tile{width:var(--tile);height:var(--tile);flex:0 0 auto;border-radius:var(--tile-radius);
 background:var(--ch-brand);color:var(--ch-glyph);display:inline-flex;align-items:center;justify-content:center}
.ch-tile svg{width:var(--tile-glyph);height:var(--tile-glyph)}

/* ---------------------------------------------------------------- hero -- */
.ch-hero{background:var(--ch-raised);padding:clamp(40px,7vw,88px) var(--ch-gutter) clamp(48px,8vw,104px);
 display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:64px;align-items:center}
.ch-hero__copy{display:flex;flex-direction:column;align-items:flex-start;gap:24px;max-width:600px;min-width:0}
.ch-eyebrow{display:inline-flex;align-items:center;gap:9px;min-height:34px;padding:0 14px;
 background:var(--ch-tint);border:1px solid var(--ch-tint-line);border-radius:999px;
 font-size:13px;font-weight:600;color:var(--ch-soft)}
.ch-eyebrow::before{content:"";width:7px;height:7px;border-radius:50%;background:var(--ch-accent)}
.ch h1{margin:0;font-size:clamp(34px,5.4vw,68px);font-weight:700;line-height:1.02;letter-spacing:-.035em}
.ch h1 em{font-style:normal;color:var(--ch-brand)}
.ch-lede{margin:0;font-size:clamp(16px,1.5vw,20px);line-height:1.55;color:var(--ch-muted);max-width:540px}

.ch-search{position:relative;width:100%;max-width:560px}
.ch-search__box{display:flex;align-items:center;gap:12px;min-height:64px;padding:8px 8px 8px 20px;
 background:var(--ch-page);border:1px solid var(--ch-line-strong);border-radius:16px;color:var(--ch-subtle)}
.ch-search__box:focus-within{border-color:var(--ch-brand);box-shadow:0 0 0 3px var(--c-focus-ring,rgba(79,184,165,.22))}
.ch-search input{flex:1 1 auto;min-width:0;border:0;outline:0;background:transparent;
 font-family:var(--ch-body);font-size:16px;color:var(--ch-ink)}
.ch-search input::placeholder{color:var(--ch-subtle)}
.ch-search button{flex:0 0 auto;min-height:48px;padding:0 24px;background:var(--ch-brand);color:var(--ch-on-brand);
 border:0;border-radius:11px;font-family:var(--ch-display);font-size:16px;font-weight:700;cursor:pointer;white-space:nowrap}
.ch-results{position:absolute;z-index:40;top:calc(100% + 8px);left:0;right:0;background:var(--ch-card);
 border:1px solid var(--ch-line);border-radius:16px;box-shadow:var(--c-shadow-float,0 30px 60px rgba(0,0,0,.35));
 padding:6px;margin:0;list-style:none;max-height:340px;overflow:auto;text-align:left}
.ch-results[hidden]{display:none}
.ch-results a{display:block;padding:10px 12px;border-radius:10px;color:var(--ch-ink);font-size:15px}
.ch-results a:hover{background:var(--ch-hover)}
.ch-results small{display:block;color:var(--ch-muted);font-size:13px}
.ch-results__empty{padding:12px;color:var(--ch-muted);font-size:15px}

.ch-chips{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.ch-chips>span{font-size:15px;color:var(--ch-subtle)}
.ch-chip{display:inline-flex;align-items:center;gap:8px;min-height:36px;padding:0 14px;
 background:var(--ch-sunken);border-radius:999px;font-size:14px;font-weight:600;color:var(--ch-ink)}
.ch-chip::before{content:"";width:7px;height:7px;border-radius:2px;background:var(--ch-brand)}
.ch-chip:hover{background:var(--ch-hover);color:var(--ch-ink)}

/* The preview card, and the three pieces floating around it. */
.ch-preview{position:relative;justify-self:end;width:100%;min-width:0;max-width:568px;--ch-inset-x:40px;--ch-inset-y:36px}
.ch-preview__card{margin:var(--ch-inset-y) var(--ch-inset-x) 0;
 background:var(--ch-card);border:1px solid var(--ch-line-strong);border-radius:24px;padding:28px;
 box-shadow:var(--c-shadow-float,0 30px 60px rgba(0,0,0,.35));display:flex;flex-direction:column;gap:18px}
.ch-preview__head{display:flex;align-items:center;gap:14px}
.ch-preview__name{font-family:var(--ch-display);font-size:17px;font-weight:700;line-height:1.2}
.ch-preview__cat{display:block;font-family:var(--ch-body);font-size:13px;font-weight:400;color:var(--ch-muted)}
.ch-preview__cells{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.ch-cell{background:var(--ch-sunken);border-radius:12px;padding:12px 14px}
.ch-cell span{display:block;font-size:12px;color:var(--ch-muted)}
.ch-cell strong{display:block;margin-top:3px;font-family:var(--ch-display);font-size:17px;font-weight:700;
 font-variant-numeric:tabular-nums}
.ch-preview__result{background:var(--ch-result);border:1px solid var(--ch-result-line);border-radius:16px;padding:18px}
.ch-preview__label{font-size:13px;font-weight:600;color:var(--ch-soft)}
.ch-preview__value{display:block;margin:4px 0 14px;font-family:var(--ch-display);font-size:38px;font-weight:700;
 line-height:1;font-variant-numeric:tabular-nums}
.ch-split{display:flex;height:8px;border-radius:999px;overflow:hidden;gap:2px}
.ch-split i{display:block}
.ch-split__legend{display:flex;flex-wrap:wrap;gap:8px 18px;margin-top:12px;font-size:12px;color:var(--ch-muted)}
.ch-split__legend span{display:inline-flex;align-items:center;gap:7px}
.ch-split__legend i{width:8px;height:8px;border-radius:2px}
.ch-float{position:absolute;display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:16px;
 box-shadow:var(--c-shadow-chip,0 16px 32px rgba(0,0,0,.3))}
.ch-float--bmi{top:0;right:0;background:var(--ch-brand);color:var(--ch-on-brand);transform:rotate(4deg)}
.ch-float--tip{bottom:18px;right:0;background:var(--ch-accent);color:var(--ch-on-accent);transform:rotate(-3deg)}
.ch-float--pct{bottom:0;left:0;transform:rotate(-5deg)}
.ch-float small{display:block;font-size:12px;font-weight:600}
.ch-float strong{font-family:var(--ch-display);font-size:20px;font-weight:700;line-height:1.2}

/* ------------------------------------------------------------ sections -- */
.ch-band{padding:clamp(44px,7vw,96px) var(--ch-gutter)}
.ch-band--tight{padding-top:0}
.ch-head{display:flex;align-items:flex-end;gap:24px;flex-wrap:wrap;margin-bottom:40px}
.ch-head>div{flex:1 1 320px;display:flex;flex-direction:column;gap:8px}
.ch h2{margin:0;font-size:clamp(26px,3.4vw,40px);font-weight:700;line-height:1.1}
.ch-head p{margin:0;font-size:18px;color:var(--ch-muted)}
.ch-head a{display:inline-flex;align-items:center;gap:8px;font-size:16px;font-weight:600;white-space:nowrap}

.ch-cats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:20px}
.ch-cat{background:var(--ch-card);border:1px solid var(--ch-line);border-radius:20px;padding:24px;
 display:flex;flex-direction:column;gap:14px;min-height:236px;color:inherit;
 transition:transform 180ms ease,border-color 180ms ease}
.ch-cat:hover{transform:translateY(-4px);border-color:var(--ch-hover-line);color:inherit}
.ch-cat__name{font-family:var(--ch-display);font-size:19px;font-weight:600;line-height:1.25}
.ch-cat__blurb{flex:1 1 auto;font-size:15px;line-height:1.45;color:var(--ch-muted)}
.ch-count{font-size:13px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--ch-brand)}

.ch-tools{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px}
.ch-tool{background:var(--ch-card);border:1px solid var(--ch-line);border-radius:22px;overflow:hidden;
 display:flex;flex-direction:column;color:inherit;transition:transform 180ms ease,border-color 180ms ease}
.ch-tool:hover{transform:translateY(-4px);border-color:var(--ch-hover-line);color:inherit}
.ch-thumb{position:relative;height:168px;background:var(--ch-tint);border-bottom:1px solid var(--ch-line);overflow:hidden}
.ch-thumb .ch-tile{position:absolute;top:22px;left:22px;z-index:1}
.ch-thumb svg.ch-motif{position:absolute;inset:0;width:100%;height:100%}
.ch-tool__body{padding:22px 24px 24px;display:flex;flex-direction:column;gap:8px}
.ch-tool__cat{font-size:13px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--ch-brand)}
.ch-tool__name{font-family:var(--ch-display);font-size:21px;font-weight:600;line-height:1.25}
.ch-tool__blurb{margin:0;font-size:16px;line-height:1.45;color:var(--ch-muted)}

.ch-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));background:var(--ch-card);
 border:1px solid var(--ch-line);border-radius:22px;overflow:hidden}
.ch-stat{padding:32px;border-left:1px solid var(--ch-line);display:flex;flex-direction:column;gap:6px}
.ch-stat:first-child{border-left:0}
.ch-stat strong{font-family:var(--ch-display);font-size:clamp(30px,3.6vw,44px);font-weight:700;line-height:1;
 letter-spacing:-.03em;color:var(--ch-brand);
 font-variant-numeric:tabular-nums}
.ch-stat span{font-size:16px;line-height:1.45;color:var(--ch-muted)}

.ch-all{display:none}


@media (prefers-reduced-motion:reduce){
 .ch-cat:hover,.ch-tool:hover{transform:none}
}

@media (max-width:1199px){
 .ch-hero{grid-template-columns:minmax(0,1fr);gap:44px}
 .ch-preview{justify-self:stretch;max-width:560px;--ch-inset-x:0px;--ch-inset-y:0px}
 .ch-float{display:none}
 .ch-cats{grid-template-columns:repeat(3,minmax(0,1fr))}
 .ch-tools{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media (max-width:767px){
 .ch-cats{grid-template-columns:repeat(2,minmax(0,1fr))}
 .ch-cat{min-height:148px;padding:16px;border-radius:18px;gap:10px}
 .ch-cat__blurb{display:none}
 .ch-cat__name{font-size:16px}
 .ch-tools{grid-template-columns:1fr}
 .ch-stats{grid-template-columns:repeat(2,minmax(0,1fr))}
 .ch-stat{border-left:0;border-top:1px solid var(--ch-line);padding:22px 20px}
 .ch-stat:nth-child(2){border-left:1px solid var(--ch-line)}
 .ch-stat:nth-child(4){border-left:1px solid var(--ch-line)}
 .ch-stat:first-child,.ch-stat:nth-child(2){border-top:0}
 .ch-search__box{min-height:56px;padding:6px 6px 6px 16px}
 .ch-head{margin-bottom:24px}
 .ch-head a{display:none}
 .ch-all{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:20px;min-height:48px;
  border:1px solid var(--ch-line-strong);border-radius:12px;font-family:var(--ch-display);font-size:15px;
  font-weight:600;color:var(--ch-ink)}
 .ch-thumb{height:136px}
}

@media (max-width:379px){
 .ch-cats{grid-template-columns:minmax(0,1fr)}
 .ch-cat{min-height:0}
 .ch-stats{grid-template-columns:minmax(0,1fr)}
 .ch-stat,.ch-stat:nth-child(2),.ch-stat:nth-child(4){border-left:0}
 .ch-stat:nth-child(2){border-top:1px solid var(--ch-line)}
}
"""

SCRIPT = """
(function () {
  var root = document.getElementById('ch-home');
  if (!root) { return; }
  var data = JSON.parse(document.getElementById('ch-index').textContent);
  var input = root.querySelector('#ch-q');
  var list = root.querySelector('#ch-results');
  var form = root.querySelector('#ch-form');

  /* A prefix match beats a word-start match, which beats a match anywhere, so
     "per" puts Percentage first rather than Percent Off. */
  function score(name, term) {
    var n = name.toLowerCase();
    if (n.indexOf(term) === 0) { return 0; }
    if (n.indexOf(' ' + term) > -1) { return 1; }
    if (n.indexOf(term) > -1) { return 2; }
    return -1;
  }

  function render(term) {
    if (term.length < 2) { list.hidden = true; list.innerHTML = ''; return; }
    var hits = [];
    for (var i = 0; i < data.length; i++) {
      var s = score(data[i].n, term);
      if (s > -1) { hits.push({ item: data[i], rank: s }); }
    }
    hits.sort(function (a, b) { return a.rank - b.rank || a.item.n.length - b.item.n.length; });
    if (!hits.length) {
      list.innerHTML = '<li class="ch-results__empty">Nothing matches that yet.</li>';
      list.hidden = false;
      return;
    }
    var out = '';
    for (var j = 0; j < Math.min(hits.length, 8); j++) {
      var it = hits[j].item;
      out += '<li><a href="' + it.u + '">' + it.n + '<small>' + it.c + '</small></a></li>';
    }
    list.innerHTML = out;
    list.hidden = false;
  }

  input.addEventListener('input', function () { render(input.value.trim().toLowerCase()); });
  input.addEventListener('focus', function () { render(input.value.trim().toLowerCase()); });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var first = list.querySelector('a');
    if (first) { window.location.href = first.getAttribute('href'); }
  });

  document.addEventListener('click', function (e) {
    if (!root.contains(e.target)) { list.hidden = true; }
  });
  input.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { list.hidden = true; }
  });
}());
"""


def build(inventory, base='/'):
    by_slug = {}
    for cat in inventory:
        for item in cat['items']:
            by_slug[item['slug']] = (item, cat)

    missing = [s for s, _ in POPULAR if s not in by_slug]
    missing += [s for s, _, _ in FEATURED if s not in by_slug]
    if missing:
        raise SystemExit('Homepage points at calculators that do not exist: ' + ', '.join(sorted(set(missing))))

    unknown = [c['slug'] for c in inventory if c['slug'] not in CATEGORIES]
    if unknown:
        raise SystemExit('No icon or blurb for category: ' + ', '.join(unknown))

    total = sum(len(c['items']) for c in inventory)
    p = ['<!-- wp:html -->', '<div class="ch" id="ch-home">',
         '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?'
         'family=Space+Grotesk:wght@500;600;700&amp;family=Source+Sans+3:wght@400;500;600;700&amp;display=swap">',
         '<style>' + CSS.strip() + '</style>']

    # ----------------------------------------------------------- hero ----
    p.append('<section class="ch-hero">')
    p.append('<div class="ch-hero__copy">')
    p.append('<p class="ch-eyebrow">%d tools, ten use cases, no sign-up</p>' % total)
    p.append('<h1>A calculator for <em>everything</em> in life</h1>')
    p.append('<p class="ch-lede">Mortgages, macros, percentages, paint. Type your numbers in and '
             'read the answer, because working it out yourself was never the interesting part.</p>')
    p.append(
        '<form class="ch-search" id="ch-form" role="search" autocomplete="off">'
        '<div class="ch-search__box">%s'
        '<label class="screen-reader-text" for="ch-q" style="position:absolute;width:1px;height:1px;'
        'overflow:hidden;clip:rect(0 0 0 0)">Search calculators</label>'
        '<input id="ch-q" type="search" placeholder="Try mortgage, BMI, percentage, tip">'
        '<button type="submit">Search</button></div>'
        '<ul class="ch-results" id="ch-results" hidden></ul></form>' % icon('ui-search'))

    chips = ['<span>Most used today</span>']
    for slug, label in POPULAR:
        item, cat = by_slug[slug]
        chips.append('<a class="ch-chip" href="%s%s/%s/">%s</a>' % (base, cat['slug'], slug, esc(label)))
    p.append('<div class="ch-chips">' + ''.join(chips) + '</div>')
    p.append('</div>')

    # The preview: a real calculator with real figures, which is the promise
    # the page is making shown rather than described.
    boat = by_slug.get('boat-loan-calculator')
    boat_url = '%s%s/boat-loan-calculator/' % (base, boat[1]['slug']) if boat else base
    p.append('<div class="ch-preview">')
    p.append('<a class="ch-preview__card" href="%s">' % boat_url)
    p.append('<span class="ch-preview__head">%s<span class="ch-preview__name">Boat Loan Calculator'
             '<span class="ch-preview__cat">Loan Calculators</span></span></span>'
             % tile(icon('tool-boat-loan', 'category-loans'), 40))
    p.append('<span class="ch-preview__cells">'
             '<span class="ch-cell"><span>Boat price</span><strong>$60,000</strong></span>'
             '<span class="ch-cell"><span>Deposit</span><strong>$12,000</strong></span>'
             '<span class="ch-cell"><span>Interest rate</span><strong>7.5%</strong></span>'
             '<span class="ch-cell"><span>Term</span><strong>15 years</strong></span></span>')
    p.append('<span class="ch-preview__result">'
             '<span class="ch-preview__label">Monthly payment</span>'
             '<span class="ch-preview__value">$444.97</span>'
             '<span class="ch-split"><i style="width:60%;background:var(--ch-brand)"></i>'
             '<i style="width:40%;background:var(--ch-accent)"></i></span>'
             '<span class="ch-split__legend">'
             '<span><i style="background:var(--ch-brand)"></i>Principal $48,000</span>'
             '<span><i style="background:var(--ch-accent)"></i>Interest $32,094</span>'
             '</span></span>')
    p.append('</a>')
    p.append('<span class="ch-float ch-float--bmi" aria-hidden="true">%s<span>'
             '<small>BMI, 70 kg at 175 cm</small><strong>22.9 healthy weight</strong></span></span>'
             % icon('tool-bmi'))
    p.append('<span class="ch-float ch-float--tip" aria-hidden="true">%s<span>'
             '<small>18%% tip on $64.00</small><strong>$11.52</strong></span></span>' % icon('tool-tip'))
    p.append('<span class="ch-float ch-float--pct" aria-hidden="true">%s</span>'
             % tile(icon('category-math'), 64))
    p.append('</div>')
    p.append('</section>')

    # ------------------------------------------------------ categories ----
    p.append('<section class="ch-band"><div class="ch-head"><div>'
             '<h2>Browse by use case</h2>'
             '<p>Ten categories, organised the way people actually search.</p></div>'
             '<a href="%sall-calculators/">All %d calculators %s</a></div>' % (base, total, icon('ui-arrow-right')))
    p.append('<div class="ch-cats">')
    for cat in inventory:
        icon_name, short_name, blurb = CATEGORIES[cat['slug']]
        count = len(cat['items'])
        p.append('<a class="ch-cat" href="%s%s/">%s'
                 '<span class="ch-cat__name">%s</span>'
                 '<span class="ch-cat__blurb">%s</span>'
                 '<span class="ch-count">%d calculator%s</span></a>'
                 % (base, cat['slug'], tile(icon(icon_name), 52), short_name, esc(blurb),
                    count, '' if count == 1 else 's'))
    p.append('</div>')
    p.append('<a class="ch-all" href="%sall-calculators/">All %d calculators</a>' % (base, total))
    p.append('</section>')

    # ----------------------------------------------------------- tools ----
    p.append('<section class="ch-band ch-band--tight"><div class="ch-head"><div>'
             '<h2>Most used today</h2>'
             '<p>The six calculators people reach for first.</p></div></div>')
    p.append('<div class="ch-tools">')
    for slug, motif_name, icon_name in FEATURED:
        item, cat = by_slug[slug]
        cat_icon, cat_short = CATEGORIES[cat['slug']][0], CATEGORIES[cat['slug']][1]
        p.append('<a class="ch-tool" href="%s%s/%s/">'
                 '<span class="ch-thumb">%s%s</span>'
                 '<span class="ch-tool__body">'
                 '<span class="ch-tool__cat">%s</span>'
                 '<span class="ch-tool__name">%s</span>'
                 '<span class="ch-tool__blurb">%s</span>'
                 '</span></a>'
                 % (base, cat['slug'], slug,
                    tile(icon(icon_name or cat_icon, cat_icon), 52),
                    motif(motif_name).replace('<svg ', '<svg class="ch-motif" ', 1),
                    cat_short, esc(item['name']), esc(item['blurb'])))
    p.append('</div></section>')

    # ----------------------------------------------------------- stats ----
    p.append('<section class="ch-band ch-band--tight"><div class="ch-stats">')
    for value, caption in STATS:
        p.append('<div class="ch-stat"><strong>%s</strong><span>%s</span></div>' % (esc(value), esc(caption)))
    p.append('</div></section>')

    index = [{'n': item['name'], 'u': '%s%s/%s/' % (base, cat['slug'], item['slug']), 'c': cat['h1']}
             for cat in inventory for item in cat['items']]
    p.append('<script type="application/json" id="ch-index">%s</script>'
             % json.dumps(index, separators=(',', ':')).replace('</', '<\\/'))
    p.append('<script>' + SCRIPT.strip() + '</script>')
    p.append('</div>')
    p.append('<!-- /wp:html -->')
    return '\n'.join(p)


INDEX_CSS = """
.ch-index{padding:clamp(36px,6vw,72px) var(--ch-gutter)}
.ch-index__head{display:flex;flex-direction:column;gap:12px;margin-bottom:32px;max-width:760px}
.ch-index__head p{margin:0;font-size:17px;line-height:1.6;color:var(--ch-muted)}
.ch-jump{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:36px}
.ch-jump a{display:inline-flex;align-items:center;min-height:36px;padding:0 14px;background:var(--ch-sunken);
 border-radius:999px;font-size:14px;font-weight:600;color:var(--ch-ink)}
.ch-jump a:hover{background:var(--ch-hover)}
.ch-block{background:var(--ch-card);border:1px solid var(--ch-line);border-radius:22px;padding:28px;
 margin-bottom:20px;scroll-margin-top:96px}
.ch-block__head{display:flex;flex-wrap:wrap;align-items:center;gap:8px 14px;margin-bottom:8px}
.ch-block h2{font-size:22px;min-width:0}
.ch-block>p{margin:0 0 20px;font-size:15px;line-height:1.6;color:var(--ch-muted);max-width:760px}
.ch-block ul{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px 24px}
.ch-block li{font-size:15px;line-height:1.5}
@media (max-width:1199px){.ch-block ul{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media (max-width:767px){.ch-block ul{grid-template-columns:1fr}.ch-block{padding:20px}}
"""


def build_index(inventory, base='/'):
    """The A to Z hub. It exists because one page linking to all 105 gives every
    calculator a second internal link, and somebody who does not know the
    category name still has one place to look."""
    total = sum(len(c['items']) for c in inventory)
    p = ['<!-- wp:html -->', '<div class="ch" id="ch-home">',
         '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?'
         'family=Space+Grotesk:wght@500;600;700&amp;family=Source+Sans+3:wght@400;500;600;700&amp;display=swap">',
         '<style>' + CSS.strip() + INDEX_CSS.strip() + '</style>',
         '<section class="ch-index">']
    p.append('<div class="ch-index__head"><h1>All %d calculators</h1>'
             '<p>Every tool on the site, grouped by the job it does. Each one runs in your browser, '
             'shows the formula it used and needs no account.</p></div>' % total)
    p.append('<nav class="ch-jump" aria-label="Jump to a category">')
    for cat in inventory:
        p.append('<a href="#%s">%s</a>' % (cat['slug'], esc(cat['h1'])))
    p.append('</nav>')
    for cat in inventory:
        icon_name = CATEGORIES[cat['slug']][0]
        count = len(cat['items'])
        p.append('<section class="ch-block" id="%s">' % cat['slug'])
        p.append('<div class="ch-block__head">%s<h2><a href="%s%s/">%s</a></h2>'
                 '<span class="ch-count">%d calculator%s</span></div>'
                 % (tile(icon(icon_name), 40), base, cat['slug'], esc(cat['h1']),
                    count, '' if count == 1 else 's'))
        if cat.get('desc'):
            p.append('<p>%s</p>' % esc(cat['desc']))
        p.append('<ul>')
        for item in sorted(cat['items'], key=lambda i: i['name']):
            p.append('<li><a href="%s%s/%s/">%s</a></li>' % (base, cat['slug'], item['slug'], esc(item['name'])))
        p.append('</ul></section>')
    p.append('</section></div>')
    p.append('<!-- /wp:html -->')
    return '\n'.join(p)


if __name__ == '__main__':
    src, out_dir = sys.argv[1], sys.argv[2].rstrip('/')

    with open(src) as fh:
        inv = json.load(fh)

    for name, markup in (('home.html', build(inv)), ('all-calculators.html', build_index(inv))):
        with open(out_dir + '/' + name, 'w') as fh:
            fh.write(markup)
        print('%s/%s written, %d bytes' % (out_dir, name, len(markup)))
