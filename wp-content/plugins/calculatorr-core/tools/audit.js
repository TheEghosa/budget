/*
 * A contrarian audit of a rendered page.
 *
 * Everything here is measured in a real browser against the real stylesheets,
 * because the interesting failures on this site have all been ones that a
 * reading of the source would have called fine: a theme rule out-specifying
 * ours, a container query losing to source order, a percentage margin
 * disagreeing with a viewport unit by the width of a scrollbar.
 *
 * It reports rather than judges. Every finding is a place to look, and the
 * count next to each is how many elements are involved, so a rule that fires
 * two hundred times is a systematic problem and one that fires once is a typo.
 *
 * Usage: node tools/audit.js <file.html> [widths] [--json]
 */
const { spawn } = require('child_process');
const http = require('http'), net = require('net'), crypto = require('crypto');

const CHROME = '/opt/pw-browsers/chromium-1194/chrome-linux/chrome';
const PORT = 9400 + (process.pid % 300);
const file = process.argv[2];
const widths = (process.argv[3] || '390,1440').split(',').map(Number);
const asJson = process.argv.includes('--json');

const sleep = ms => new Promise(r => setTimeout(r, ms));
const get = p => new Promise((res, rej) => http.get({ host: '127.0.0.1', port: PORT, path: p },
  r => { let b = ''; r.on('data', c => b += c); r.on('end', () => res(JSON.parse(b))); }).on('error', rej));

