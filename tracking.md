# Prime Tours — Tracking Specification

**Version 1.0 — August 2026**
GA4 + Google Tag Manager. Ships **before** launch, not after (`build.md` §8).

> **Why before launch:** without this you cannot tell which of the six focus experiences actually earns, and that decision drives everything in months 4–9. Retrofitting analytics means throwing away the launch period's data — the only clean baseline you'll ever get.

---

## Part 1 — What you do (about 20 minutes)

### 1.1 Create a new GA4 property

**In your existing Google Analytics account**, create a new property. Don't reuse the old one — see the reasoning at the foot of this document.

| Setting | Value | Why |
|---|---|---|
| Property name | `Prime Tours — Affiliate Guide` | Distinguishes it from the dormant operator property |
| Reporting time zone | **United Kingdom** | Controller is UK-based |
| **Currency** | **British Pound (£)** | **Critical.** Income arrives in GBP. GA4 does not retroactively re-convert when you change this later. |
| Industry category | Travel | |
| Data retention | **14 months** | Matches the privacy policy. Default is 2 months — **you must change this**, it's easy to miss. |
| Google signals | **Off** at launch | Adds demographic data but also consent complexity. Revisit once traffic justifies it. |

Then create a **Web data stream** for `https://primetours.co.za`, leave enhanced measurement on, and copy the Measurement ID.

✅ **Measurement ID: `G-7K5E9PFV3T`** (received Aug 2026, already baked into the GTM container JSON).

### 1.2 Register custom dimensions

Admin → Custom definitions → Create custom dimension. All four are **event-scoped**.

| Dimension name | Event parameter | Answers |
|---|---|---|
| Affiliate destination | `affiliate_destination` | Which OTA earns — Viator or GetYourGuide |
| Experience | `experience` | Which of the six tours earns |
| Link position | `link_position` | Which part of the page converts |
| Go slug | `go_slug` | Reconciles directly against ThirstyAffiliates and commission reports |

**Register these before launch.** GA4 does not backfill custom dimensions — data collected before registration is unrecoverable. This is the single most common analytics regret.

### 1.3 Mark the key event

Admin → Events → mark **`affiliate_click`** as a key event (conversion).

### 1.4 Create the GTM container and import

Create a web container for `primetours.co.za` and copy the **Container ID** (`GTM-XXXXXXX`).

Then **Admin → Import Container** and upload **`tracking/gtm-container.json`** from this repo. Settings:

- **Choose workspace:** Existing → Default Workspace
- **Import option:** **Merge → Overwrite conflicting tags**
- **Review the diff GTM shows you before confirming.** It lists every tag, trigger and variable being added.

The file already contains your Measurement ID. The `GTM-PLACEHOLDER` value inside it is ignored on import — GTM writes the tags into whichever container you're importing to.

**What gets created:**

| Type | Name |
|---|---|
| Tag | GA4 - Google Tag *(all pages)* |
| Tag | GA4 Event - affiliate_click |
| Trigger | Event - affiliate_click |
| Variable | Const - GA4 Measurement ID |
| Variables | DLV - affiliate_destination, experience, link_position, go_slug |

Both tags are set to **require `analytics_storage` consent**, so they hold until Complianz grants it. Don't publish until you've run Preview mode (§5).

> **If the import errors:** GTM's schema shifts between releases, and a parameter key occasionally moves. If a tag fails to import, create that one by hand from the structure in Part 3 — the variables and trigger are the fiddly part and those are the most stable. Tell me what it rejected and I'll adjust the file.

### 1.5 Link Search Console

Admin → Product links → Search Console. Free, and it puts organic query data next to behaviour data — which is how you'll see which queries actually lead to bookings rather than just to visits.

### IDs — both received ✅

| | Value | Where it lives |
|---|---|---|
| Measurement ID | `G-7K5E9PFV3T` | `tracking/gtm-container.json` |
| Container ID | `GTM-WHSX6CTM` | `inc/tracking.php`, overridable per environment |

Nothing further needed from you on IDs. Remaining setup is §1.1–1.3 and §1.5 in GA4, plus the container import in §1.4.

---

## Part 2 — The event schema

### `affiliate_click` — the only event that matters

Everything else is context. This is the one that maps to money.

```js
dataLayer.push({
  event: 'affiliate_click',
  affiliate_destination: 'viator',                 // viator | getyourguide
  experience: 'cape-peninsula-cape-point-tour',    // post slug
  link_position: 'booking_module',                 // see below
  go_slug: 'cape-peninsula-full-day'               // the /go/ slug
});
```

**`link_position` values** — a closed list. Keep it closed, or the reports become unreadable:

| Value | Where |
|---|---|
| `booking_module` | The main `.pt-booking` CTA |
| `booking_secondary` | The budget alternative line |
| `inline` | A link in body text |
| `quick_answer` | Inside the Quick Answer box |
| `verdict` | Inside the verdict block |
| `comparison_table` | Inside a comparison table |
| `homepage_card` | An experience card on the homepage |

This is the dimension that answers *"does the top module or the verdict actually convert?"* — and per `affiliates.md`, it's where placement analysis lives, because the OTA campaign code can't carry it.

### Supporting events

| Event | Fires when | Worth it because |
|---|---|---|
| `page_view` | Automatic | Baseline |
| `scroll` | 90% depth (enhanced measurement) | Distinguishes "read it" from "bounced" |
| `outbound_click` | Automatic | Catches any affiliate link that escaped the `/go/` layer — a **leak detector** |
| `form_submit` | Contact form | Low volume, useful |

