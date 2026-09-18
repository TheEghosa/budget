# calculatorr.com: The 100-Calculator Roadmap

This is the build inventory for calculatorr.com, organised by use case rather than
alphabetically, because the use case is what drives the URL structure, the internal
linking, and eventually the category hub pages that hold the whole site together in
search. Ten categories, ten calculators each.

Everything below the first ten is a judgment call rather than a keyword-validated
pick, so treat the ordering inside each category as provisional. Once Fredrick sends
the real keyword set, we re-rank each category by search volume against difficulty
and the build order changes accordingly.

## URL structure

Every calculator lives under its use-case folder, which means the category is visible
in the URL itself and Google gets a clean topical signal before it has even crawled
the page:

```
calculatorr.com/<category-slug>/<calculator-slug>/
```

For example `calculatorr.com/finance/compound-interest-calculator/` and
`calculatorr.com/health/bmi-calculator/`. Each category slug also resolves to a hub
page at `calculatorr.com/<category-slug>/` that lists every calculator in that
category, since those hubs are what pass authority down to the individual tools and
what we point new backlinks at.

| Category | Slug |
|---|---|
| Finance & Money | `/finance/` |
| Loans, Debt & Mortgages | `/loans/` |
| Health & Fitness | `/health/` |
| Math & Algebra | `/math/` |
| Geometry & Shapes | `/geometry/` |
| Unit Conversion & Everyday | `/convert/` |
| Business, Sales & Marketing | `/business/` |
| Home, Construction & DIY | `/home-diy/` |
| Date, Time & Age | `/time/` |
| Education & Academic | `/education/` |

---

## 1. Finance & Money  `/finance/`

Personal money tools where the user already knows their numbers and just wants the
arithmetic done. Commercially the strongest category on the site, because finance
keywords carry the highest display-ad rates.

1. Compound Interest Calculator
2. Savings Goal Calculator
3. Retirement Savings Calculator
4. Inflation Calculator
5. Investment Return (ROI) Calculator
6. 50/30/20 Budget Calculator
7. Net Worth Calculator
8. Emergency Fund Calculator
9. Salary to Hourly Calculator
10. Take-Home Pay Calculator

## 2. Loans, Debt & Mortgages  `/loans/`

Higher intent than general finance because someone running these numbers is usually
weeks away from signing something, which is exactly the audience advertisers pay for.

11. Mortgage Payment Calculator
12. Mortgage Affordability Calculator
13. Auto Loan Calculator
14. Personal Loan Calculator
15. Student Loan Repayment Calculator
16. Credit Card Payoff Calculator
17. Debt Snowball Calculator
18. Loan Amortization Schedule
19. Refinance Break-Even Calculator
20. Down Payment Calculator

## 3. Health & Fitness  `/health/`

Enormous evergreen volume and very simple maths, so this category gives us the best
traffic per hour of build time. The tradeoff is that we need a visible medical
disclaimer on every page, both for the user's sake and because health content without
one struggles on trust signals.

21. BMI Calculator
22. Calorie (TDEE) Calculator
23. Body Fat Percentage Calculator
24. Ideal Weight Calculator
25. Macro Calculator
26. BMR Calculator
27. Water Intake Calculator
28. Heart Rate Zone Calculator
29. Running Pace Calculator
30. Pregnancy Due Date Calculator

## 4. Math & Algebra  `/math/`

Constant student demand that spikes hard with the academic year. These rank on utility
alone, so the page needs to solve and show the working rather than just print a number.

31. Percentage Calculator
32. Percentage Increase & Decrease Calculator
33. Fraction Calculator
34. Ratio Calculator
35. Mean, Median & Mode Calculator
36. Standard Deviation Calculator
37. Quadratic Equation Solver
38. Scientific Calculator
39. GCF & LCM Calculator
40. Exponent & Logarithm Calculator

## 5. Geometry & Shapes  `/geometry/`

Closely related to the maths category but worth separating, since the search intent is
measurement rather than algebra and the pages benefit from a diagram of the shape.

41. Area Calculator
42. Volume Calculator
43. Circle Calculator
44. Pythagorean Theorem Calculator
45. Right Triangle Calculator
46. Rectangle & Square Calculator
47. Cylinder Volume Calculator
48. Sphere Calculator
49. Surface Area Calculator
50. Distance Between Two Points Calculator

## 6. Unit Conversion & Everyday  `/convert/`

Massive aggregate volume spread across thousands of long-tail variations such as
"kg to lbs" and "cm to inches", which means one well-built converter can capture
hundreds of keywords if we generate the common unit pairs as their own sub-pages.

