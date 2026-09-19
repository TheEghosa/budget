/*
 * Horizontal overflow check for the generated home and hub pages.
 *
 * Usage: node tools/check-overflow.js <absolute path to a built preview> [widths]
 * Build the preview first with tools/preview-page.py, which wraps the generated
 * block in the same main.site-main > div.page-content chain the live theme uses,
 * because site.css keys its container rules off exactly that nesting.
 *
 * Measures horizontal overflow at a set of widths by driving headless Chrome
 * over the DevTools protocol. A screenshot cannot answer this question, because
 * headless Chrome refuses to size its window below 500px and silently scales
 * the result, which makes a page that fits look like a page that does not.
 * Device metrics override sets the layout viewport directly, so the numbers
 * below are the ones a phone would actually produce.
 */
const { spawn } = require('child_process');
const http = require('http');

const CHROME = '/opt/pw-browsers/chromium-1194/chrome-linux/chrome';
const PORT = 9222;
const file = process.argv[2];
const widths = (process.argv[3] || '320,360,390,414,768,1024,1280,1440').split(',').map(Number);

function get(path) {
  return new Promise((res, rej) => {
    http.get({ host: '127.0.0.1', port: PORT, path }, r => {
      let b = ''; r.on('data', c => b += c); r.on('end', () => res(JSON.parse(b)));
    }).on('error', rej);
  });
}

const chrome = spawn(CHROME, ['--headless', '--no-sandbox', '--disable-gpu',
  '--remote-debugging-port=' + PORT, '--remote-allow-origins=*', 'about:blank']);

const sleep = ms => new Promise(r => setTimeout(r, ms));

(async () => {
  let targets;
  for (let i = 0; i < 40; i++) {
    try { targets = await get('/json/list'); if (targets.length) break; } catch (e) {}
    await sleep(250);
  }
  const ws = targets.find(t => t.webSocketDebuggerUrl).webSocketDebuggerUrl;

  // A minimal DevTools client: the protocol is newline-free JSON over a
  // websocket, and pulling in a library for four message types is not worth it.
  const WebSocket = require('node:worker_threads') && null;
  const net = require('net');
  const crypto = require('crypto');
  const u = new URL(ws);
  const sock = net.connect(Number(u.port), u.hostname);
  const key = crypto.randomBytes(16).toString('base64');
  let handshaken = false, buf = Buffer.alloc(0);
  const pending = new Map();
  let id = 0;

  function send(method, params) {
    return new Promise(resolve => {
      const msg = JSON.stringify({ id: ++id, method, params: params || {} });
      pending.set(id, resolve);
      const payload = Buffer.from(msg);
      const mask = crypto.randomBytes(4);
      let header;
      if (payload.length < 126) header = Buffer.from([0x81, 0x80 | payload.length]);
      else { header = Buffer.alloc(4); header[0] = 0x81; header[1] = 0xfe; header.writeUInt16BE(payload.length, 2); }
      const masked = Buffer.alloc(payload.length);
      for (let i = 0; i < payload.length; i++) masked[i] = payload[i] ^ mask[i % 4];
      sock.write(Buffer.concat([header, mask, masked]));
    });
  }

  sock.write(`GET ${u.pathname} HTTP/1.1\r\nHost: ${u.host}\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Key: ${key}\r\nSec-WebSocket-Version: 13\r\n\r\n`);

  sock.on('data', d => {
    buf = Buffer.concat([buf, d]);
    if (!handshaken) {
      const i = buf.indexOf('\r\n\r\n');
      if (i < 0) return;
      buf = buf.slice(i + 4); handshaken = true;
    }
    while (buf.length >= 2) {
      let len = buf[1] & 0x7f, off = 2;
      if (len === 126) { len = buf.readUInt16BE(2); off = 4; }
      else if (len === 127) { len = Number(buf.readBigUInt64BE(2)); off = 10; }
      if (buf.length < off + len) return;
      const text = buf.slice(off, off + len).toString();
      buf = buf.slice(off + len);
      try {
        const m = JSON.parse(text);
        if (m.id && pending.has(m.id)) { pending.get(m.id)(m.result); pending.delete(m.id); }
      } catch (e) {}
    }
  });

  await new Promise(r => setTimeout(r, 400));
  await send('Page.enable');

  const rows = [];
  for (const w of widths) {
    await send('Emulation.setDeviceMetricsOverride',
      { width: w, height: Number(process.env.VH || 900), deviceScaleFactor: 1, mobile: w < 768 });
    await send('Page.navigate', { url: 'file://' + file });
    await new Promise(r => setTimeout(r, 900));
    const r = await send('Runtime.evaluate', {
      returnByValue: true,
      expression: `(() => {
        const d = document.documentElement;
        // The real question is whether the visitor can scroll sideways, not
        // what scrollWidth reports: the page is deliberately full-bleed with
        // width:100vw, which always exceeds clientWidth by the scrollbar, and
        // scrollWidth keeps reporting that even under overflow-x:hidden.
        d.scrollLeft = 9999;
        const scrollable = d.scrollLeft;
        d.scrollLeft = 0;
        const over = [];
        document.querySelectorAll('.ch *').forEach(el => {
          if (el.children.length) return;           // leaves only: a parent is wide because a child is
          const b = el.getBoundingClientRect();
          if (b.width > 0 && (b.right > d.clientWidth + 1 || b.left < -1)) {
            over.push(Math.round(b.right) + 'px  <' + el.tagName.toLowerCase() + ' class="' + (el.className || '') + '"> ' +
                      JSON.stringify((el.textContent || '').trim().slice(0, 40)));
          }
        });
        over.sort((a, b) => parseInt(b) - parseInt(a));
        return { scroll: d.scrollWidth, client: d.clientWidth, scrollable, worst: over.slice(0, 5) };
      })()`
    });
    const v = r.result.value;
    rows.push({ w, ...v });
  }

  let bad = 0;
  for (const r of rows) {
    const ok = r.scrollable === 0;
    if (!ok) bad++;
    console.log(`${String(r.w).padStart(5)}px  client ${String(r.client).padStart(5)}  scrollWidth ${String(r.scroll).padStart(5)}  sideways-scroll ${String(r.scrollable).padStart(4)}px  ${ok ? 'fits' : 'SCROLLS'}`);
    r.worst.forEach(s => console.log('           ' + s));
  }
  console.log(bad ? `\n${bad} width(s) overflow` : '\nno horizontal overflow at any width');
  chrome.kill();
  process.exit(bad ? 1 : 0);
})().catch(e => { console.error(e); chrome.kill(); process.exit(2); });
