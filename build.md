# Prime Tours — WordPress Build Specification

**Version 1.0 — August 2026**
Fresh WordPress build. Local development → Hostinger staging → cutover to production.

---

## 1. Decisions Locked

| Decision | Choice | Rationale |
|---|---|---|
| Platform | WordPress (fresh install) | Publishing cadence decides whether this business works. Any stack with a build step throttles output. |
| Host | Hostinger **Business**, Arizona US | Already in place, LiteSpeed Enterprise, staging included at this tier. Adequate for 2–3 years of traffic. |
| Local dev | **DDEV** + Claude Code + **GitHub** | Docker-based, config-as-code, reproducible. Correct choice over LocalWP for a git-driven workflow. |
| Pipeline | Local → staging → production | Current site stays live and earning trust signals until cutover. |
| Content | Automated drafting, manual publish gate | Volume without surrendering factual control. See §10. |
| Migration | None — clean build | Two blog posts and placeholder pages. Nothing worth carrying but the URLs. |

**Not carried over:** the existing theme, plugin set, page templates, or the Specials/Custom Tours structure. **Carried over:** domain, and 301 redirects from every existing URL.

---

## 2. Environments, Repository & Deployment

### Local development — DDEV

DDEV is the right call: Docker-based, reproducible, and its configuration is a committed file rather than an application setting, so the environment travels with the repo.

Match production exactly — **PHP 8.3+**, MariaDB, WordPress 7.0.x — in `.ddev/config.yaml`. Getting the PHP version wrong locally is the classic source of "works on my machine" deployment failures.

```
ddev config --project-type=wordpress --php-version=8.3
ddev start
```

WP-CLI is available inside the container (`ddev wp ...`) and is the fastest route for scripted setup — installing plugins, registering post types, seeding test content — which suits Claude Code driving the build.

### Repository structure — what is and isn't tracked

The single most important decision here: **track code, never track content.**

```
Tracked
  .ddev/                          environment config
  composer.json / .lock           core + plugin versions
  wp-content/themes/primetours/   child theme
  wp-content/mu-plugins/          custom functionality
  wp-content/acf-json/            ACF field groups (see below)
  scripts/                        WP-CLI setup, pre-publish lint
  .github/workflows/              deploy automation

Ignored
  wp-core / wp-admin / wp-includes    managed by Composer
  wp-content/uploads/                 media lives on the server
  wp-content/plugins/*                except custom ones
  wp-config.php, .env                 secrets never touch the repo
```

**Manage core and third-party plugins with Composer** via `wpackagist`. This makes every version explicit, reviewable in a diff, and identical across the three environments. Use plain Composer rather than Bedrock — Bedrock's restructured webroot is awkward on Hostinger's shared hosting and buys little here.

**ACF field groups must sync to JSON** (`wp-content/acf-json/`). This is the mechanism that turns database-resident configuration into version-controlled code, and it is what stops staging and production drifting apart. Enable it before creating the first field group. Menus and site options don't have an equivalent — script those with WP-CLI in `scripts/` so they can be replayed on any environment.

### The rule that prevents disaster

> **Code flows up. Content flows down.**
>
> Local → staging → production for code.
> Production → staging → local for the database.

Before launch, content moves upward once, at cutover. **After launch, production is the sole source of truth for content**, and pushing a local database up would destroy everything published since. Set this rule now, while there's nothing to lose by getting it wrong.

### Branching & deployment

- `main` → production, `develop` → staging, feature branches off `develop`.
- **GitHub Actions** deploys on push: `develop` → staging automatically, `main` → production behind a manual approval gate.
- Deploy by rsync/SFTP of tracked paths only, then `composer install --no-dev` and any pending WP-CLI migrations on the server. Hostinger Business includes SSH, so this is straightforward.
- Deploy credentials live in **GitHub Secrets**. Never in the repo, never in a workflow file.

