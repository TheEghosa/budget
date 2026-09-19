# Calculatorr redesign: build specification

This document is the complete handoff for the Calculatorr redesign, written so that whoever builds it (a developer or Claude Code) never has to guess a colour, a size, a state or a behaviour. When something is decided, it is written here with its value. When something was deliberately left open, it is listed in the final section as an open item, so an open question never quietly turns into an improvised answer.

## 1. How to use this package

The package has four parts, and they are ranked, because the artboards were drawn by hand and the tokens were extracted from them afterwards and checked against every artboard.

1. `tokens.json` is the source of truth for every value: colours for both themes, type, spacing, radii, sizes, breakpoints and motion.
2. `tokens.css` is the same data as ready-to-paste CSS custom properties. Dark is the default on `:root`, and light applies through `[data-theme="light"]`.
3. This spec explains how the tokens are applied, component by component and page by page, and it covers behaviour the pictures cannot show.
4. The design canvas (the "Calculatorr Redesign" artifact) shows ten artboards: three dark desktop pages, a light desktop homepage and calculator page, three dark mobile pages, a light mobile homepage, and a 390 x 844 "first screen" of the mobile calculator that shows the sticky result bar. If a pixel on an artboard ever seems to disagree with a token, the token wins, although an automated check found no disagreement at the time of handoff.

The `assets` folder holds every icon, both logo marks and every thumbnail motif as standalone SVG files, so nothing needs to be redrawn or traced from a screenshot.

## 2. The brand rule that drives everything

The palette comes directly from the logo, which is a teal tile holding an equals sign whose lower bar is cut short and closed by an amber dot. The logo guideline explains that the equals sign stands for the answer, so the design follows one rule. **Teal is the brand, and amber is the answer.**

In practice that means three things, and each exists for a reason.

- Every icon tile, primary button, link, active state and chart principal is teal (`--c-brand`), so the whole site reads as one product.
- Amber (`--c-accent`) appears once per graphic at most, on the element that represents the result: the arc of the 35% ring, the needle pivot on the BMI gauge, the final point on the compound growth curve, the interest share in the loan charts. Because it stays rare, it keeps its meaning.
- No other hues are used. An earlier draft gave each of the ten categories its own colour, but that was dropped because it no longer matched the logo. Categories are told apart by their icon and their name, never by colour.

The one exception is the disclaimer notice, which uses a muted amber-brown family (`--c-notice-*`) because it is a caution, and caution is a separate job from brand identity.

## 3. Colour tokens

All 33 colour tokens with their dark and light values and their intended use are in `tokens.json` under `color`. The table below covers the ones that are easiest to misuse, since these are where a developer is most likely to reach for the wrong shade.

| Token | Dark | Light | Where it goes |
|---|---|---|---|
| `bg-page` | #111418 | #f7f6f3 | Page body and the fill inside text inputs |
| `bg-raised` | #14181d | #ffffff | Header, homepage hero band, category header band |
| `surface-card` | #181c22 | #ffffff | Every card and panel |
| `surface-sunken` | #1c2128 | #f1efeb | Chips, list rows, secondary and icon buttons |
| `brand` | #4fb8a5 | #0f6b5e | Tiles, primary button, links, active nav, principal in charts |
| `brand-graphic` | #4fb8a5 | #2a9d88 | Thumbnail motifs only, because they are decorative and never carry text |
| `tile-glyph` | #13171c | #ffffff | Icon strokes on a teal tile |
| `accent` | #e8a33a | #e8a33a | The answer highlight, the eyebrow dot, interest in charts |
| `on-accent` | #14171c | #14171c | Anything placed on amber |

Notice that the tile glyph flips between themes: dark teal tiles carry near-black icons, while light theme tiles are the deeper #0f6b5e and carry white icons. That mirrors the two logo lockups in your brand sheet exactly, so do not use one glyph colour for both themes.

### Contrast (measured, WCAG 2.x formula)

Every text pairing was computed rather than eyeballed. The lowest ratio in the system is 4.57:1, which clears the 4.5:1 minimum for body text.

