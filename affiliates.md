# Prime Tours — Affiliate Programmes

Reference for programme status, link construction, and the conventions that keep links swappable. Updated as programmes are approved.

---

## Status

| Programme | Status | Partner ID | Cookie | Currency | Notes |
|---|---|---|---|---|---|
| **Viator** | ✅ Approved (Aug 2026) | `P00148357` | 30 days | **GBP** | Affiliate tier. Live. |
| **GetYourGuide** | ✅ Approved (Aug 2026) | `2ANVBLS` | 31 days | **GBP** | Direct programme. Live. |

**Both pay GBP into a UK bank account.**
| Direct operator deals | ⬜ Future | — | — | — | Safari, shark cage, helicopter typically pay 10–20% direct. Pursue once traffic gives leverage. |

**Partner IDs are not secrets.** They appear in every outbound link and are safe to commit. Payout credentials and API keys are not — those stay out of the repo.

### Both programmes live — how to choose per experience

Both pay roughly 8%. The commission rate is therefore **not** the deciding factor, and treating it as one would be a mistake.

**Choose per experience based on which listing is genuinely better for the reader** — price, review volume and quality, cancellation terms, departure times, availability. That is the editorially honest call, and it also happens to be the commercially better one, because conversion follows listing quality far more than platform preference does. A reader sent to a thin listing with four reviews doesn't book, whatever the commission rate.

Rules of thumb from inventory differences: GetYourGuide tends to be stronger across Europe and on ticketed attractions; Viator carries far more inventory overall (~300k vs ~60k experiences) and is stronger in North America. For Cape Town specifically, compare the actual product pages rather than assuming.

Then measure. Because every link is a `/go/` slug, switching destination is a one-field change, so you can:

1. Set a default per experience based on listing quality.
2. Read `affiliate_click` events against actual commission reports after a few months.
3. Switch the underperformers without touching a single article.

Avoid randomly alternating per click — it muddies attribution and makes the data unreadable. Change deliberately, one experience at a time, and give each a fair window.

### Currency and payouts

Both programmes pay **GBP into a UK account**, so there is no ZAR conversion on the way in — one currency, one destination, no FX spread on payout. Simpler than the usual arrangement for a South African site.

Two consequences worth acting on:

**Set revenue targets in GBP, not ZAR.** The site quotes prices in ZAR and the tours are sold in ZAR, but income arrives in GBP. If you track performance in converted ZAR, GBP/ZAR movement shows up as apparent growth or decline that has nothing to do with the site. Fix the reporting currency to GBP and the numbers mean what they appear to mean.

**Bookings are still priced in ZAR**, so the OTAs convert at their own rate before commission lands. That conversion is invisible to you and slightly variable — expect small month-to-month noise in effective commission per booking that isn't a performance signal.

Still to confirm per programme: payout threshold, payment method, and frequency. Thresholds matter disproportionately early — a £50 minimum can hold the first few months' earnings until they accumulate.

> Revenue landing in a UK account has tax and entity-structure implications that depend on where the business is resident and registered. Worth a conversation with an accountant familiar with both jurisdictions before volume builds — not something to sort out retrospectively.

---

## Viator link construction

```
https://www.viator.com/tours/<destination>/<product>/<code>
    ?pid=P00148357
    &mcid=<numeric>
    &medium=link
    &campaign=<optional-tracking-code>
```

| Parameter | Purpose |
|---|---|
| `pid` | Partner ID — ties the booking to the account |
| `mcid` | Media channel ID, supplied by Viator |
| `medium` | `link`, `widget`, `banner`, or `api` |
| `campaign` | Optional. **Alphanumerics and dashes only** — other characters can break attribution entirely |

> ⚠️ **Never hand-edit `pid` or `mcid`.** If either is modified or dropped, Viator cannot attribute the booking and will not pay out. Generate links from the partner dashboard and paste them whole.

### Campaign code convention: one code per `/go/` link

```
pt-<go-slug>
```

| `/go/` slug | Platform | `campaign` / `cmp` value |
|---|---|---|
| `/go/cape-peninsula-full-day` | Viator | `pt-cape-peninsula-full-day` |
| `/go/cape-peninsula-budget` | Viator | `pt-cape-peninsula-budget` |
| `/go/table-mountain-cable-car` | TBC | `pt-table-mountain-cable-car` |
| `/go/robben-island` | TBC | `pt-robben-island` |
| `/go/cape-winelands-tour` | TBC | `pt-cape-winelands-tour` |
| `/go/safari-day-trip` | TBC | `pt-safari-day-trip` |
| `/go/shark-cage-gansbaai` | TBC | `pt-shark-cage-gansbaai` |