Hostinger's hPanel also offers native Git deployment, which is a simpler alternative if Actions feels like overhead early on.

### Staging

Business plan confirmed, so **built-in staging is available** — use it rather than building a separate subdomain install.

Staging must be **noindexed and password-protected**. A crawlable staging copy is among the most common and most damaging self-inflicted SEO wounds, and it is trivially avoidable.

### Production cutover

Sequence: deploy code → migrate content once (All-in-One WP Migration or Duplicator) → verify the full redirect map → cut over during low-traffic hours → immediately re-verify redirects, sitemap submission, and analytics.

Timing: early morning SAST is late night in the US and mid-morning in Europe. Given your traffic will skew UK/EU, aim for very early SAST.

### Hosting configuration

**On the Arizona data centre.** Worth a clear-eyed look, because your audience isn't primarily American. H1 2026 arrivals: UK ~210k, US ~188k, Germany ~156k — so UK plus Germany alone roughly doubles the US, and that's before the rest of Europe. Phoenix to London or Frankfurt is roughly 140–160ms round trip; an Amsterdam origin serving London is nearer 10–15ms.

> **SUPERSEDED (Aug 2026): move hosting to the United Kingdom.**
>
> The advice below was right on the facts as they stood. Two things then changed: the business is a **UK-established sole trader** and Andrew is the **UK-based data controller**, and hosting is moving to match. That makes a UK data centre better on both axes at once — lowest latency for the largest single market, and UK data residency, which simplifies the international-transfer position in `privacy.md`.
>
> Cloudflare edge caching remains worth doing regardless; it is what serves German and US readers quickly from a UK origin. The Arizona reasoning is kept below because the logic still applies to any future origin decision.

**The earlier recommendation was: keep Arizona, and fix it with Cloudflare.** With full-page HTML caching at the edge, origin location becomes close to irrelevant for cached hits — a European reader is served from a European edge node regardless of where the origin sits. Properly configured edge caching beats migrating the origin without it, costs nothing, and carries no migration risk. Revisit only if analytics later show a poor cache-hit ratio combined with heavy EU traffic.

- **Cloudflare** in front, free tier, with cache rules configured to cache HTML for anonymous visitors — not just static assets. This is the single highest-leverage performance decision on the project, and the default Cloudflare setup does *not* do it.
- **LiteSpeed Cache** as the origin-side cache. Correct for Hostinger's LiteSpeed Enterprise stack — not WP Rocket or W3 Total Cache.
- **Automatic backups** confirmed active, plus an independent backup (see §4).

---

## 3. Theme

**GeneratePress + GenerateBlocks.**

The fastest independent theme available — under 10 KB of CSS on a clean install, and it handles image dimension attributes natively, which protects Cumulative Layout Shift. GenerateBlocks supplies the layout components without loading a page-builder's worth of overhead.