| Pairing | Dark | Light |
|---|---|---|
| text-primary on surface-card | 15.08 | 17.81 |
| text-secondary on surface-card | 9.28 | 9.44 |
| text-muted on surface-card | 7.02 | 6.89 |
| text-subtle on surface-sunken | 5.25 | 4.57 |
| brand text on surface-card | 7.11 | 6.39 |
| on-brand on brand (primary button) | 7.59 | 6.39 |
| result-muted on result-bg | 7.85 | 5.69 |
| on-accent on accent | 8.32 | 8.32 |
| notice-text on notice-bg | 11.06 | 7.31 |

## 4. Typography

The display face is Space Grotesk (500, 600, 700) and the body face is Source Sans 3 (400, 500, 600, 700), loaded from Google Fonts with the URL in `tokens.json`. I chose these because they match the letterforms visible in your current site screenshots, but I could not inspect the live font files since the site blocks my fetcher. If your codebase already self-hosts different files, confirm the families match before swapping anything.

Weight 700 of Source Sans 3 is required, because the uppercase overline labels ("9 CALCULATORS", "LOANS") use it; without it the browser fakes a bold that looks muddy.

Every text style is in `tokens.json` under `type`, with desktop and mobile sizes, line height and letter spacing. A few rules apply across all of them.

- Headings use negative tracking (between -0.02em and -0.035em) because Space Grotesk looks loose at large sizes otherwise.
- Every number that can change (payments, totals, stat values) uses `font-variant-numeric: tabular-nums`, so digits do not jump sideways as the user types.
- Overlines are uppercase, 0.06em tracking, weight 700, in the brand colour.
- Running text never drops below 14px; only captions (13px on mobile) and overlines (11 to 13px) go smaller. Input text is at least 16px on mobile so iOS does not zoom into a field when it is tapped.

## 5. Layout, grid and breakpoints

The desktop artboards are drawn at 1440px and the mobile artboards at 390px. The content container is 1200px wide and centred, which is where the 120px side gutter at 1440 comes from.

| Range | Name | Gutter | Header |
|---|---|---|---|
| 1200px and up | Desktop | centred 1200px container | Full nav, 76px tall |
| 768px to 1199px | Tablet | 32px | Full nav from 1024px up, mobile header below 1024px |
| 767px and down | Mobile | 20px | Logo, search button, menu button, 64px tall |

The tablet range was not drawn, so the following rules are specified here rather than left to interpretation:

- The homepage hero is two columns from 1024px up and stacks below that, with the preview card under the search, as on mobile. The three floating chips around the preview card are hidden below 1024px because they need the side space.
- The category grid is five columns on desktop, three on tablet and two on mobile.
- Tool card grids are three columns on desktop, two on tablet and a single column on mobile.
- On the calculator page, the sidebar sits beside the main column from 1200px up and moves below the main column on tablet. The inputs and the result panel sit side by side from 768px up and stack below that.

## 6. Components

Each component lists its anatomy, its values and its states. Hover states apply only on devices that support hover. Keyboard focus uses a visible `outline: 2px solid var(--c-brand); outline-offset: 2px` on every interactive element, because the artboards cannot show focus but the site must.

### 6.1 Header

The header is 76px tall on desktop (64px on mobile) on `bg-raised`, with a 1px `border-divider` along the bottom. On desktop it holds the logo lockup (36px mark with a 26px wordmark and a 12px gap), six nav links in body 600 at 16px spaced 32px apart, a 44px square search button and the theme toggle. The active nav link turns `brand` and gets a 3px bottom bar in `brand` drawn as `box-shadow: inset 0 -3px 0`, with the link stretched to the full header height so the bar sits on the header's bottom edge.

On mobile the nav links are replaced by a 44px menu button. The menu itself was not drawn, so build it as a full-height sheet sliding in from the right on `bg-raised`, listing the six nav links as 56px rows followed by the theme toggle, and closable with a 44px close button and the Escape key.

The theme toggle is a 44px-tall secondary button reading "Light" with the sun icon while the dark theme is active, and "Dark" with the moon icon while the light theme is active.