Alphanumerics and dashes only. Other characters can break attribution entirely.

### Why not track placement in the campaign code

An earlier version of this file suggested per-placement codes (`pt-cape-peninsula-hero`, `-verdict`, `-comparison`). **That does not work with link cloaking**, and it is worth understanding why before anyone reinvents it.

The campaign value lives inside the destination URL stored in the ThirstyAffiliates record. One `/go/` slug holds exactly one destination, so every click on that slug sends the same campaign value no matter where on the page it was clicked. Getting per-placement codes would mean a separate `/go/` link for every placement of every experience, which multiplies the link register and destroys the one-field-swap property that made cloaking worth doing.

**Placement is tracked on our side instead**, in the GA4 `affiliate_click` event, which already carries a `position` parameter (`build.md` §8). That gives the same insight without touching the affiliate URL.

Clean division: the **OTA campaign code answers "which experience earned"**, and **GA4 answers "which part of the page earned it"**. Reconcile the two monthly.

---

## GetYourGuide link construction

```
https://www.getyourguide.com/<location>/<product>-t<id>/
    ?partner_id=2ANVBLS
    &cmp=<optional-campaign>
```

| Parameter | Purpose |
|---|---|
| `partner_id` | Your Cookie ID / Partner ID — ties the booking to the account |
| `cmp` | Optional campaign tracking |

GetYourGuide's dashboard also produces **short `gyg.me/...` links**. Don't use them in content. They add a redirect hop, hide the destination from you at a glance, and duplicate what ThirstyAffiliates already does — cloaking is our layer, not theirs.

Cookie attribution runs **31 days** across the whole GetYourGuide site, so a click on a Table Mountain page that converts into a Winelands booking three weeks later still pays.

> ⚠️ As with Viator, generate links in the dashboard and paste them whole. A dropped `partner_id` is an unattributed booking.

---

## ThirstyAffiliates conventions

Every outbound booking link is a cloaked internal URL. No raw OTA URL ever appears in content — the pre-publish lint blocks it, and `primetours-core.php` carries a backstop filter for anything that slips through.

### Slug naming — name the experience, never the platform

```
✅  /go/cape-peninsula-full-day
✅  /go/table-mountain-cable-car
✅  /go/shark-cage-gansbaai

❌  /go/viator-cape-peninsula
❌  /go/gyg-table-mountain
```

This is the whole point of cloaking. When GetYourGuide is approved and out-converts Viator on a given experience, you change one destination field and every article follows. If the platform name is baked into the slug you either keep a misleading URL or break links across the site.

### Required settings per link

- `rel="sponsored nofollow noopener"` — a Google requirement, applied automatically
- `target="_blank"`
- Category matching the `experience` post, so click reporting groups usefully

---

## Disclosure

Required in all three target markets — FTC (US), ASA/CAP (UK), EU consumer rules. The lint blocks any draft containing an affiliate link without one.

**Inline, near the first affiliate link on every page:**

> *We earn a commission if you book through this link. It costs you nothing extra and doesn't affect what we recommend.*

**In the booking module** (`identity.md` §4c) — name the destination, state that Prime Tours is not the operator, show price and cancellation terms.

**Site-wide footer** plus the `/how-we-make-money/` page in main navigation.

---

## Open items

- [x] Confirm Viator `mcid` value from a dashboard-generated link: **42383**
- [x] Create the Cape Peninsula ThirstyAffiliates `/go/` links (see register below)
- [ ] Confirm payout thresholds, methods and frequency for both programmes
- [ ] Confirm tax/entity treatment of GBP income with an accountant
- [ ] For each of the remaining five focus experiences: compare the Viator and GetYourGuide listings, pick the better one for the reader, record the choice and the reason
- [ ] Create the remaining five ThirstyAffiliates `/go/` links
- [ ] Verify a test booking attributes correctly on **both** platforms before launch

### Link register

Fill in as products are chosen. The "why" column is the useful part: it is what stops a future switch being made on commission rate alone.

| `/go/` slug | Platform | Product | Why this one |
|---|---|---|---|
| `/go/cape-peninsula-full-day` | **Viator, live** | `d318-88021P2` (vs GYG `t68762`) | Like-for-like pair, genuinely close. Viator narrowly ahead on rating and fee transparency. See comparison below. |
| `/go/cape-peninsula-budget` | **Viator, live** | `d318-58181P6` | Same tour as GYG `t125519`, but 2x the reviews and higher rated for $1 more. Secondary CTA on the same page. |
| `/go/table-mountain-cable-car` | | | |
| `/go/robben-island` | | | |
| `/go/cape-winelands-tour` | | | |
| `/go/safari-day-trip` | | | |
| `/go/shark-cage-gansbaai` | | | |

