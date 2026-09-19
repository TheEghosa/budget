/* Walks the page with real Tab keypresses and reports what each focused
   element actually draws. Keyboard focus is the only way to make :focus-visible
   behave the way it does for a person, so anything short of pressing the key
   measures something else. */
const { spawn } = require('child_process');
const http = require('http'), net = require('net'), crypto = require('crypto');
const CHROME = '/opt/pw-browsers/chromium-1194/chrome-linux/chrome';
const PORT = 9777;
const file = process.argv[2];
const STEPS = Number(process.argv[3] || 40);
const sleep = ms => new Promise(r => setTimeout(r, ms));
const get = p => new Promise((res, rej) => http.get({host:'127.0.0.1',port:PORT,path:p}, r => { let b=''; r.on('data',c=>b+=c); r.on('end',()=>res(JSON.parse(b))); }).on('error',rej));
const chrome = spawn(CHROME, ['--headless','--no-sandbox','--disable-gpu','--hide-scrollbars','--remote-debugging-port='+PORT,'about:blank']);
(async () => {
  let t; for (let i=0;i<60;i++){ try{ t=await get('/json/list'); if(t.length) break; }catch(e){} await sleep(250); }
  const u = new URL(t.find(x=>x.webSocketDebuggerUrl).webSocketDebuggerUrl);
  const sock = net.connect(Number(u.port), u.hostname);
  let hs=false, buf=Buffer.alloc(0), id=0; const pending=new Map();
  const send=(m,p)=>new Promise(r=>{ const msg=JSON.stringify({id:++id,method:m,params:p||{}}); pending.set(id,r);
    const pl=Buffer.from(msg), mask=crypto.randomBytes(4); let hd;
    if(pl.length<126) hd=Buffer.from([0x81,0x80|pl.length]);
    else if(pl.length<65536){ hd=Buffer.alloc(4); hd[0]=0x81; hd[1]=0xfe; hd.writeUInt16BE(pl.length,2); }
    else { hd=Buffer.alloc(10); hd[0]=0x81; hd[1]=0xff; hd.writeBigUInt64BE(BigInt(pl.length),2); }
    const mk=Buffer.alloc(pl.length); for(let i=0;i<pl.length;i++) mk[i]=pl[i]^mask[i%4];
    sock.write(Buffer.concat([hd,mask,mk])); });
  sock.write(`GET ${u.pathname} HTTP/1.1\r\nHost: ${u.host}\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Key: ${crypto.randomBytes(16).toString('base64')}\r\nSec-WebSocket-Version: 13\r\n\r\n`);
  sock.on('data', d => { buf=Buffer.concat([buf,d]);
    if(!hs){ const i=buf.indexOf('\r\n\r\n'); if(i<0) return; buf=buf.slice(i+4); hs=true; }
    while(buf.length>=2){ let len=buf[1]&0x7f, off=2;
      if(len===126){ len=buf.readUInt16BE(2); off=4; } else if(len===127){ len=Number(buf.readBigUInt64BE(2)); off=10; }
      if(buf.length<off+len) return;
      const txt=buf.slice(off,off+len).toString(); buf=buf.slice(off+len);
      try{ const m=JSON.parse(txt); if(m.id&&pending.has(m.id)){ pending.get(m.id)(m.result); pending.delete(m.id);} }catch(e){} } });
  await sleep(400);
  await send('Page.enable'); await send('Runtime.enable');
  await send('Emulation.setDeviceMetricsOverride',{width:1440,height:900,deviceScaleFactor:1,mobile:false});
  await send('Page.navigate',{url:'file://'+file});
  await sleep(1500);

  const rows = [];
  for (let i = 0; i < STEPS; i++) {
    await send('Input.dispatchKeyEvent', { type:'rawKeyDown', windowsVirtualKeyCode:9, nativeVirtualKeyCode:9, key:'Tab', code:'Tab' });
    await send('Input.dispatchKeyEvent', { type:'keyUp', windowsVirtualKeyCode:9, nativeVirtualKeyCode:9, key:'Tab', code:'Tab' });
    const r = await send('Runtime.evaluate', { returnByValue: true, expression: `(() => {
      const el = document.activeElement;
      if (!el || el === document.body) return { tag: 'BODY' };
      const c = getComputedStyle(el);
      const ring = c.outlineStyle !== 'none' && parseFloat(c.outlineWidth) > 0;
      const shadow = c.boxShadow && c.boxShadow !== 'none';
      return {
        tag: el.tagName.toLowerCase() + (el.className ? '.' + String(el.className).split(' ').filter(Boolean).slice(0,2).join('.') : ''),
        text: (el.textContent||el.value||'').trim().slice(0,26),
        focusVisible: el.matches(':focus-visible'),
        outline: c.outlineStyle + ' ' + c.outlineWidth + ' ' + c.outlineColor,
        shadow: shadow ? c.boxShadow.slice(0,42) : 'none',
        visible: ring || shadow
      };
    })()` });
    rows.push(r.result.value);
  }
  const bad = rows.filter(r => r.tag !== 'BODY' && !r.visible);
  rows.forEach((r,i) => {
    if (r.tag === 'BODY') return;
    console.log(String(i+1).padStart(3) + '  ' + (r.visible ? 'ok  ' : 'NONE') + '  fv=' + (r.focusVisible?'y':'n') + '  ' + r.tag.padEnd(34) + ' ' + JSON.stringify(r.text) + '   outline=' + r.outline);
  });
  console.log('\n' + bad.length + ' of ' + rows.filter(r=>r.tag!=='BODY').length + ' tab stops draw nothing');
  chrome.kill(); process.exit(0);
})().catch(e=>{console.error(e);chrome.kill();process.exit(2);});
