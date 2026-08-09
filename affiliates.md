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

Suggested `campaign` convention, useful for reading which page types actually earn:

```
pt-<experience-slug>-<placement>

pt-cape-peninsula-hero
pt-cape-peninsula-verdict
pt-table-mountain-comparison
```

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

- [ ] Confirm Viator `mcid` value from a dashboard-generated link
- [ ] Confirm payout thresholds, methods and frequency for both programmes
- [ ] Confirm tax/entity treatment of GBP income with an accountant
- [ ] For each of the six focus experiences: compare the Viator and GetYourGuide listings, pick the better one for the reader, record the choice and the reason
- [ ] Create the six ThirstyAffiliates `/go/` links
- [ ] Verify a test booking attributes correctly on **both** platforms before launch

### Link register

Fill in as products are chosen. The "why" column is the useful part — it is what stops a future switch being made on commission rate alone.

| `/go/` slug | Platform | Product | Why this one |
|---|---|---|---|
| `/go/cape-peninsula-full-day` | | | |
| `/go/table-mountain-cable-car` | | | |
| `/go/robben-island` | | | |
| `/go/cape-winelands-tour` | | | |
| `/go/safari-day-trip` | | | |
| `/go/shark-cage-gansbaai` | | | |

---

## Related

- `strategy.md` §1 — unit economics and why average order value matters more than volume
- `identity.md` §4 — trust architecture and the booking handoff
- `build.md` §4 — why affiliate link management is non-negotiable