51. Length Converter
52. Weight & Mass Converter
53. Temperature Converter
54. Volume & Liquid Converter
55. Speed Converter
56. Area Converter
57. Data Storage Converter
58. Cooking Measurement Converter
59. Shoe Size Converter
60. Currency Converter

## 7. Business, Sales & Marketing  `/business/`

Lower volume than consumer categories but far better ad rates and a much more valuable
visitor, so these earn their place despite the smaller audience.

61. Profit Margin Calculator
62. Markup Calculator
63. Break-Even Point Calculator
64. Sales Tax Calculator
65. VAT Calculator
66. Discount Calculator
67. Tip Calculator
68. CPM & Ad Cost Calculator
69. Conversion Rate Calculator
70. Customer Lifetime Value Calculator

## 8. Home, Construction & DIY  `/home-diy/`

Seasonal and strongly commercial, because a person calculating how much paint to buy is
about to buy paint. Worth building before spring if we want to catch that cycle.

71. Paint Calculator
72. Flooring & Tile Calculator
73. Concrete Calculator
74. Mulch & Soil Calculator
75. Roofing Calculator
76. Drywall Calculator
77. Board Foot & Lumber Calculator
78. Fence Calculator
79. Stair Calculator
80. BTU & HVAC Sizing Calculator

## 9. Date, Time & Age  `/time/`

Trivial to build and surprisingly high volume, which makes this the category to lean on
whenever we need quick wins to grow the site's page count.

81. Age Calculator
82. Date Difference Calculator
83. Add or Subtract Days Calculator
84. Business Days Calculator
85. Countdown Calculator
86. Time Duration Calculator
87. Time Zone Converter
88. Work Hours & Timesheet Calculator
89. Sleep Cycle Calculator
90. Week Number Calculator

## 10. Education & Academic  `/education/`

Predictably seasonal around term start and exam periods, so the sensible move is to have
these live and indexed well before August rather than building them in September.

91. GPA Calculator
92. Weighted Grade Calculator
93. Final Grade Calculator
94. Test Score Percentage Calculator
95. Reading Time Calculator
96. Word & Character Count Calculator
97. Typing Speed (WPM) Calculator
98. College Cost Calculator
99. Class Attendance Calculator
100. Cumulative GPA Planner

---

## Deliberately held back

Automotive and Travel is the obvious eleventh category, covering fuel cost, MPG, road
trip time, tyre size and currency-per-trip tools. It did not make the first hundred
because Auto Loan already sits in the Loans category and the rest of that cluster is
better chosen from real keyword data than from guesswork. When the keyword list
arrives, it is the first candidate for expansion.

---

## Build order: the first ten

These ten come first because each one is pure arithmetic with no external data feed, no
API key and no live rate to maintain, which means build one ships as a completely
self-contained site. They also span eight of the ten categories, so by the time the
tenth is done we will have proven the page template against every layout variation the
remaining ninety will need.

| # | Calculator | URL | Why it is in the first ten |
|---|---|---|---|
| 1 | Percentage Calculator | `/math/percentage-calculator/` | Highest raw volume on the whole list and the simplest possible logic, so it doubles as the template test |
| 2 | BMI Calculator | `/health/bmi-calculator/` | Huge evergreen demand, and it forces us to solve the metric/imperial unit toggle early |
| 3 | Mortgage Payment Calculator | `/loans/mortgage-payment-calculator/` | Best ad revenue per visitor, and it establishes the amortisation logic four other tools reuse |
| 4 | Compound Interest Calculator | `/finance/compound-interest-calculator/` | Proves the results-table and chart pattern that every projection tool needs |
| 5 | Age Calculator | `/time/age-calculator/` | Trivial build, high volume, and it validates date handling across time zones |
| 6 | Tip Calculator | `/business/tip-calculator/` | Mobile-first by nature, so it is the honest stress test of the responsive layout |
| 7 | Discount Calculator | `/business/discount-calculator/` | Shares most of its logic with Tip, making it a cheap second page in the same category |
| 8 | Calorie TDEE Calculator | `/health/tdee-calculator/` | Multi-input form with a dropdown, which is the most complex input pattern we need to design |
| 9 | GPA Calculator | `/education/gpa-calculator/` | Requires add-and-remove dynamic rows, the last input pattern the template has to support |
| 10 | Sales Tax Calculator | `/business/sales-tax-calculator/` | Simple maths but region-dependent, so it proves the pattern for location-aware variants later |
