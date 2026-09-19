#!/usr/bin/env python3
"""
Builds the calculatorr.org homepage from the live calculator inventory.

The homepage is page content rather than a plugin template, because it is a
marketing page the owner should be able to open and edit, and because it must
render correctly whether or not the plugin's stylesheet happens to be enqueued
on that particular page. Everything it needs travels with it.

Usage: python3 tools/build-home.py <inventory.json> <out.html>
"""

import html
import json
import sys

# Matched to the artboards in the design file: one icon and one sentence per
# category, keyed by the category slug the plugin actually registers.
CATEGORY_ART = {
    'financial-calculators-online': (
        'M12 2v20M17 6H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6',
        'Savings, interest, retirement and take-home pay'),
    'loan-calculators-online': (
        'M3 10l9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z',
        'Repayments, affordability and payoff plans'),
    'health-calculators-online': (
        'M20.8 5.6a5 5 0 0 0-7.1 0L12 7.3l-1.7-1.7a5 5 0 0 0-7.1 7.1L12 21l8.8-8.3a5 5 0 0 0 0-7.1z',
        'BMI, calories, macros and training zones'),
    'math-calculators-online': (
        'M19 5L5 19M7.5 8a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zM17 21a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z',
        'Percentages, fractions, averages and solvers'),
    'geometry-calculators-online': (
        'M12 3l9 16H3z',
        'Area, volume, triangles and surface area'),
    'unit-conversion-calculators-online': (
        'M4 8h15m0 0l-4-4m4 4l-4 4M20 16H5m0 0l4 4m-4-4l4-4',
        'Length, weight, temperature and cooking'),
    'business-calculators-online': (
        'M4 20V10M10 20V4M16 20v-7M22 20H2',
        'Margin, markup, break-even, tax and tips'),
    'construction-calculators-online': (
        'M14 6l4 4L8 20H4v-4zM13 7l4 4',
        'Paint, flooring, concrete, roofing and fencing'),
    'time-and-date-calculators-online': (
        'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18zM12 7v5l3 2',
        'Age, durations, business days and time zones'),
    'grade-calculators-online': (
        'M22 9L12 4 2 9l10 5zM6 12v5c0 1.5 2.7 3 6 3s6-1.5 6-3v-5',
        'GPA, grades, reading time and word counts'),
}

# The chips under the search box. Named by slug so a rename fails the build
# instead of quietly shipping a dead link.
POPULAR = [
    ('percentage-calculator', 'Percentage'),
    ('mortgage-payment-calculator', 'Mortgage'),
    ('bmi-calculator', 'BMI'),
    ('age-calculator', 'Age'),
    ('tip-calculator', 'Tip'),
    ('compound-interest-calculator', 'Compound interest'),
]

VALUE_CARDS = [
    ('Every formula is shown',
     'Each page prints the maths it used, because a number you cannot check '
     'is a number you should not act on.'),
    ('Nothing is stored',
     'Calculations run in your browser and never reach a server, so your '
     'salary and your weight stay on your own device.'),
    ('No account, ever',
     'There is nothing to sign up for and nothing to unlock, since a tool '
     'that asks for an email before answering has stopped being a tool.'),
]