### 6.2 Brand tile

The tile is the site's signature element. It is a rounded square in `brand` with a centred stroke icon in `tile-glyph`, where the icon is half the tile size and uses a 2.2 stroke width. The corner radius is roughly 0.29 of the tile size, and the exact pairs are 76/22, 64/18, 52/15, 44/13, 40/12 and 36/11.

### 6.3 Buttons

| Variant | Height | Fill | Border | Text |
|---|---|---|---|---|
| Primary | 48px (44px in the mobile search) | brand | none | on-brand, Space Grotesk 700 15 to 16px |
| Secondary | 48px | surface-sunken | 1px border-strong | text-primary, Source Sans 3 600 15px |
| Icon | 44 x 44px | surface-sunken | 1px border-strong | icon in text-secondary, with an `aria-label` |

Buttons use 12px radius, 8px icon-to-label gap and `white-space: nowrap`. The secondary hover fill is `surface-hover`. Icons inside buttons must not shrink (`flex-shrink: 0`), which was a real bug caught in QA.

### 6.4 Search form

The search form is 64px tall on desktop and 56px on mobile, with a 16px radius, filled with `bg-page` and bordered with `border-strong`. It holds a 22px search icon in `text-subtle`, a visually hidden label ("Search calculators"), the input and a primary button reading "Search". The placeholder reads "Try mortgage, BMI, percentage, tip" on desktop and "Try mortgage, BMI, tip" on mobile, since the longer text truncates at 390px.

### 6.5 Chips and pills

Popular chips are 36px tall pills on `surface-sunken` with a 7 to 8px teal square dot, the label in body 600 and `surface-hover` on hover. The eyebrow pill ("105 tools, ten use cases, no sign-up") is filled with `brand-tint`, uses `brand-soft-text` for its label and has a round amber dot, so it echoes the logo.

### 6.6 Category card

On desktop the category card is a vertical stack on `surface-card` with a `border-default` border, 20px radius, 24px padding, 14px gaps and a minimum height of 236px. The stack contains a 52px tile, the name in the category-card-title style, the description in `text-muted` (which grows to push the count down) and the overline count in `brand`. On mobile the card drops the description, uses a 44px tile, 16px padding, 18px radius and a minimum height of 148px, because two columns at 390px leave no room for three lines of description.

On hover it lifts by 4px and its border changes to `border-hover` over 180ms ease. With reduced motion on, only the border changes.

### 6.7 Tool card with thumbnail

The tool card is `surface-card` with a `border-default` border, a 22px radius and clipped overflow. The top is the thumbnail (section 7), and the body below has 22px 24px 24px padding (26px at the bottom on the category page) and holds the overline category (homepage only), the title and the description. It uses the same hover lift as the category card.

On the mobile homepage the tool card becomes a horizontal row: a 92px tinted square holding a 44px tile with a 10px amber dot in its bottom-right corner, followed by the overline, the title and a description clamped to two lines.

### 6.8 List row

List rows appear in "You might also need" and in the calculator sidebar. Each row is a flex row on `surface-sunken` with 12px padding and a 14px radius, holding a 40px tile, a title in row-title style over a 14px description in `text-muted`, and an optional trailing arrow icon in `text-subtle`. On hover the row fill changes to `surface-hover`.

### 6.9 Calculator field

Each field is a label (body 600, 15px, `text-primary`) above an input shell. The shell is 56px tall on desktop and 52px on mobile, filled with `bg-page`, bordered with `border-strong`, with a 14px radius and 16px horizontal padding. It holds an optional prefix ($) or suffix (%, years) in `text-subtle`. The input value uses Space Grotesk 600 at 20px (18px on mobile), and the native number spinners are hidden. On focus the shell border turns `brand` and gains a 3px `focus-ring` spread.

### 6.10 Result panel