---

## Product comparison — Cape Peninsula full day

Researched 9 August 2026. Prices are "from" (cheapest tier) and were returned in USD on both platforms; verify in ZAR/GBP from your dashboards.

### GetYourGuide shortlist

| Product | From | Rating | Reviews | Notes |
|---|---|---|---|---|
| `t68762` Cape of Good Hope & Penguins Day Tour with Pickup | $51 | 4.7 | 4,142 | GYG "Top pick" |
| `t334741` Cape Point & Boulders Beach Full-Day | $44 | 4.8 | 3,399 | 1 hr at Boulders |
| `t125519` Cape Point & Penguin Explorer Full-Day | $31 | 4.7 | 2,046 | "Certified by GetYourGuide" |
| `t434639` Penguins & Cape of Good Hope with Pickup | $29 (was $49) | 4.9 | 817 | "Likely to sell out" |
| `t153572` Table Mountain, Penguins & Cape Point | $57 | 4.7 | 2,197 | *Combo — belongs on the Table Mountain page* |

### Viator shortlist

| Product | From | Rating | Reviews | Notes |
|---|---|---|---|---|
| `d318-58181P6` Cape Point and Boulder's Penguins Full Day | **$32** | **4.9** | **4,333** | 8 hrs, free cancellation |
| `d318-88021P2` Cape of Good Hope and Boulders Penguins Day Tour | $57 | 4.9 | 3,541 | 9–10 hrs; description states "entrance fees extra" |
| `d318-12189P40` Table Mountain, Penguins & Cape Point Group | $60 (was $67) | 4.7 | 2,320 | *Combo* |
| `d318-88021P12` Table Mountain, Boulders Penguins & Cape Point | $169 | 4.9 | 1,065 | "Best Seller", private |
| `d318-178196P1` Cape Peninsula Full Day in Private Car | $285 **per group** | 4.8 | 357 | Priced per car, not per person |

### Comparing like with like

Two distinct product tiers exist here, and comparing across them is misleading. Pair them properly:

#### Tier 1 — mid-range full day with hotel pickup

| | GetYourGuide `t68762` | Viator `d318-88021P2` |
|---|---|---|
| Price from | **$51** | $57 |
| Rating | 4.7 | **4.9** |
| Reviews | **4,142** | 3,541 |
| Duration | full day | 9–10 hrs |
| Cancellation | not surfaced in listing | **free**, prominent |
| Fee transparency | silent | **states "entrance fees extra"** |

**Genuinely close.** GetYourGuide is $6 cheaper with a slightly larger review corpus; Viator rates higher and is clearer about terms.

Two things tip it to **Viator**, narrowly. A 0.2 rating gap across 3,500+ reviews is a real signal rather than noise — that is a large enough sample that the difference isn't sampling error. And the listing states plainly that entrance fees are extra, which is the single fact this page is built around. Sending readers from an article about hidden conservation fees to a listing that conceals them would undercut the argument; sending them to one that says it out loud reinforces it.

The $6 is real, though, and if you'd rather lead on price the GetYourGuide listing is defensible. This is a judgement call, not a calculation.

#### Tier 2 — budget 8-hour, same tour on both platforms

**GetYourGuide `t125519` and Viator `d318-58181P6` are the same product** — identical name and URL slug, same operator listed twice. The listings are not equivalent:

| | GetYourGuide `t125519` | Viator `d318-58181P6` |
|---|---|---|
| Price from | $31 | $32 |
| Rating | 4.7 | **4.9** |
| Reviews | 2,046 | **4,333** |
| Cancellation | not surfaced | **free**, prominent |

For $1, the Viator listing carries more than twice the reviews and a higher score for the identical tour. If a budget option is offered on the page, use **Viator `d318-58181P6`** — there is no argument for the GetYourGuide version.

#### Suggested page treatment

Offering both tiers serves the reader better than a single link, and it lifts average order value rather than cannibalising it — the budget option captures readers who would otherwise bounce on price.

- **Primary CTA:** Tier 1 (pickup, longer, better rated)
- **Secondary line:** "On a tighter budget, [the 8-hour version] covers the same route for about $32."

**Still to do:** generate both tracked links from the Viator dashboard (never hand-build them — see the warning above), and confirm ZAR prices and cancellation windows before the page publishes.

---

## Related

- `strategy.md` §1 — unit economics and why average order value matters more than volume
- `identity.md` §4 — trust architecture and the booking handoff
- `build.md` §4 — why affiliate link management is non-negotiable