const AUDIT = `(() => {
  const out = { findings: [], stats: {} };
  const add = (id, severity, what, nodes) => {
    if (!nodes || !nodes.length) return;
    out.findings.push({ id, severity, what, count: nodes.length, sample: nodes.slice(0, 4) });
  };
  const name = el => {
    const t = el.tagName.toLowerCase();
    const c = String(el.className || '').split(' ').filter(Boolean).slice(0,2).join('.');
    const txt = (el.textContent || '').trim().slice(0, 28);
    return t + (c ? '.' + c : '') + (txt ? ' "' + txt + '"' : '');
  };
  const vis = el => {
    const s = getComputedStyle(el);
    if (s.display === 'none' || s.visibility === 'hidden' || Number(s.opacity) === 0) return false;
    const r = el.getBoundingClientRect();
    return r.width > 0 && r.height > 0;
  };

  /* ---- contrast ---------------------------------------------------------- */
  const parse = c => {
    const m = c.match(/rgba?\\(([^)]+)\\)/);
    if (!m) return null;
    const p = m[1].split(',').map(Number);
    return { r: p[0], g: p[1], b: p[2], a: p.length > 3 ? p[3] : 1 };
  };
  const lum = ({r,g,b}) => {
    const f = v => { v /= 255; return v <= 0.03928 ? v/12.92 : Math.pow((v+0.055)/1.055, 2.4); };
    return 0.2126*f(r) + 0.7152*f(g) + 0.0722*f(b);
  };
  const over = (fg, bg) => ({ r: Math.round(fg.r*fg.a + bg.r*(1-fg.a)),
                              g: Math.round(fg.g*fg.a + bg.g*(1-fg.a)),
                              b: Math.round(fg.b*fg.a + bg.b*(1-fg.a)), a: 1 });
  const bgOf = el => {
    let n = el;
    while (n && n !== document.documentElement) {
      const c = parse(getComputedStyle(n).backgroundColor);
      if (c && c.a > 0.85) return c;
      n = n.parentElement;
    }
    return parse(getComputedStyle(document.body).backgroundColor) || { r:255,g:255,b:255,a:1 };
  };
  const ratio = (a, b) => { const L1 = lum(a), L2 = lum(b); const hi = Math.max(L1,L2), lo = Math.min(L1,L2); return (hi+0.05)/(lo+0.05); };

  const lowContrast = [];
  document.querySelectorAll('p,span,a,li,td,th,label,h1,h2,h3,h4,h5,h6,button,small,strong,em,div').forEach(el => {
    if (!vis(el)) return;
    /* Only elements that own their text, so a wrapper is not blamed for a child. */
    const own = [...el.childNodes].some(n => n.nodeType === 3 && n.textContent.trim().length > 1);
    if (!own) return;
    const s = getComputedStyle(el);
    const fg0 = parse(s.color); if (!fg0) return;
    const bg = bgOf(el);
    const fg = fg0.a < 1 ? over(fg0, bg) : fg0;
    const px = parseFloat(s.fontSize);
    const bold = (parseInt(s.fontWeight, 10) || 400) >= 700;
    const large = px >= 24 || (bold && px >= 18.66);
    const need = large ? 3 : 4.5;
    const r = ratio(fg, bg);
    if (r < need) lowContrast.push(name(el) + '  ' + r.toFixed(2) + ':1 (needs ' + need + ') ' + s.color + ' on rgb(' + bg.r + ',' + bg.g + ',' + bg.b + ')');
  });
  add('contrast', 'high', 'text below its WCAG AA contrast floor', lowContrast);

  /* ---- names and labels -------------------------------------------------- */
  const noName = [];
  document.querySelectorAll('a[href],button,[role="button"]').forEach(el => {
    if (!vis(el)) return;
    const txt = (el.textContent || '').trim();
    const aria = el.getAttribute('aria-label') || el.getAttribute('title') || '';
    const labelled = el.getAttribute('aria-labelledby');
    if (!txt && !aria && !labelled) noName.push(name(el) + ' ' + (el.outerHTML || '').slice(0, 70));
  });
  add('no-accessible-name', 'high', 'interactive element with no accessible name', noName);

  const unlabelled = [];
  document.querySelectorAll('input,select,textarea').forEach(el => {
    if (el.type === 'hidden' || !vis(el)) return;
    const id = el.id;
    const has = (id && document.querySelector('label[for="' + CSS.escape(id) + '"]'))
      || el.closest('label') || el.getAttribute('aria-label') || el.getAttribute('aria-labelledby');
    if (!has) unlabelled.push(name(el) + ' name=' + (el.name || '-'));
  });
  add('unlabelled-field', 'high', 'form control with no label', unlabelled);

  const noAlt = [];
  document.querySelectorAll('img').forEach(el => { if (!el.hasAttribute('alt')) noAlt.push(name(el) + ' src=' + (el.getAttribute('src')||'').slice(-40)); });
  add('img-no-alt', 'medium', 'image with no alt attribute', noAlt);

  /* ---- structure --------------------------------------------------------- */
  const ids = {}; const dupes = [];
  document.querySelectorAll('[id]').forEach(el => { const i = el.id; ids[i] = (ids[i]||0)+1; if (ids[i] === 2) dupes.push(i); });
  add('duplicate-id', 'medium', 'id used more than once', dupes);

  const heads = [...document.querySelectorAll('h1,h2,h3,h4,h5,h6')].filter(vis);
  out.stats.h1Count = heads.filter(h => h.tagName === 'H1').length;
  const skips = []; let prev = 0;
  heads.forEach(h => { const l = Number(h.tagName[1]); if (prev && l > prev + 1) skips.push(name(h) + ' (h' + prev + ' -> h' + l + ')'); prev = l; });
  add('heading-skip', 'low', 'heading level skipped', skips);

  const posTab = [...document.querySelectorAll('[tabindex]')].filter(el => Number(el.getAttribute('tabindex')) > 0).map(name);
  add('positive-tabindex', 'medium', 'positive tabindex, which reorders the tab sequence', posTab);

  /* ---- touch targets ----------------------------------------------------- */
  const small = [];
  document.querySelectorAll('a[href],button,input[type="checkbox"],input[type="radio"],select,[role="button"]').forEach(el => {
    if (!vis(el)) return;
    const r = el.getBoundingClientRect();
    /* Links inside a paragraph are inline text, not targets, so they are out. */
    if (el.tagName === 'A' && getComputedStyle(el).display === 'inline') return;
    if (r.width < 24 || r.height < 24) small.push(name(el) + '  ' + Math.round(r.width) + 'x' + Math.round(r.height));
  });
  add('small-target', 'medium', 'tap target under 24x24 CSS px', small);

  /*
   * Focus visibility is deliberately not checked here.
   *
   * It cannot be measured without pressing the key. Programmatic .focus() does
   * not put an element into :focus-visible, and focus({ focusVisible: true })
   * is not honoured in this build, so both report a page styled entirely in
   * :focus-visible as having no focus styling at all. That produced a
   * forty-three item finding that was wrong in every particular. Real Tab
   * presses live in tools/tabtest.js, which is the only honest version of this
   * check, and a check that cries wolf is worse than no check.
   */

  /* ---- live regions and motion ------------------------------------------- */
  out.stats.liveRegions = document.querySelectorAll('[aria-live]').length;
  out.stats.landmarks = document.querySelectorAll('main,nav,header,footer,aside,[role="main"]').length;
  out.stats.mainCount = document.querySelectorAll('main,[role="main"]').length;

  /* ---- layout ------------------------------------------------------------ */
  const d = document.documentElement;
  out.stats.scrollWidth = d.scrollWidth;
  out.stats.clientWidth = d.clientWidth;
  const spill = [];
  document.querySelectorAll('.calcr *, .ch *, .calcr-hub *').forEach(el => {
    if (el.children.length || !vis(el)) return;
    const r = el.getBoundingClientRect();
    if (r.right > d.clientWidth + 1 || r.left < -1) spill.push(name(el) + ' [' + Math.round(r.left) + '..' + Math.round(r.right) + ']');
  });
  add('clipped-content', 'high', 'leaf element outside the viewport', spill);

  /* Text that cannot fit its box is text somebody cannot read. */
  const truncated = [];
  document.querySelectorAll('button, .calcr-field__label, .ch-cat__name, .calcr-hub__name, .calcr__row-label').forEach(el => {
    if (!vis(el)) return;
    if (el.scrollWidth > el.clientWidth + 2 && getComputedStyle(el).overflow !== 'visible') {
      truncated.push(name(el) + ' ' + el.scrollWidth + '>' + el.clientWidth);
    }
  });
  add('overflowing-text', 'medium', 'text wider than the box that clips it', truncated);

  return out;
})()`;