The result panel sits on `result-bg` with a `result-border` border, and it carries `aria-live="polite"` so screen readers announce new results. Its contents are the label "Monthly payment" in `brand-soft-text`, the result value, the sentence "for N months on a $X loan" in `result-muted`, a donut chart (132px desktop, 104px mobile, 18px stroke, principal in `brand`, interest in `accent`, starting at 12 o'clock and running clockwise) with a two-item legend, and three summary rows divided by `result-border`. The last row, "Total cost of the boat", is emphasised with a `brand-soft-text` label and a bold value.

### 6.11 Notice

The notice is a flex row on `notice-bg` with a `notice-border` border, 16px radius and 18px 22px padding. It holds a 20px info icon in `accent` and the text in `notice-text`. The wording is kept exactly from your current site.

### 6.12 Stat strip

The stat strip is four cells on desktop and a 2 x 2 grid on mobile, on `surface-card` with `border-default` dividers. Each cell shows a stat value in `brand` above a caption in `text-muted`. The four statements are 105 calculators and converters, 10 use cases, 0 sign-ups, accounts or paywalls, and one tap to copy or share any result. The first three come straight from your homepage copy. The fourth is based on the Copy result and Share buttons on the Boat Loan page, and it assumes every calculator page has those buttons, so confirm that before launch.

### 6.13 Footer

The footer sits on `bg-footer` with a top `border-divider`. On the homepage it shows the logo lockup, the tagline, the ten category links plus "All calculators" in a three-column grid (two columns on mobile), and a bottom row with "calculatorr.org" and "All rights reserved". The category and calculator page artboards show a compact version, but the build should use the full homepage footer on every page, since consistent footer links help both users and crawlers.

## 7. Thumbnails and icons

The thumbnail is a tinted panel that shows what the tool produces, so a visitor can recognise a calculator before reading its name. Each one is built the same way, which is what keeps 105 of them consistent.

- **Panel:** 168px tall on homepage cards, 160px on category cards and 136px on mobile category cards, filled with `brand-tint`, with a 1px `border-default` rule underneath.
- **Tile:** a 52px brand tile placed 22px from the top and left.
- **Motif:** an SVG with a viewBox of 384 by the panel height and `preserveAspectRatio="xMaxYMid meet"`, so it stays anchored right and never collides with the tile. It is drawn in `brand-graphic` using fill opacities between 0.12 and 0.8 for depth, and it has exactly one `accent` element standing for the answer.

Thirteen motifs are exported in `assets/thumbnails/dark` and `assets/thumbnails/light`: percentage, mortgage payment (a homepage variant and a category variant), BMI, age, tip, compound interest, amortization, boat loan, HELOC, mortgage payoff, personal loan and the loans category banner. Those cover 11 calculators, so the other 94 do not have motifs yet, and section 10 explains how to handle them without improvising.

Icons are 24px stroke icons with round caps and joins and a 2.2 stroke width, and they use `currentColor` so the theme sets their colour. All 30 are in `assets/icons`, named by role (`category-*`, `tool-*`, `ui-*`). The logo marks for both surfaces are in `assets/logo`.

## 8. Page templates

### 8.1 Homepage

The homepage runs in six bands, and the order matters because it moves from promise to proof to navigation.

1. **Header.**
2. **Hero** on `bg-raised`, 88px top and 104px bottom padding, in two columns with a 64px gap. The left column holds the eyebrow pill, the H1 "A calculator for everything in life" (with "everything" in `brand`), the lead paragraph, the search form and the "Most used today" chips. The right column holds the Boat Loan preview card: a 460px card with a 24px radius and `shadow-float`, 2 x 2 mini value cells, and a tinted result block showing $444.97 with a 60/40 principal and interest bar. Around the card sit three floating elements: a teal BMI chip rotated 4 degrees, an amber tip chip rotated -3 degrees and a 64px percent tile rotated -5 degrees, each with `shadow-chip`.
3. **Browse by use case:** the section title, the subtitle and a right-aligned "All 105 calculators" link, then the category grid.
4. **Most used today:** six tool cards with thumbnails.
5. **Stat strip.**
6. **Footer.**

On mobile the hero stacks, the preview card follows the chips with its floating elements removed, and the "All 105 calculators" link becomes a full-width 48px outlined button under the grid.

### 8.2 Category page (Loans shown)

The header band on `bg-raised` holds the breadcrumb, a pill with the calculator count, the H1, the intro paragraph and, on desktop only, a 360 x 220px illustrated banner on the right. Below that come "All loan calculators" (with "Sorted A to Z" on the right) and the tool card grid, then a two-panel row with "You might also need" (three list rows from other categories) and "Other use cases" (the nine other categories as small rows). On mobile the banner is replaced by a 52px tile beside the count pill, and the two panels stack.

### 8.3 Calculator page (Boat Loan shown)

The page shows the breadcrumb (on mobile, a single back link to the category), then a 76px tile beside the H1 and its one-line description. The main column holds the calculator card (inputs on the left, result panel on the right, each half width), the notice and the "How the payment is worked out" panel with the formula. The 340px sidebar holds "More loans & debt tools". On desktop, the three actions sit on one line in an action bar that spans the full width of the calculator card, separated from the inputs and the result by a 1px `border-default` rule and 20px 36px of padding. Reset sits at the left edge, under the inputs it clears, while Copy result and Share are pushed to the right edge (`margin-left: auto` on Copy result), under the result they act on. Keeping Reset away from Share also matters, because a user reaching for Share should never wipe their numbers by mistake.

Mobile follows the same logic, but each action moves into the card it belongs to, since there is no room for a shared bar at 390px:

- The inputs card opens with a header row: "Your numbers" (Space Grotesk 700, 18px) on the left and Reset on the right as a 44px-tall text button in `brand` with the reset icon.
- The result card comes directly after the inputs, and Copy result and Share sit inside it at the bottom, splitting the width equally. Copy result is an outlined button with a `result-border` border on a transparent fill, and Share is the primary button.
- The order down the page is title, inputs, result, notice, the formula panel and then related tools.

On a 390 x 844 screen, the inputs push the big result number to the fold, so the mobile calculator also has a **sticky result bar**. It is fixed to the bottom of the screen on `result-bg` with a 1px `result-border` top rule, 12px 20px 20px padding (plus `env(safe-area-inset-bottom)`) and a `0 -12px 32px` shadow. It shows "Monthly payment" in `brand-soft-text` at 13px over the live value in Space Grotesk 700 at 26px, with a 44px outlined "Breakdown" button on the right that scrolls smoothly to the result card. The bar carries `aria-live="polite"`, and the result card's own live region is switched off while the bar is showing, so a screen reader never announces the same number twice.

The bar's visibility rules exist so it only appears when it is useful:

- Watch the result card with an IntersectionObserver. Show the bar while the card's main number is not fully in view, and hide it (a 150ms fade, no movement) once the number is fully visible.
- Keep the bar visible while a field has focus, because live feedback while typing is its main job. Position it against `window.visualViewport` so it rides above the on-screen keyboard, and test that on iOS Safari specifically, since fixed elements there can end up behind the keyboard.
- Never show the bar at 768px and wider, where the result sits beside the inputs.

## 9. Calculator behaviour (Boat Loan)

The calculator shows an answer immediately using the default values, because the current site shows a blank dash until every field is filled, and an empty result is the weakest possible first impression.

| Field | Default | Parsing |
|---|---|---|
| Boat price | 60000 | Decimal, dollars |
| Deposit | 12000 | Decimal, empty counts as 0 |
| Interest rate | 7.5 | Percent per year |
| Term | 15 | Years, converted to months and rounded to a whole number |
| Fees and documentation | 500 | Decimal, empty counts as 0, paid upfront |

The loan amount P is the price minus the deposit, r is the percentage rate divided by 1,200 (the yearly rate as a decimal, divided by 12), and n is the term in months. The monthly payment is P × r ÷ (1 − (1 + r)^−n), or P ÷ n when the rate is zero. Total repaid is the payment times n, total interest is total repaid minus P, upfront is the deposit plus fees, and total cost is total repaid plus upfront.

Results recalculate on every keystroke. When the loan is not positive, the term is not positive or the rate is negative, the panel replaces the figures with "Enter a price above your deposit, a term and a rate to see your payment."

Output formats are fixed so nothing is rounded inconsistently:

- The monthly payment and the totals repaid and cost show two decimals ($444.97, $80,093.87, $92,593.87).
- The loan amount, the interest and the upfront figure are whole dollars ($48,000, $32,094, $12,500).
- The principal share is rounded to a whole percent, and the interest share is 100 minus that, so the two always add to 100.
- The donut's principal arc length is share × 2π × 54.

With the defaults, the expected output is $444.97 a month for 180 months, $32,094 interest, $80,093.87 repaid and $92,593.87 total. I recomputed these independently, and they are the test values to check the build against.

Reset restores all five defaults. Copy result writes "Boat loan: $444.97 a month for 180 months" (with the live values) to the clipboard and changes the button label to "Copied" until the next input change. The Share button's behaviour is not defined by this design, so keep whatever the current site does.

## 10. Content and data rules

Everything shown in the artboards is either taken from your screenshots or marked as a placeholder, and the build must keep that distinction. Placeholders in square brackets must be filled from the site's real data, never from made-up copy.

| Item | Status |
|---|---|
| Counts for Financial (9), Loans (6), Health & Body (13), Math (22), Geometry (9) | Verified from your screenshots |
| Counts for Unit Conversion, Business & Shopping, Construction & DIY, Time & Date, Grade & Study | Placeholder `[N]`; the five together must total 46, so all ten reach 105 |
| Descriptions for those five categories | Placeholder |
| Descriptions for Percentage, BMI, Tip, Compound Interest, AP Score | Placeholder |
| Age in Time & Date and Tip in Business & Shopping | Assumed by me; confirm against the site's data |
| Every other title, description and paragraph | Taken from your current site |

For the 94 calculators without a drawn motif, use the plain thumbnail (tint panel plus the tool's 52px tile, with the motif area empty) until a motif is drawn. That fallback is deliberate, because a generic or mismatched motif would undo the point of thumbnails.

The live site has two linking errors that this design corrects, so do not carry them over. The Loans page's "More calculators" block repeats the Amortization Calculator from the main list, and the calculator sidebar headed "More loans & debt tools" lists the 401(k) and Age calculators, which are not loan tools. In the redesign, the sidebar lists only calculators from the same category, and cross-category suggestions go in "You might also need".

## 11. Accessibility requirements

- Every interactive element is a real `<a>`, `<button>` or `<input>` with a `<label>`, and icon-only buttons carry an `aria-label`.
- Touch targets are at least 44 x 44px everywhere.
- Each page has exactly one H1, and the headings run H1, H2 and so on without skipping levels.
- The breadcrumb is a `<nav aria-label="Breadcrumb">` and the current page is marked with `aria-current="page"`.
- The result panel is an `aria-live="polite"` region.
- Decorative SVGs (thumbnails, dots, charts) carry `aria-hidden="true"`, since the same information is present as text.
- `prefers-reduced-motion` removes the hover lift, which `tokens.css` already handles.

## 12. Open items

Four things are open, and each one needs a decision from you rather than from the developer:

1. The real category counts and descriptions, and the five missing calculator descriptions (section 10).
2. Whether the site should follow the visitor's operating-system theme on the first visit. The design assumes dark is the default, because your current dark pages show the toggle reading "Light", but that is an inference from the screenshots.
3. The behaviour of the Share button, which the design leaves as it is today.
4. Motifs for the remaining 94 calculators, which can be drawn in batches by category once this system is approved.

## 13. Pre-launch QA checklist

Before a page ships, run through this list:

- Every colour in the built CSS comes from a `--c-*` variable, with no stray hex values.
- Both themes pass the contrast table in section 3 when measured in the browser.
- The Boat Loan defaults produce exactly the figures in section 9.
- At 390px, 768px, 1024px and 1440px, nothing overflows horizontally and no button label wraps.
- Every calculator link points to a real page, and no card still shows a square-bracket placeholder.
- On a real phone, the sticky result bar appears on load, updates on every keystroke, stays above the keyboard and disappears once the result card is in view.
