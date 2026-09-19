import pathlib, subprocess, re, html, sys, json
sp = pathlib.Path('.')
base = '/home/user/budget/wp-content/plugins/calculatorr-core'
fonts = (sp/'local-fonts.css').read_text()
css = '\n'.join((pathlib.Path(base)/'assets/css'/f).read_text() for f in ('tokens.css','site.css','calculator.css'))

TEST = """
<script>
window.addEventListener('load', function(){
  var log = [];
  var root = document.querySelector('.calcr[data-calcr-slug]');
  function snap(tag){
    var vals = [];
    root.querySelectorAll('[data-calcr-input]').forEach(function(el){
      vals.push(el.getAttribute('data-calcr-input')+'='+JSON.stringify(el.value));
    });
    var seg = [];
    root.querySelectorAll('.calcr-seg__btn').forEach(function(b){
      if (b.classList.contains('is-active')) { seg.push(b.getAttribute('data-value')); }
    });
    var reset = root.querySelector('[data-calcr-reset]');
    log.push(tag+' :: '+vals.join(' ')+' | seg='+seg.join(',')+
      ' | rows='+root.querySelectorAll('[data-calcr-rep-row]').length+
      ' | repFilled='+Array.prototype.filter.call(root.querySelectorAll('[data-calcr-rep-cell]'),function(c){return c.type!=='select-one'&&String(c.value).trim()!=='';}).length+
      ' | primary='+JSON.stringify(root.querySelector('[data-calcr-primary-value]').textContent.trim())+
      ' | resetDisabled='+(reset?reset.disabled:'n/a'));
  }
  snap('load');
  root.querySelectorAll('[data-calcr-input]').forEach(function(el){
    if (el.type === 'hidden') { return; }
    el.value = '7'; el.dispatchEvent(new Event('input',{bubbles:true}));
  });
  root.querySelectorAll('[data-calcr-rep-cell]').forEach(function(el){
    el.value = el.tagName === 'SELECT' ? el.value : '3';
    el.dispatchEvent(new Event('input',{bubbles:true}));
  });
  var add = root.querySelector('[data-calcr-rep-add]');
  if (add) { add.click(); }
  var segBtns = root.querySelectorAll('.calcr-seg__btn');
  if (segBtns.length > 1) { segBtns[segBtns.length-1].click(); }
  snap('typed');
  var reset = root.querySelector('[data-calcr-reset]');
  if (reset) { reset.click(); }
  snap('reset');
  var pre=document.createElement('pre'); pre.id='probe'; pre.textContent=log.join('\\n');
  document.body.appendChild(pre);
});
</script>
"""

for frag in sorted(sp.glob('r-*.frag')):
    inner = frag.read_text()
    page = ('<!doctype html><html><head><meta charset="utf-8"><style>'+fonts+'\nbody{margin:0}\n'+css+'</style></head>'
            '<body class="page"><main class="site-main"><div class="page-content">'+inner+'</div></main>'
            f'<script src="{base}/assets/js/share.js"></script>'
            f'<script src="{base}/assets/js/formulas.js"></script>'
            f'<script src="{base}/assets/js/calculator.js"></script>'
            + TEST + '</body></html>')
    out = sp/(frag.stem+'.html')
    out.write_text(page)
    r = subprocess.run(['timeout','90','/opt/pw-browsers/chromium-1194/chrome-linux/chrome','--headless','--no-sandbox',
                        '--disable-gpu','--timeout=15000','--virtual-time-budget=4000','--window-size=1300,900',
                        '--dump-dom', 'file://'+str(out.resolve())], capture_output=True, text=True)
    m = re.search(r'<pre id="probe">(.*?)</pre>', r.stdout, re.S)
    print('===', frag.stem, '===')
    print(html.unescape(m.group(1)) if m else '  NO OUTPUT')