**Watch `outbound_click` for `viator.com` or `getyourguide.com` hosts.** Any hit there means a raw affiliate URL got published and is earning nothing. The lint should prevent it; this catches what the lint misses.

---

## Part 3 — GTM container structure

### Variables

| Name | Type | Source |
|---|---|---|
| `DLV - affiliate_destination` | Data Layer Variable | `affiliate_destination` |
| `DLV - experience` | Data Layer Variable | `experience` |
| `DLV - link_position` | Data Layer Variable | `link_position` |
| `DLV - go_slug` | Data Layer Variable | `go_slug` |
| `Const - GA4 Measurement ID` | Constant | `G-XXXXXXXXXX` |

### Triggers

| Name | Type | Condition |
|---|---|---|
| `All Pages` | Page View | — |
| `Event - affiliate_click` | Custom Event | Event name equals `affiliate_click` |

### Tags

**1. GA4 Configuration** — fires on `All Pages`, uses the Measurement ID constant.

**2. GA4 Event — affiliate_click** — fires on `Event - affiliate_click`, passing all four parameters.

### Consent settings

**The theme sets Consent Mode v2 defaults itself**, in `inc/tracking.php`, immediately before the GTM snippet in the same function.

That ordering is deliberate and it is the part most implementations get wrong. Consent defaults must be in the page *before* the container loads, or tags can fire once before consent is known — which is precisely the breach the banner exists to prevent. Emitting both from one function guarantees the order rather than depending on which plugin happens to enqueue first.

Defaults are `denied` for `analytics_storage`, `ad_storage`, `ad_user_data` and `ad_personalization`; `granted` for `functionality_storage` and `security_storage`, which are strictly necessary. Complianz then issues `gtag('consent','update',…)` when the visitor chooses, unblocking the GA4 tags.

Both tags in the container JSON are already set to require `analytics_storage`.

### Where GTM loads, and where it deliberately doesn't

`primetours_should_load_gtm()` gates the container:

| Context | Loads? | Why |
|---|---|---|
| Production, logged out | ✅ | Real visitors |
| Local / staging | ❌ | Dev sessions would distort a low-traffic launch, and **GA4 offers no way to remove them afterwards** |
| Logged-in editors | ❌ | Our own traffic isn't data |
| Admin screens | ❌ | — |

To test tracking on staging deliberately, set `define( 'PRIMETOURS_GTM_FORCE', true );` in that environment's `wp-config.php`. Remove it afterwards.

Behaviour when consent is declined: GA4 sends cookieless pings only, so you get modelled aggregate data and no identifiable measurement. **The affiliate link still works and the commission is still earned** — see `build.md` §4. Consent costs measurement, not revenue.

---

## Part 4 — What to actually measure

Vanity metrics are a trap here. These four are the ones that inform decisions.

| Question | How | Decides |
|---|---|---|
| **Which experience earns?** | `affiliate_click` by `experience`, reconciled against commission reports | Where the next five pages' effort goes |
| **Which placement converts?** | `affiliate_click` by `link_position` | Page template design |
| **Which OTA converts better?** | `affiliate_click` by `affiliate_destination` vs actual commission | Whether to switch a destination (`affiliates.md`) |
| **Does honesty convert?** | Clicks from `verdict` vs `booking_module` | Whether the editorial verdict is doing commercial work, or just sitting there |

That last one is the interesting one. If verdict-block clicks convert at a materially higher rate than top-of-page module clicks, it means readers are buying *after being persuaded* rather than on impulse — which would validate the entire strategy and argue for longer, more opinionated pages.

### The monthly reconciliation

`affiliates.md` requires comparing click data against actual commission. Do it monthly:

1. Export `affiliate_click` by `experience` and `affiliate_destination` from GA4
2. Export commission by product from Viator and GetYourGuide
3. Match on `go_slug` → product
4. Derive **real** click→booking conversion and average commission per click

This replaces the assumptions in `strategy.md` §1 with observed numbers, and it's the only way to know whether the model is working. Expect GA4 click counts to exceed OTA-attributed clicks — consent decline, ad blockers and cookie loss all cut the same way. **A gap is normal; a widening gap is a signal.**

---

## Part 5 — Verification before launch

- [ ] GTM Preview mode: `affiliate_click` fires on a booking CTA with all four parameters populated
- [ ] GA4 DebugView shows the event arriving with correct values
- [ ] All four custom dimensions registered **before** any real traffic
- [ ] `affiliate_click` marked as a key event
- [ ] Consent banner: declining blocks GA4, and the `/go/` link still redirects correctly
- [ ] Data retention set to 14 months, not the 2-month default
- [ ] Currency is GBP, timezone UK
- [ ] Staging traffic excluded — internal traffic filter, or a separate GTM environment
- [ ] `link_position` populated correctly from at least three different placements on one page

---

## Appendix — Why a new property

Recorded so the reasoning survives.

**The decisive one is currency.** Reporting should be GBP (`affiliates.md`), because that's what lands in the account. The existing property is almost certainly ZAR with an Africa/Johannesburg timezone, and **GA4 converts at the rate on each collection date rather than retroactively**. Changing currency on a live property leaves a timeline whose units silently shift partway through — precisely the "exchange-rate movement looks like performance change" failure the GBP decision was made to avoid.

Supporting reasons: it is effectively a different website sharing a domain, so pre-2023 operator traffic makes no useful comparison; the old property likely carries legacy conversions from the operator era; and a fresh property lets retention, consent mode and dimensions be right from event one.

There is no GA4 benefit to property age, so nothing is lost. **Keep the old property** — dormant, as a historical record. Deleting it gains nothing.