CSS = """
.ch-home{--ch-paper:#FBFAF8;--ch-surface:#FFFFFF;--ch-ink:#14181D;--ch-muted:#5A6472;
 --ch-line:#E4E2DC;--ch-line-strong:#C9CDCB;--ch-sunken:#F1EFEA;--ch-teal:#0E6E63;
 --ch-teal-soft:#E8F2F0;--ch-amber:#B45E0C;
 --ch-display:'Space Grotesk',ui-sans-serif,system-ui,sans-serif;
 --ch-body:'Source Sans 3',ui-sans-serif,system-ui,sans-serif;
 font-family:var(--ch-body);color:var(--ch-ink);background:var(--ch-paper);
 margin:0 calc(50% - 50vw);width:100vw;max-width:100vw;box-sizing:border-box;overflow-x:clip;
 -webkit-font-smoothing:antialiased}
.ch-home *,.ch-home *::before,.ch-home *::after{box-sizing:border-box}
.ch-home a{color:var(--ch-teal);text-decoration:none}
.ch-home a:hover{color:var(--ch-amber)}
.ch-wrap{max-width:1280px;margin:0 auto;padding:0 clamp(16px,4vw,64px)}
/* The full-bleed band is sized in vw, and vw counts the scrollbar, so on a
   desktop with a classic scrollbar the band is a few pixels wider than the
   page and the whole document scrolls sideways. Scoped with :has() so the
   override only applies on a page that actually carries this block. */
html:has(.ch-home){overflow-x:hidden}
/* Hello Elementor prints the page title in its own header above the content,
   which puts a second H1 above this block and inverts the order the design
   sets. Hidden only where this block is present; the plugin suppresses it at
   source on the calculator pages. */
body:has(.ch-home) .page-header{display:none}

.ch-hero{background:var(--ch-surface);border-bottom:1px solid var(--ch-line);
 padding:clamp(36px,6vw,66px) 0 clamp(32px,5vw,56px)}
.ch-hero__inner{display:flex;flex-direction:column;align-items:center;gap:22px;text-align:center}
.ch-eyebrow{font-family:var(--ch-display);font-size:13px;font-weight:600;letter-spacing:.1em;
 text-transform:uppercase;color:var(--ch-teal);margin:0}
.ch-home h1{margin:0;font-family:var(--ch-display);font-size:clamp(32px,5.2vw,54px);font-weight:700;
 line-height:1.1;letter-spacing:-.03em;max-width:800px}
.ch-lede{margin:0;font-size:clamp(16px,2vw,19px);line-height:1.6;color:var(--ch-muted);max-width:620px}

.ch-search{position:relative;width:min(640px,100%)}
.ch-search__box{display:flex;align-items:center;gap:12px;min-height:60px;background:var(--ch-paper);
 border:1px solid var(--ch-line-strong);border-radius:12px;padding:8px 10px 8px 18px}
.ch-search__box:focus-within{border-color:var(--ch-teal);box-shadow:0 0 0 3px rgba(14,110,99,.15)}
.ch-search input{flex:1 1 auto;min-width:0;border:0;outline:0;background:transparent;
 font-family:var(--ch-body);font-size:17px;color:var(--ch-ink)}
.ch-search button{min-height:44px;padding:0 20px;background:var(--ch-teal);color:#fff;border:0;
 border-radius:9px;font-family:var(--ch-display);font-size:15px;font-weight:600;cursor:pointer}
.ch-search button:hover{background:#0B5C53}
.ch-results{position:absolute;z-index:40;top:calc(100% + 8px);left:0;right:0;background:var(--ch-surface);
 border:1px solid var(--ch-line);border-radius:12px;box-shadow:0 18px 40px rgba(20,24,29,.13);
 padding:6px;margin:0;list-style:none;text-align:left;max-height:340px;overflow:auto}
.ch-results[hidden]{display:none}
.ch-results a{display:block;padding:10px 12px;border-radius:8px;color:var(--ch-ink);font-size:15px}
.ch-results a:hover,.ch-results a:focus{background:var(--ch-teal-soft);color:var(--ch-ink)}
.ch-results small{display:block;color:var(--ch-muted);font-size:13px}
.ch-results__empty{padding:12px;color:var(--ch-muted);font-size:15px}

.ch-chips{display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:center}
.ch-chips span{font-size:14px;color:var(--ch-muted)}
.ch-chip{min-height:36px;padding:0 14px;display:inline-flex;align-items:center;background:var(--ch-sunken);
 border-radius:18px;font-size:14px;font-weight:600;color:var(--ch-ink)}
.ch-chip:hover{background:var(--ch-teal-soft);color:var(--ch-ink)}

.ch-section{padding:clamp(32px,4vw,46px) 0 0}
.ch-section__head{display:flex;align-items:flex-end;gap:20px;flex-wrap:wrap;margin-bottom:20px}
.ch-section__head div{flex:1 1 320px;display:flex;flex-direction:column;gap:6px}
.ch-home h2{margin:0;font-family:var(--ch-display);font-size:clamp(24px,3vw,30px);font-weight:600;letter-spacing:-.01em}
.ch-section__head p{margin:0;font-size:16px;color:var(--ch-muted)}
.ch-section__head a{font-size:15px;font-weight:600}

.ch-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:16px}
.ch-card{background:var(--ch-surface);border:1px solid var(--ch-line);border-radius:13px;padding:20px;
 display:flex;flex-direction:column;gap:10px;min-height:128px;color:inherit}
.ch-card:hover{border-color:var(--ch-teal);box-shadow:0 8px 22px rgba(20,24,29,.07);color:inherit}
.ch-card__icon{width:38px;height:38px;border-radius:9px;background:var(--ch-teal-soft);display:flex;
 align-items:center;justify-content:center;flex:0 0 auto}
.ch-card__name{font-family:var(--ch-display);font-size:16px;font-weight:600;line-height:1.3;color:var(--ch-ink)}
.ch-card__blurb{flex:1 1 auto;font-size:13px;line-height:1.5;color:var(--ch-muted)}
.ch-card__count{font-size:12px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--ch-teal)}

.ch-promo{margin-top:28px;background:var(--ch-surface);border:1px solid var(--ch-line);
 border-radius:13px;padding:22px 24px}
.ch-promo h2{font-size:18px}
.ch-promo p{margin:4px 0 16px;font-size:14px;color:var(--ch-muted)}
.ch-promo ul{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px 20px}
.ch-promo li{font-size:15px;font-weight:600;line-height:1.4}

.ch-values{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px;
 margin:28px 0 clamp(40px,6vw,64px)}
.ch-value{background:var(--ch-surface);border:1px solid var(--ch-line);border-radius:13px;padding:24px;
 display:flex;flex-direction:column;gap:9px}
.ch-value h3{margin:0;font-family:var(--ch-display);font-size:18px;font-weight:600}
.ch-value p{margin:0;font-size:15px;line-height:1.6;color:var(--ch-muted)}

@media (max-width:1100px){.ch-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media (max-width:820px){.ch-values{grid-template-columns:1fr}
 .ch-promo ul{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:620px){.ch-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
 /* The search stacks on a phone. The basis stays shrinkable, because a
    non-shrinking 100% basis beside the button is wider than the box and
    drags the whole page sideways. */
 .ch-search__box{flex-wrap:wrap;padding:12px;gap:8px}
 .ch-search__box>svg{display:none}
 .ch-search input{flex:1 1 100%;min-width:0;order:-1}
 .ch-search button{flex:1 1 100%}
 .ch-promo ul{grid-template-columns:1fr}}
@media (max-width:400px){.ch-grid{grid-template-columns:1fr}}
"""