const chrome = spawn(CHROME, ['--headless', '--no-sandbox', '--disable-gpu', '--hide-scrollbars',
  '--remote-debugging-port=' + PORT, 'about:blank']);

(async () => {
  let t;
  for (let i = 0; i < 60; i++) { try { t = await get('/json/list'); if (t.length) break; } catch (e) {} await sleep(250); }
  const u = new URL(t.find(x => x.webSocketDebuggerUrl).webSocketDebuggerUrl);
  const sock = net.connect(Number(u.port), u.hostname);
  let hs = false, buf = Buffer.alloc(0), id = 0; const pending = new Map();
  const send = (m, p) => new Promise(r => {
    const msg = JSON.stringify({ id: ++id, method: m, params: p || {} }); pending.set(id, r);
    const pl = Buffer.from(msg), mask = crypto.randomBytes(4); let hd;
    if (pl.length < 126) hd = Buffer.from([0x81, 0x80 | pl.length]);
    else if (pl.length < 65536) { hd = Buffer.alloc(4); hd[0] = 0x81; hd[1] = 0xfe; hd.writeUInt16BE(pl.length, 2); }
    else { hd = Buffer.alloc(10); hd[0] = 0x81; hd[1] = 0xff; hd.writeBigUInt64BE(BigInt(pl.length), 2); }
    const mk = Buffer.alloc(pl.length); for (let i = 0; i < pl.length; i++) mk[i] = pl[i] ^ mask[i % 4];
    sock.write(Buffer.concat([hd, mask, mk]));
  });
  const logs = [];
  sock.write(`GET ${u.pathname} HTTP/1.1\r\nHost: ${u.host}\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Key: ${crypto.randomBytes(16).toString('base64')}\r\nSec-WebSocket-Version: 13\r\n\r\n`);
  sock.on('data', d => {
    buf = Buffer.concat([buf, d]);
    if (!hs) { const i = buf.indexOf('\r\n\r\n'); if (i < 0) return; buf = buf.slice(i + 4); hs = true; }
    while (buf.length >= 2) {
      let len = buf[1] & 0x7f, off = 2;
      if (len === 126) { len = buf.readUInt16BE(2); off = 4; } else if (len === 127) { len = Number(buf.readBigUInt64BE(2)); off = 10; }
      if (buf.length < off + len) return;
      const txt = buf.slice(off, off + len).toString(); buf = buf.slice(off + len);
      try { const m = JSON.parse(txt);
        if (m.method === 'Runtime.consoleAPICalled' && ['error','warning'].includes(m.params.type)) logs.push(m.params.type + ': ' + (m.params.args||[]).map(a=>a.value||a.description||'').join(' ').slice(0,120));
        if (m.method === 'Runtime.exceptionThrown') logs.push('exception: ' + (m.params.exceptionDetails.text||'') + ' ' + (m.params.exceptionDetails.exception?.description||'').slice(0,120));
        if (m.id && pending.has(m.id)) { pending.get(m.id)(m.result); pending.delete(m.id); }
      } catch (e) {}
    }
  });
  await sleep(400);
  await send('Page.enable'); await send('Runtime.enable');

  const report = {};
  for (const w of widths) {
    logs.length = 0;
    if (process.env.REDUCED) {
      await send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
    }
    await send('Emulation.setDeviceMetricsOverride', { width: w, height: Number(process.env.VH || 900), deviceScaleFactor: 1, mobile: w < 768 });
    await send('Page.navigate', { url: 'file://' + file });
    await sleep(1500);
    const r = await send('Runtime.evaluate', { returnByValue: true, expression: AUDIT });
    report[w] = r.result.value || { findings: [], stats: {} };
    report[w].console = logs.slice(0, 8);
  }

  if (asJson) { console.log(JSON.stringify(report, null, 1)); }
  else {
    for (const w of widths) {
      const rep = report[w];
      console.log('\n===== ' + file.split('/').pop() + ' @ ' + w + 'px =====');
      console.log('  h1s=' + rep.stats.h1Count + '  landmarks=' + rep.stats.landmarks + '  live=' + rep.stats.liveRegions +
                  '  scroll=' + rep.stats.scrollWidth + '/' + rep.stats.clientWidth);
      if (rep.console.length) rep.console.forEach(l => console.log('  CONSOLE  ' + l));
      if (!rep.findings.length) console.log('  no findings');
      rep.findings.sort((a,b) => ({high:0,medium:1,low:2}[a.severity] - {high:0,medium:1,low:2}[b.severity]));
      rep.findings.forEach(f => {
        console.log('  [' + f.severity.toUpperCase().padEnd(6) + '] ' + f.id + ' x' + f.count + '  ' + f.what);
        f.sample.forEach(s => console.log('            - ' + s));
      });
    }
  }
  chrome.kill(); process.exit(0);
})().catch(e => { console.error(e); chrome.kill(); process.exit(2); });