*Alternative:* **Kadence**, if you want more out-of-the-box blocks and expect to lean hard on the block editor. Slightly heavier (roughly 55 KB default page weight versus GeneratePress's minimum) but loads block CSS conditionally, so it stays fast on content-heavy pages. Either is defensible; GeneratePress wins on raw speed, Kadence on editor convenience.

**Avoid entirely:** multipurpose themes (Divi, Avada, Elementor-dependent builds). They are why most WordPress content sites fail Core Web Vitals, and they make the site slow in a way that is very expensive to undo later.

Build a **child theme** for customisations. Typography and colour per `identity.md` §5 — serif body type, muted landscape palette, generous reading measure, mobile-first.

---

## 4. Plugin Stack

Discipline matters more than any individual choice. **Target: 10 or fewer.** Every plugin is a performance cost, a security surface, and a maintenance obligation.

| Plugin | Purpose | Notes |
|---|---|---|
| **Rank Math SEO** | Schema, sitemaps, redirects, meta | Free tier gives finer schema control than Yoast — important for §7. Includes the redirect manager, so no separate plugin. |
| **LiteSpeed Cache** | Caching, WebP, critical CSS, lazy load | Free, and the correct choice on Hostinger's LiteSpeed stack. Also removes the need for a separate image optimiser. |
| **GenerateBlocks** | Layout and component blocks | Pairs with GeneratePress. |
| **Advanced Custom Fields** | Structured experience data | Powers the booking module, comparison tables, and schema output. See §6. |
| **ThirstyAffiliates** | Affiliate link management | **Do not skip this.** See below. |
| **Fluent Forms Lite** | Contact and email capture | Lightweight; most form plugins are not. |
| **WP Mail SMTP** | Form deliverability | Otherwise form mail silently vanishes. |
| **UpdraftPlus** | Independent backups | Belt and braces alongside Hostinger's own. |
| **Solid Security Basic** | Hardening, login protection | Or Wordfence Lite. |
| **Complianz** (free tier) | Cookie consent — **launch blocker** | Region-aware banner, WP Consent API, Google Consent Mode v2. See below. |

### Cookie consent — Complianz, free tier

GA4 plus affiliate cookies, served to a majority UK/EU audience, requires **consent before those cookies are set**. This is substantive rather than paperwork: setting them without consent is the breach itself, and no privacy-policy wording cures it.

**Recommendation: Complianz free tier.** Four reasons, in order of weight:

1. **Region-aware banners.** The banner appears only where consent is legally required. US and South African visitors see nothing at all — no friction, no lost cookies, no lost commission. This is the feature that matters most commercially, and it's the reason to prefer Complianz over a simpler notice plugin.
2. **Self-hosted.** No third-party script fetched on every pageview, unlike hosted CMPs such as CookieYes. Nothing to slow the site down or add a render-blocking external request.
3. **WP Consent API + Google Consent Mode v2** supported natively, so GA4 behaves correctly rather than being crudely blocked.
4. **Genuinely usable free tier** — self-hosted, no pageview cap, guided setup. Around 1M+ installs and 4.7★ on wordpress.org.

Takes the plugin count to **10 — at budget, not over.**

*Alternative considered:* **Cloudflare Zaraz** handles consent at the edge and would keep the plugin count at nine. Reasonable if you later move tag management to Cloudflare wholesale, but it's a bigger architectural change for a marginal gain, and Complianz's region logic is easier to verify.

### ✅ Correction: consent does not cost commission

Earlier drafts of this file warned that declined consent would reduce affiliate revenue. **That was wrong**, and the distinction matters for how the banner is configured.

**Affiliate cookies are set by the OTA, on the OTA's domain, after the visitor has left this site.** Our `/go/` links are plain server-side redirects with no third-party script. Nothing from GetYourGuide or Viator executes on primetours.co.za, so there is no OTA cookie for our banner to block. Attribution happens on their side, under their own consent flow.

What a declined consent actually costs is **analytics visibility**, not money:

| Visitor declines | Consequence |
|---|---|
| GA4 doesn't fire | We can't see their journey or their `affiliate_click` |
| They click through and book anyway | **Commission is still earned and attributed** |

So the banner costs measurement, not revenue. Worth knowing, because it removes any temptation to configure consent aggressively — there is nothing to gain by it.

> ⚠️ **This holds only while `/go/` links stay as plain redirects.** If GetYourGuide or Viator **embedded widgets** are ever added — booking boxes, availability calendars, price widgets — those load third-party scripts on our pages, do set cookies on our domain, and *would* require consent. Adding a widget silently converts this into the situation described above. Don't add one without revisiting this section and the privacy policy.

**Also check ThirstyAffiliates' click-tracking setting.** If its internal statistics are enabled it may store click data as first-party. Decide whether that runs on legitimate interest or consent, and make sure the privacy policy matches whatever is switched on.

### Why affiliate link management is non-negotiable

Every outbound booking link should be a managed, cloaked internal URL (`primetours.co.za/go/cape-point-full-day`) rather than a raw GetYourGuide affiliate URL pasted into the post.

This buys you four things that become critical at scale: you can **swap GetYourGuide for Viator across 200 articles by editing one record**; `rel="sponsored nofollow"` is applied automatically and consistently, which is a Google requirement; you get **per-link click data** natively; and when an operator delists a product you fix one link, not forty. Retrofitting this after a hundred articles is miserable. Set it up before article one.

---

## 5. Content Architecture

Deliberately simple. Complexity here compounds into maintenance drag.

### Post types

**`experience`** (custom post type) — the tour review pages. The six focus tours first, expanding over time. These carry structured data and booking modules.

**Posts** — articles, guides, planning content. Categorised by topic cluster.

**Pages** — pillar pages, About Andrew, How We Make Money, Contact, editorial standards.

### Taxonomies

- **`region`** — hierarchical: Cape Town → City Bowl, Peninsula, Winelands; later Garden Route, Kruger, Johannesburg. This taxonomy is what makes national expansion structural rather than a rewrite.
- **`experience-type`** — wildlife, wine, history, adventure, scenic, cultural.
- **Categories** on posts, mapped to the topic clusters in `strategy.md` §4.

### Custom fields on `experience` (ACF)

These are the heart of the build. They feed the booking module, the comparison tables, and the schema simultaneously — and they are precisely the extractable specifics that AI answer engines cite.

```
price_from_zar          duration_hours           departure_point
best_months             gyg_affiliate_link       viator_affiliate_link
cancellation_terms      includes / excludes      physical_demand
verdict_short           worth_it (yes/no/depends)
last_verified_date
```

**`last_verified_date`** deserves particular attention. Surface it on the page ("Prices and details verified 12 August 2026"). It is a genuine trust signal to readers, a freshness signal to Google, and it gives you a queryable list of what needs re-checking each season. Few affiliate sites do this, and it shows.

---

## 6. Reusable Components

Build these as block patterns or ACF blocks before writing content, so every article assembles from consistent parts.

| Component | Purpose |
|---|---|
| **Quick Answer box** | Sits at the top of every page. Directly answers the page's core question in 40–60 words. This is the GEO workhorse — AI retrieval weights opening content heavily. |
| **Byline block** | Andrew's photo, name, one-line credential, published and updated dates. Every substantial page. |
| **Booking handoff module** | Price from, duration, cancellation terms, "we're not the operator" line, disclosure, CTA. Per `identity.md` §4c. |
| **Verdict block** | The honest call: worth it, not worth it, or depends — and why. The signature editorial element. |
| **Comparison table** | Responsive, genuinely readable on mobile. Serves readers and AI extraction equally. |
| **Inline disclosure** | Short line placed near the first affiliate link on the page. |
| **Last verified stamp** | Pulled from the ACF field. |

---

## 7. Schema & Structured Data

Deploy via Rank Math, with custom output for the `experience` type.

**Use:** `Organization` (Prime Tours) ← linked → `Person` (Andrew, as author on every article); `Article` / `BlogPosting`; `FAQPage` on pages with genuine Q&A; `BreadcrumbList`; `ItemList` on roundups; `TouristAttraction` or `TouristTrip` for the places themselves where it fits naturally.

**Do not use:** `Product`, `Offer`, or aggregate `Review` schema on tour pages. You are not selling these tours, and marking them up as products you offer is both a structured-data violation and a repeat of exactly the operator-impersonation problem the rebrand exists to fix. If you want a rating, express it in prose and in the verdict block rather than in schema.

The `Person` → `Organization` link is the single highest-value schema on the site. It is what lets Google and the AI engines resolve Andrew as a real, credentialed entity rather than a byline.

---

## 8. Tracking & Measurement

Ship this **before** launch, not after. Without it you cannot tell which of the six focus tours is actually earning, and that decision drives everything in months 4–9.

- **GA4 via Google Tag Manager.**
- **`affiliate_click` event** on every outbound booking link, with parameters for destination platform, experience, source page, and position on page. Position matters — it tells you whether readers convert from the top module or only after reading the verdict.
- **Google Search Console** and **Bing Webmaster Tools**. Bing is not optional here: it feeds Copilot, so it is part of your GEO surface.
- **IndexNow** (Rank Math supports it) for fast indexing of new and updated content.
- Reconcile affiliate network earnings against click data monthly to derive real conversion rates and average order value per page. The assumptions in `strategy.md` §1 should be replaced with observed numbers as soon as you have them.

---

## 9. Crawlers & `robots.txt`

Explicitly allow the AI crawlers: `GPTBot`, `ClaudeBot`, `PerplexityBot`, `Google-Extended`, `CCBot`, `Bytespider`, and successors.

There is a real trade-off, and it's worth stating plainly: allowing these crawlers means being read without always earning a click. For a publisher selling ad impressions that's a bad bargain. For an affiliate site it isn't — a citation in an AI answer carries your name and a link into a high-intent planning conversation, and with a third of US trip-planners now using generative tools for research, being absent from that channel costs more than being quoted in it. **Allow them.**

Verify the file post-launch. WordPress generates a virtual `robots.txt`, and blocking crawlers unintentionally is a common and invisible failure.

---

## 10. Content Pipeline — Automated Drafting, Manual Publishing

You asked for automation with retained control. This is the shape that gives you both.

```
1. BRIEF      Topic, target query, focus experience, cluster placement
                ↓
2. DRAFT      I write to brand voice in this folder as markdown —
              front-loaded answer, structured fields populated,
              disclosure and booking module in place
                ↓
3. AUTO-CHECK Automated pre-publish lint (see below)
                ↓
4. REVIEW     Andrew edits and fact-checks  ← THE GATE
                ↓
5. PUSH       Posted to WordPress as a DRAFT via REST API
                ↓
6. PUBLISH    Andrew publishes from WP admin
```

### The automated pre-publish check

A script that refuses to push anything failing on:

- Banned operator phrasing — "our tours", "we offer", "book with us", "our guides" (`CLAUDE.md` §Identity)
- Banned style phrases — "hidden gem", "nestled", "breathtaking", et al.
- Unresolved `[CONFIRM]` placeholders
- Missing affiliate disclosure where an affiliate link is present
- Missing Quick Answer box, byline, or `last_verified_date`
- Raw affiliate URLs that bypass the ThirstyAffiliates layer
- Required ACF fields empty on `experience` posts

Mechanical rules, mechanically enforced — which frees your review time for the thing only you can do.

### Why step 4 never becomes automatic

The entire brand rests on claims only you can verify: that a viewpoint is ruined by eleven, that an operator cuts corners, that a "full day" is really four hours of driving. A confidently wrong published claim doesn't just cost one page — it falsifies the promise the whole site is built on. Automate the drafting, the formatting, the checking, and the pushing. Never the verifying.

**Requires:** a WordPress Application Password (WP Admin → Users → Profile) once staging exists. Nothing connects to the live site until you say so.

---

## 11. URL Structure & Redirects

Flat and keyword-clean. Avoid dated or deeply nested paths.

```
/cape-peninsula-cape-point-tour/      experiences
/table-mountain/
/robben-island/
/cape-winelands-tour/
/safari-day-trip-cape-town/
/shark-cage-diving-gansbaai/

/plan/best-time-to-visit-cape-town/   planning guides
/plan/is-cape-town-safe/
/plan/cape-town-7-day-itinerary/

/about/                                utility
/how-we-make-money/
/editorial-standards/
```

### Redirect map — every existing URL needs a 301

| Old | New |
|---|---|
| `/tour/cape-peninsula-full-day/` | `/cape-peninsula-cape-point-tour/` |
| `/tour/city-tour-table-mountain/` | `/table-mountain/` |
| `/tour/cape-winelands-full-day/` | `/cape-winelands-tour/` |
| `/tour-type/day-tours/` | `/cape-town-tours/` (index) |
| `/about-us/` | `/about/` |
| `/contact/` | `/contact/` (retain) |
| `/travel-insurance/` | `/plan/travel-insurance/` |
| `/our-guide-answers-your-top-questions/` | Closest new guide, or `/plan/` |
| `/planning-your-canoeing-adventure/` | Closest new guide, or `/plan/` |

Before cutover, crawl the live site with Screaming Frog (free to 500 URLs — ample here) to catch any URL not in this list. Media library URLs matter too if any images are linked externally.

---

## 12. Launch Checklist

**Blocking**
- [ ] Every redirect in §11 tested and returning 301
- [ ] Staging noindex removed; production `robots.txt` and meta robots verified
- [ ] Affiliate disclosure live and professionally reviewed (`identity.md` §7)
- [ ] "How we make money" page published and in main nav
- [ ] No operator language anywhere on the site
- [ ] GA4 + `affiliate_click` events firing and verified in DebugView
- [ ] `Organization` + `Person` schema validated in Rich Results Test
- [ ] SSL, backups, and security plugin confirmed active on production

**Important**
- [ ] Core Web Vitals: LCP under 2.5s on mobile, tested on throttled 4G
- [ ] Search Console and Bing Webmaster verified, sitemap submitted
- [ ] All six `experience` pages complete with verified prices and `last_verified_date`
- [ ] Andrew's bio, photo, and credentials confirmed and published
- [ ] 404 page routes usefully into the planning content
- [ ] Test bookings through each affiliate link to confirm tracking attributes correctly

---

## 13. Build Sequence

**Phase 0 — Repo & environment.** GitHub repo, DDEV config, Composer manifest, `.gitignore`, ACF JSON sync enabled, GitHub Actions deploy workflows stubbed. Output: a reproducible environment any machine can spin up.

**Phase 1 — Foundation (local).** WordPress install, GeneratePress child theme, plugin stack, post types, taxonomies, ACF field groups. Output: a working, empty site.

**Phase 2 — Components & templates (local).** The seven components in §6, `experience` and article templates, homepage, navigation, schema configuration. Output: templates ready to receive content.

**Phase 3 — Core content (local).** About Andrew, How We Make money, editorial standards, plus the six `experience` pages. Output: a launchable minimum site.

**Phase 4 — Staging.** Migrate, configure redirects, wire tracking, full review on real devices. Output: sign-off candidate.

**Phase 5 — Cutover.** Launch, verify redirects and indexing, submit sitemaps.

**Phase 6 — Content operations.** Pipeline in §10 running at a sustainable cadence. This phase never ends, and it is the phase that determines whether the business works.

---

## Open Items

**Resolved:** Hostinger Business plan (staging available); Arizona US data centre (retained, mitigated with Cloudflare edge caching); DDEV + GitHub + Claude Code as the development stack.

1. **GitHub repository** — created, and access confirmed for Claude Code.
2. **Hostinger SSH credentials** — needed for GitHub Actions deployment to staging. Stored as GitHub Secrets, never in the repo.
3. **WordPress Application Password** — needed for the content pipeline (§10) once staging exists.
4. **Cloudflare account** — is the domain already proxied through Cloudflare? The existing site shows Cloudflare email obfuscation, which suggests yes; confirm who holds the account.
5. **Photography audit** — original images are a hard requirement (`identity.md` §5). What exists from the operating years, and what needs shooting?
6. **Affiliate approvals — done.** Viator `P00148357` and GetYourGuide `2ANVBLS` both live. Outstanding: choose the better listing per experience and create the six `/go/` links. See `affiliates.md`.

---

## Related Files

- `strategy.md` — business model, six focus tours, brand voice, SEO/GEO plan
- `identity.md` — positioning, naming, author identity, trust architecture, page copy
- `CLAUDE.md` — project ground rules
