/*
 * Drives a built preview page in a real browser and reports what the panel
 * says before and after somebody types.
 *
 * This is the only check that covers a JSON calculator end to end. The parity
 * test proves the formula produces the right numbers, and the render test
 * proves the markup comes out, but neither of them starts a Web Worker, and
 * the worker is where a JSON calculator actually computes. A page served from
 * file:// cannot start one at all, so the preview is served over HTTP here for
 * the same reason a browser insists on it.
 *
 * Usage: node tools/check-in-browser.js <preview-dir> <slug> [field=value ...]
 *
 * Every field named on the command line is typed into in turn, and the answer
 * is read after each one, so a calculator that responds to its first field and
 * ignores its second shows up as two lines rather than one.
 */
const { spawn } = require('child_process');
const http = require('http');
const fs = require('fs');
const path = require('path');
const net = require('net');
const crypto = require('crypto');

const CHROME = '/opt/pw-browsers/chromium-1194/chrome-linux/chrome';
const PORT = 9222;
const SERVE = 8731;

/* Either a built preview directory plus a slug, or a live URL. The live check
   is the one that catches what the preview cannot: a theme that loads scripts
   differently, a caching plugin that combines or defers them, and a CDN
   serving a stale worker beside a fresh runtime. */
const live = process.argv[2].startsWith('http') ? process.argv[2] : '';
const dir = live ? '' : process.argv[2];
const slug = live ? live.replace(/\/$/, '').split('/').pop() : process.argv[3];
const edits = process.argv.slice(live ? 3 : 4).map(a => {
  const i = a.indexOf('=');
  return { id: a.slice(0, i), value: a.slice(i + 1) };
});

if (!slug || (!dir && !live)) {
  console.error('Usage: node tools/check-in-browser.js <preview-dir> <slug> [field=value ...]');
  console.error('   or: node tools/check-in-browser.js <live-url> [field=value ...]');
  process.exit(2);
}

const TYPES = { '.html': 'text/html', '.css': 'text/css', '.js': 'text/javascript', '.json': 'application/json' };

const server = http.createServer((req, res) => {
  const rel = decodeURIComponent(req.url.split('?')[0]).replace(/^\/+/, '');
  const file = path.join(dir, rel);

  if (!file.startsWith(path.resolve(dir))) { res.writeHead(403); res.end(); return; }

  fs.readFile(file, (err, body) => {
    if (err) { res.writeHead(404); res.end('not found'); return; }
    res.writeHead(200, { 'Content-Type': TYPES[path.extname(file)] || 'application/octet-stream' });
    res.end(body);
  });
});

function get(path) {
  return new Promise((res, rej) => {
    http.get({ host: '127.0.0.1', port: PORT, path }, r => {
      let b = ''; r.on('data', c => b += c); r.on('end', () => res(JSON.parse(b)));
    }).on('error', rej);
  });
}

const sleep = ms => new Promise(r => setTimeout(r, ms));

const chrome = spawn(CHROME, ['--headless', '--no-sandbox', '--disable-gpu',
  '--remote-debugging-port=' + PORT, '--remote-allow-origins=*', 'about:blank']);

