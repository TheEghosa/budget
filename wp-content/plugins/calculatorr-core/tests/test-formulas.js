// Independent check: every expected value below was worked out by hand from the
// trade constants, not read back out of the implementation.
const fs = require('fs');
global.window = {};
eval(fs.readFileSync('assets/js/formulas.js', 'utf8'));
const F = global.window.CalculatorrFormulas;

let pass = 0, fail = 0;
function check(name, actual, expected) {
  const ok = String(actual) === String(expected);
  if (ok) { pass++; } else { fail++; console.log(`  FAIL ${name}\n    got      ${actual}\n    expected ${expected}`); }
}

// Concrete: 10ft x 10ft x 4in = 33.33 cu ft = 1.23 cu yd; 80lb bag holds 0.6 cu ft.
let r = F['concrete-calculator']({shape:'slab', length:10, width:10, thickness:4, quantity:1, waste:0});
check('concrete yards', r.value, '1.23 cubic yards');
check('concrete 80lb bags', r.rows[2].value, '56');

// Square footage: plain 12 x 10.
r = F['square-footage-calculator']({shape:'rectangle', length:12, width:10, rooms:1});
check('sq ft', r.value, '120 sq ft');

// Gravel: 20 x 10 x 3in = 50 cu ft = 1.85 cu yd; 1.4 tons per yard = 2.59 tons.
r = F['gravel-calculator']({length:20, width:10, depth:3, waste:0});
check('gravel yards', r.value, '1.85 cubic yards');
check('gravel tons', r.rows[2].value, '2.59 US tons');

// Tile: 120 sq ft, 12in tiles = 1 sq ft each, +10% waste = 132 tiles, 14 boxes of 10.
r = F['tile-calculator']({length:12, width:10, tileWidth:12, tileHeight:12, waste:10, perBox:10});
check('tiles', r.value, '132 tiles');
check('tile boxes', r.rows[2].value, '14');
check('tile spares', r.rows[3].value, '8');

// Paint: 12x10 room, 8ft walls => perimeter 44, wall area 352.
// Minus 1 door (21) and 2 windows (30) = 301 paintable. Two coats / 350 = 1.72 gal.
r = F['paint-calculator']({length:12, width:10, height:8, doors:1, windows:2, coats:2, coverage:350, ceiling:'no'});
check('paint cans', r.value, '2 gallons');
check('paint exact', r.rows[0].value, '1.72 gal');
check('paint paintable', r.rows[1].value, '301 sq ft');

// Stairs: 108in rise at ~7in => 15 risers of 7.2in, 14 treads of 10in = 140in run.
// Stringer = sqrt(108^2 + 140^2) = 176.81.
r = F['stair-calculator']({totalRise:108, targetRiser:7, tread:10});
check('stair steps', r.value, '15 risers');
check('stair riser', r.rows[0].value, '7.2 in');
check('stair run', r.rows[2].value, '140 in');
check('stair stringer', r.rows[3].value, '176.82 in');

// Board foot: 2in x 6in x 8ft = 8 bd ft.
r = F['board-foot-calculator']({thickness:2, width:6, length:8, quantity:1, price:0});
check('board feet', r.value, '8 bd ft');

// Pool: 32 x 16, depths 3 and 8 => avg 5.5 => 2816 cu ft => 21,065 gallons.
r = F['pool-volume-calculator']({shape:'rectangle', length:32, width:16, shallow:3, deep:8});
check('pool gallons', r.value, '21,065 gallons');

// Voltage drop: 12 AWG copper (6530 cmil), 20A over 100ft, single phase, 120V.
// (2 x 12.9 x 20 x 100) / 6530 = 7.90V = 6.58499%.
r = F['voltage-drop-calculator']({gauge:'12', material:'copper', amps:20, distance:100, volts:120, phase:'single'});
check('voltage drop', r.value, '7.9 V');
check('voltage drop pct', r.rows[0].value, '6.58%');

// Deck: 16ft x 12ft, 5.5in boards with 0.25in gaps => 26 rows x 16ft = 416 linear ft.
// Joists at 16in over 16ft => floor(192/16)+1 = 13.
r = F['deck-calculator']({length:16, width:12, boardWidth:5.5, gap:0.25, joistSpacing:16, waste:0});
check('deck linear ft', r.value, '416 linear ft');
check('deck joists', r.rows[2].value, '13');

// Cubic yards: 10 x 10 x 6in = 50 cu ft = 1.85 cu yd.
r = F['cubic-yard-calculator']({length:10, width:10, depth:6, depthUnit:'inches', waste:0});
check('cubic yards', r.value, '1.85 cubic yards');

// Mulch: 20 x 10 x 3in = 50 cu ft => 25 bags of 2 cu ft.
r = F['mulch-calculator']({length:20, width:10, depth:3, waste:0});
check('mulch bags', r.rows[3].value, '25');

console.log(`\n${pass} passed, ${fail} failed`);
process.exit(fail ? 1 : 0);
