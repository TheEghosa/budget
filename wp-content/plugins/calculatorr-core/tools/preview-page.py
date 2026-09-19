#!/usr/bin/env python3
"""Wraps a generated content/*.html block file in a minimal page so it can be
rendered headlessly with the real tokens and the real fonts. The wrapper is
deliberately thin, because anything it adds is something the live page does not
have and would therefore make the comparison lie."""
import re, sys, os

PLUGIN = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OUT = os.environ.get('CALCR_PREVIEW_DIR', os.path.join(PLUGIN, '.preview'))

FONTS = """@font-face{font-family:'Source Sans 3';font-style:normal;font-weight:400;src:url('fonts/SourceSans3-400.ttf') format('truetype');font-display:swap;}
@font-face{font-family:'Source Sans 3';font-style:normal;font-weight:600;src:url('fonts/SourceSans3-600.ttf') format('truetype');font-display:swap;}
@font-face{font-family:'Space Grotesk';font-style:normal;font-weight:500;src:url('fonts/SpaceGrotesk-500.ttf') format('truetype');font-display:swap;}
@font-face{font-family:'Space Grotesk';font-style:normal;font-weight:600;src:url('fonts/SpaceGrotesk-600.ttf') format('truetype');font-display:swap;}
@font-face{font-family:'Space Grotesk';font-style:normal;font-weight:700;src:url('fonts/SpaceGrotesk-700.ttf') format('truetype');font-display:swap;}
body{margin:0;background:var(--c-bg-page);color:var(--c-text-primary);font-family:'Source Sans 3',system-ui,sans-serif}"""

def build(src, out, theme='dark'):
    block = open(os.path.join(PLUGIN, 'content', src), encoding='utf-8').read()
    # The block file is WordPress block markup; the comment delimiters are inert
    # in a browser but stripping them keeps the dump readable.
    block = re.sub(r'<!--\s*/?wp:html\s*-->', '', block)
    tokens = open(os.path.join(PLUGIN, 'assets/css/tokens.css'), encoding='utf-8').read()
    site = open(os.path.join(PLUGIN, 'assets/css/site.css'), encoding='utf-8').read()

    # The fonts are loaded from disk when a copy sits beside the output, because
    # a preview that has to reach the network measures the network as much as the
    # page. When there is no local copy the CDN link is used instead, which is
    # what the live page does anyway.
    if os.path.isdir(os.path.join(OUT, 'fonts')):
        faces, link = '<style>%s</style>' % FONTS, ''
    else:
        faces = ''
        link = ('<link rel="stylesheet" href="https://fonts.googleapis.com/css2?'
                'family=Space+Grotesk:wght@500;600;700&family=Source+Sans+3:wght@400;500;600;700&display=swap">')

    html = ('<!doctype html><html lang="en" data-theme="%s"><head><meta charset="utf-8">'
            # Without this Chrome lays a mobile emulation out at its 980px
            # fallback width, so every narrow measurement is a lie.
            '<meta name="viewport" content="width=device-width, initial-scale=1">\n'
            '%s%s<style>%s\n%s</style></head><body>'
            # The real page nests the block as main.site-main > div.page-content >
            # div.ch, and site.css keys its container rules off exactly that, so
            # the wrapper has to reproduce it or the width test proves nothing.
            '<main id="content" class="site-main page">'
            '<div class="page-content">%s</div></main></body></html>'
            % (theme, faces, link, tokens, site, block))
    os.makedirs(OUT, exist_ok=True)
    path = os.path.join(OUT, out)
    open(path, 'w', encoding='utf-8').write(html)
    print('%s -> %s (%d bytes)' % (src, out, len(html)))
    return path

if __name__ == '__main__':
    build('home.html', 'chk-home.html', 'dark')
    build('home.html', 'chk-home-light.html', 'light')
    build('all-calculators.html', 'chk-hub.html', 'dark')