(async () => {
  if (!live) { await new Promise(r => server.listen(SERVE, '127.0.0.1', r)); }

  let targets;
  for (let i = 0; i < 40; i++) {
    try { targets = await get('/json/list'); if (targets.length) break; } catch (e) {}
    await sleep(250);
  }

  const ws = targets.find(t => t.webSocketDebuggerUrl).webSocketDebuggerUrl;

  /* A minimal DevTools client, the same one tools/check-overflow.js uses: the
     protocol is JSON over a websocket and pulling in a library for five
     message types is not worth the dependency. */
  const u = new URL(ws);
  const sock = net.connect(Number(u.port), u.hostname);
  const key = crypto.randomBytes(16).toString('base64');
  let handshaken = false, buf = Buffer.alloc(0);
  const pending = new Map();
  const consoleLines = [];
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
        else if (m.method === 'Runtime.consoleAPICalled' && ['error', 'warning'].includes(m.params.type)) {
          consoleLines.push(m.params.type + ': ' + m.params.args.map(a => a.value || a.description || '').join(' '));
        }
        else if (m.method === 'Runtime.exceptionThrown') {
          consoleLines.push('exception: ' + (m.params.exceptionDetails.exception || {}).description);
        }
      } catch (e) {}
    }
  });

  await sleep(400);
  await send('Page.enable');
  await send('Runtime.enable');
  await send('Page.navigate', { url: live || `http://127.0.0.1:${SERVE}/${slug}.html` });
  await sleep(live ? 3500 : 1400);

  const READ = `(() => {
    const root = document.querySelector('[data-calcr-slug]');
    if (!root) { return { error: 'no calculator on the page' }; }
    const t = s => { const el = root.querySelector(s); return el ? el.textContent.trim() : null; };
    return {
      awaiting: root.classList.contains('calcr--awaiting'),
      sandboxed: !!root.querySelector('[data-calcr-formula]'),
      label: t('[data-calcr-primary-label]'),
      value: t('[data-calcr-primary-value]'),
      sub: t('[data-calcr-primary-sub]'),
      rows: Array.from(root.querySelectorAll('[data-calcr-rows] li')).map(li => li.textContent.trim()),
      bar: Array.from(root.querySelectorAll('[data-calcr-bar] span')).map(s => s.style.width),
      note: t('[data-calcr-note]'),
    };
  })()`;

  async function read() {
    const r = await send('Runtime.evaluate', { returnByValue: true, expression: READ });
    return r.result.value;
  }

  function show(title, state) {
    console.log(title);
    if (state.error) { console.log('  ' + state.error); return; }
    console.log(`  ${state.label}: ${state.value}${state.sub ? '  (' + state.sub + ')' : ''}`);
    state.rows.forEach(r => console.log('    ' + r));
    if (state.bar.length) { console.log('    bar: ' + state.bar.join(' / ')); }
  }

  const first = await read();
  console.log(`${slug}: ${first.sandboxed ? 'formula runs in the sandbox worker' : 'formula comes from the shipped bundle'}`);
  show('\nas the server rendered it' + (first.awaiting ? ' (worked example, nothing entered yet)' : ''), first);

  for (const edit of edits) {
    await send('Runtime.evaluate', {
      returnByValue: true,
      expression: `(() => {
        const el = document.querySelector('[data-calcr-input="${edit.id}"]');
        if (!el) { return 'no field called ${edit.id}'; }
        el.value = ${JSON.stringify(edit.value)};
        el.dispatchEvent(new Event('input', { bubbles: true }));
        el.dispatchEvent(new Event('change', { bubbles: true }));
        return 'ok';
      })()`
    });

    await sleep(500);
    show(`\nafter typing ${edit.id} = ${edit.value}`, await read());
  }

  /*
   * The lockdown, tested rather than assumed.
   *
   * The PHP validator refuses a formula that names fetch, so nothing published
   * through the route can reach this code path. That is exactly why it is
   * worth probing directly: the two layers are meant to be independent, and a
   * seal that only works because the layer in front of it never lets anything
   * through is not a seal. The worker is driven here the way a compromised
   * page would drive it, with a formula the validator would have refused.
   */
  if (first.sandboxed) {
    const probe = await send('Runtime.evaluate', {
      awaitPromise: true,
      returnByValue: true,
      expression: `new Promise(resolve => {
        const w = new Worker(window.CalculatorrConfig.sandboxUrl);
        const names = ['fetch', 'XMLHttpRequest', 'importScripts', 'WebSocket', 'indexedDB', 'localStorage', 'Worker', 'navigator'];
        let survivors = null;
        w.onmessage = e => {
          if (e.data.ready) {
            survivors = e.data.survivors;
            w.postMessage({
              id: 1,
              source: 'var found = []; ' + JSON.stringify(names) +
                '.forEach(function (n) { if (typeof self[n] !== "undefined" ) { found.push(n); } });' +
                ' return { label: "reachable", value: found.length ? found.join(", ") : "none" };',
              values: {}
            });
            return;
          }
          resolve({ survivors: survivors, ok: e.data.ok, reachable: e.data.result ? e.data.result.value : null, error: e.data.error });
          w.terminate();
        };
        setTimeout(() => resolve({ timeout: true }), 4000);
      })`
    });

    const v = probe.result.value || {};
    console.log('\nsandbox seal');
    console.log('  globals the worker could not remove: ' + (v.survivors && v.survivors.length ? v.survivors.join(', ') : 'none'));
    console.log('  globals a formula can still reach:   ' + (v.reachable === null ? 'the probe did not run: ' + (v.error || 'timed out') : v.reachable));
  }

  if (consoleLines.length) {
    console.log('\nconsole:');
    consoleLines.forEach(l => console.log('  ' + l));
  } else {
    console.log('\nconsole: clean');
  }

  sock.destroy();
  chrome.kill();
  if (!live) { server.close(); }
  process.exit(0);
})();