SCRIPT = """
(function () {
  var root = document.getElementById('ch-home');
  if (!root) { return; }
  var data = JSON.parse(document.getElementById('ch-index').textContent);
  var input = root.querySelector('#ch-q');
  var list = root.querySelector('#ch-results');
  var form = root.querySelector('#ch-form');

  /* Ranks a name against the typed text: a prefix match beats a word-start
     match, which beats a match anywhere, so "per" puts Percentage first. */
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


def esc(text):
    return html.escape(text, quote=True)


INDEX_CSS = """
.ch-index{padding:clamp(28px,4vw,44px) 0 clamp(40px,6vw,64px)}
.ch-index__head{display:flex;flex-direction:column;gap:10px;margin-bottom:28px;max-width:720px}
.ch-index__head p{margin:0;font-size:17px;line-height:1.6;color:var(--ch-muted)}
.ch-jump{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:32px}
.ch-jump a{min-height:34px;padding:0 13px;display:inline-flex;align-items:center;background:var(--ch-sunken);
 border-radius:17px;font-size:14px;font-weight:600;color:var(--ch-ink)}
.ch-jump a:hover{background:var(--ch-teal-soft);color:var(--ch-ink)}
.ch-block{background:var(--ch-surface);border:1px solid var(--ch-line);border-radius:13px;padding:24px;margin-bottom:18px;scroll-margin-top:90px}
.ch-block__head{display:flex;align-items:baseline;gap:14px;flex-wrap:wrap;margin-bottom:6px}
.ch-block h2{font-size:22px}
.ch-block__count{font-size:12px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--ch-teal)}
.ch-block>p{margin:0 0 16px;font-size:15px;line-height:1.6;color:var(--ch-muted);max-width:760px}
.ch-block ul{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:9px 22px}
.ch-block li{font-size:15px;line-height:1.45}
@media (max-width:1100px){.ch-block ul{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media (max-width:820px){.ch-block ul{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:560px){.ch-block ul{grid-template-columns:1fr}}
"""


def build_index(inventory, base='/'):
    """The full A-to-Z hub the header links to. It exists because one page that
    links to all 105 gives every calculator a second internal link, and a
    visitor who does not know the category name still has somewhere to look."""
    total = sum(len(c['items']) for c in inventory)
    parts = ['<!-- wp:html -->',
             '<div class="ch-home" id="ch-home">',
             '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?'
             'family=Space+Grotesk:wght@500;600;700&amp;family=Source+Sans+3:wght@400;600&amp;display=swap">',
             '<style>' + CSS.strip() + INDEX_CSS.strip() + '</style>',
             '<section class="ch-index"><div class="ch-wrap">']
    parts.append('<div class="ch-index__head"><h1>All %d calculators</h1>'
                 '<p>Every tool on the site, grouped by the job it does. Each one runs in your '
                 'browser, shows the formula it used and needs no account.</p></div>' % total)
    parts.append('<nav class="ch-jump" aria-label="Jump to a category">')
    for cat in inventory:
        parts.append('<a href="#%s">%s</a>' % (cat['slug'], esc(cat['h1'])))
    parts.append('</nav>')
    for cat in inventory:
        count = len(cat['items'])
        parts.append('<section class="ch-block" id="%s">' % cat['slug'])
        parts.append('<div class="ch-block__head"><h2><a href="%s%s/">%s</a></h2>'
                     '<span class="ch-block__count">%d calculator%s</span></div>'
                     % (base, cat['slug'], esc(cat['h1']), count, '' if count == 1 else 's'))
        if cat.get('desc'):
            parts.append('<p>%s</p>' % esc(cat['desc']))
        parts.append('<ul>')
        for item in sorted(cat['items'], key=lambda i: i['name']):
            parts.append('<li><a href="%s%s/%s/">%s</a></li>'
                         % (base, cat['slug'], item['slug'], esc(item['name'])))
        parts.append('</ul></section>')
    parts.append('</div></section></div>')
    parts.append('<!-- /wp:html -->')
    return '\n'.join(parts)


def build(inventory, base='/'):
    by_slug = {}
    for cat in inventory:
        for item in cat['items']:
            by_slug[item['slug']] = (item, cat)

    missing = [s for s, _ in POPULAR if s not in by_slug]
    if missing:
        raise SystemExit('Popular chips point at calculators that do not exist: ' + ', '.join(missing))
    unknown = [c['slug'] for c in inventory if c['slug'] not in CATEGORY_ART]
    if unknown:
        raise SystemExit('No icon or blurb for category: ' + ', '.join(unknown))

    total = sum(len(c['items']) for c in inventory)

    # Wrapped as a Custom HTML block for two reasons: the block editor leaves
    # it alone rather than reflowing the markup, and WordPress drops wpautop
    # from the_content as soon as a post contains blocks, which stops stray
    # paragraph tags being injected into the grid.
    parts = ['<!-- wp:html -->',
             '<div class="ch-home" id="ch-home">',
             # Same Google Fonts URL the plugin registers, so if the plugin
             # later loads it site-wide the browser serves one request.
             '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?'
             'family=Space+Grotesk:wght@500;600;700&amp;family=Source+Sans+3:wght@400;600&amp;display=swap">',
             '<style>' + CSS.strip() + '</style>']

    # Hero
    parts.append('<section class="ch-hero"><div class="ch-wrap"><div class="ch-hero__inner">')
    parts.append('<p class="ch-eyebrow">%d tools, ten use cases, no sign-up</p>' % total)
    parts.append('<h1>A calculator for everything in life</h1>')
    parts.append('<p class="ch-lede">Mortgages, macros, percentages, paint. Type your numbers in and '
                 'read the answer, because working it out yourself was never the interesting part.</p>')
    parts.append(
        '<form class="ch-search" id="ch-form" role="search" autocomplete="off">'
        '<div class="ch-search__box">'
        '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#5A6472" stroke-width="2" '
        'stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle>'
        '<line x1="16.5" y1="16.5" x2="21" y2="21"></line></svg>'
        '<label class="screen-reader-text" for="ch-q" style="position:absolute;width:1px;height:1px;'
        'overflow:hidden;clip:rect(0 0 0 0)">Search calculators</label>'
        '<input id="ch-q" type="search" placeholder="Try mortgage, BMI, percentage, tip">'
        '<button type="submit">Search</button>'
        '</div>'
        '<ul class="ch-results" id="ch-results" hidden></ul>'
        '</form>')
    chips = ['<span>Most used today</span>']
    for slug, label in POPULAR:
        item, cat = by_slug[slug]
        chips.append('<a class="ch-chip" href="%s%s/%s/">%s</a>' % (base, cat['slug'], slug, esc(label)))
    parts.append('<div class="ch-chips">' + ''.join(chips) + '</div>')
    parts.append('</div></div></section>')

    # Category grid
    parts.append('<section class="ch-section"><div class="ch-wrap">')
    parts.append('<div class="ch-section__head"><div>'
                 '<h2>Browse by use case</h2>'
                 '<p>Ten categories, organised the way people actually search.</p>'
                 '</div></div>')
    parts.append('<div class="ch-grid">')
    for cat in inventory:
        icon, blurb = CATEGORY_ART[cat['slug']]
        count = len(cat['items'])
        parts.append(
            '<a class="ch-card" href="%s%s/">'
            '<span class="ch-card__icon"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" '
            'stroke="#0E6E63" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" '
            'aria-hidden="true"><path d="%s"></path></svg></span>'
            '<span class="ch-card__name">%s</span>'
            '<span class="ch-card__blurb">%s</span>'
            '<span class="ch-card__count">%d calculator%s</span>'
            '</a>' % (base, cat['slug'], icon, esc(cat['h1']), esc(blurb), count, '' if count == 1 else 's'))
    parts.append('</div>')

    # Where the leaderboard advert sits on the artboard, carrying real links
    # until a paid unit fills it, because an empty box earns nothing and an
    # internal link at least feeds the pages it points at.
    parts.append('<div class="ch-promo"><h2>Most used this week</h2>'
                 '<p>The pages people open first, if you would rather skip the browse.</p><ul>')
    for slug, label in POPULAR:
        item, cat = by_slug[slug]
        parts.append('<li><a href="%s%s/%s/">%s</a></li>' % (base, cat['slug'], slug, esc(item['name'])))
    parts.append('</ul></div>')

    # Value cards
    parts.append('<div class="ch-values">')
    for title, body in VALUE_CARDS:
        parts.append('<div class="ch-value"><h3>%s</h3><p>%s</p></div>' % (esc(title), esc(body)))
    parts.append('</div>')
    parts.append('</div></section>')

    index = [{'n': item['name'], 'u': '%s%s/%s/' % (base, cat['slug'], item['slug']), 'c': cat['h1']}
             for cat in inventory for item in cat['items']]
    parts.append('<script type="application/json" id="ch-index">%s</script>'
                 % json.dumps(index, separators=(',', ':')).replace('</', '<\\/'))
    parts.append('<script>' + SCRIPT.strip() + '</script>')
    parts.append('</div>')
    parts.append('<!-- /wp:html -->')
    return '\n'.join(parts)


if __name__ == '__main__':
    src, out_dir = sys.argv[1], sys.argv[2].rstrip('/')
    with open(src) as fh:
        inv = json.load(fh)
    for name, markup in (('home.html', build(inv)), ('all-calculators.html', build_index(inv))):
        with open(out_dir + '/' + name, 'w') as fh:
            fh.write(markup)
        print('%s/%s written, %d bytes' % (out_dir, name, len(markup)))
