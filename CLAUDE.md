# Prime Tours — Project Ground Rules

This file is the starting point for every session on the Prime Tours project. Read it first.

## Business Model — CHANGED (Aug 2026)

Prime Tours is pivoting from **tour operator** to **content-led affiliate publisher**. The site refers bookings to GetYourGuide (and comparable OTAs such as Viator) and earns commission (~8%, 30-day cookie). Revenue also comes from blog content with affiliate links.

- **Phase 1**: Cape Town / Western Cape. **Later**: wider South Africa.
- **Target markets**: inbound tourism from the US, UK, and Europe (esp. Germany).
- **Requirements**: strong SEO *and* GEO (AI answer-engine visibility).
- See `strategy.md` for the full business case, unit economics, focus tours, and roadmap.

**IMPORTANT positioning rule**: the site must NOT present Prime Tours as the operator of tours. It is an independent guide and booking resource that recommends and links to vetted operators. A visible affiliate disclosure is required. Do not write copy implying Prime Tours runs, staffs, or sells the tours directly.

## Identity Decisions (Aug 2026)

Full detail in `identity.md`. Locked decisions:

- **Name retained**: Prime Tours, reframed from operator to independent guide. Domain primetours.co.za retained.
- **Always use the descriptor lockup** — "Prime Tours — Independent Cape Town Travel Guide" (later "…South Africa Travel Guide"). Never the bare name in a masthead/hero; alone it reads as an operator.
- **Core line** (should appear above the fold): *"We used to run these tours ourselves. Now we're independent — so we can tell you which ones are actually worth your money."*
- **Tagline**: "Straight answers about Cape Town."
- **Named author: Andrew.** Real name, photo, bio, `Person` schema linked to `Organization`, byline block on every substantial page. This is the primary E-E-A-T and GEO signal — do not publish anonymous brand-voice content.
- **"How we make money" page** gets a main-nav slot. Disclosure microcopy near the first affiliate link on every page, not just the footer.
- **Handoff framing required** on every booking CTA: name the destination, show price and cancellation terms, state that Prime Tours is not the operator.

**Banned operator phrasing**: "our tours", "we offer", "book with us", "our guides", "create your tour", "Prime Tours specialises in". Use "the tours we recommend", "where to book", "the operators we rate", "plan your trip". Any sentence implying Prime Tours staffs, insures, operates, or sells a tour is a blocker, not a style note.

**Unverified facts**: Andrew's operating dates, trip volume, surname, and guiding credentials are placeholders marked `[CONFIRM]` in `identity.md`. Never publish these without checking.

### Six focus tours (Phase 1 priority)
1. Cape Peninsula & Cape Point Full Day (incl. Boulders penguins) — flagship
2. Table Mountain (cable car + City/Bo-Kaap combos) — traffic magnet
3. Robben Island (and combination tours) — highest intent
4. Cape Winelands — Stellenbosch, Franschhoek & Wine Tram
5. Big Five Safari Day Trip from Cape Town (Aquila/Inverdoorn) — highest AOV day trip
6. Shark Cage Diving & Marine Big Five, Gansbaai — highest AOV adventure

## Legacy Business Snapshot (pre-pivot)

Prime Tours previously operated customised private tours of Cape Town and the Western Cape, South Africa, for individuals, families, and groups. This operating history is a genuine E-E-A-T asset — content should draw on real first-hand experience of these routes.

- **Website**: https://primetours.co.za (WordPress 7.0.3)
- **Phone**: +27 87 095 3002
- **WhatsApp**: +27 73 858 5195
- **Address**: 18 Ayers Street, Rosebank, Cape Town
- **Facebook**: facebook.com/primetoursza
- **Site built by**: Utility Cloud Consulting (ucc.co.za)

### Tours currently listed on the live site (legacy, pre-redesign)
- Cape Peninsula Full Day Tour
- City Tour & Table Mountain (Table Mountain and City Walking Tour)
- Cape Winelands Full Day Tour
- Custom/create-your-own tours (promoted, no working booking flow)

## Build Decisions (Aug 2026)

Full spec in `build.md`. Locked:

- **Fresh WordPress install** — no migration. Nothing carried over from the current site except the domain and 301 redirects.
- **Host: Hostinger Business, Arizona US** (retained). LiteSpeed Enterprise stack → use **LiteSpeed Cache**, not WP Rocket/W3TC. Business tier means built-in staging is available.
- **Arizona origin is mitigated, not moved.** Audience skews UK/EU (UK ~210k, Germany ~156k vs US ~188k arrivals H1 2026). Fix with **Cloudflare edge caching of HTML for anonymous visitors** — not just static assets; the default Cloudflare setup does not do this. Highest-leverage performance decision on the project.
- **Repo lives in this Google Drive folder** (decision confirmed Aug 2026). Drive sync causes conflict folders (`dev-site 2/`) and slows DDEV — mitigate by pausing Drive sync while DDEV runs, and treat **GitHub as the real backup**. If `.git` ever corrupts, re-clone rather than repair.
- **Local dev: DDEV on OrbStack** (not Docker Desktop), PHP 8.3+ matching production. **GitHub** for version control, Claude Code for the build.
- **Mutagen is deliberately disabled** (`performance_mode: none`). It is DDEV's macOS default but adds a second async sync layer on top of Drive sync — Drive ↔ host ↔ Mutagen ↔ container — which is how conflict folders and lost writes happen. OrbStack is fast enough without it. If DDEV feels slow, exclude `dev-site/` from Drive sync; do **not** re-enable Mutagen.
- **Pipeline: local → Hostinger staging → cutover.** Current site stays live until launch. Staging must be noindexed and password-protected.
- **Repo tracks code, never content**: `.ddev/`, `composer.json`, child theme, `mu-plugins`, `wp-content/acf-json/`, `scripts/`, `.github/workflows/`. Never core, uploads, third-party plugins, or secrets.
- **ACF field groups must sync to JSON** — this is what keeps environments from drifting. Enable before creating the first field group.
- **THE RULE: code flows up, content flows down.** Local → staging → production for code; production → staging → local for the database. After launch, production is the sole source of truth for content — never push a local DB upward.
- **Branching**: `main` → production (manual approval gate), `develop` → staging (auto-deploy). Deploy credentials in GitHub Secrets only.
- **Theme: GeneratePress + GenerateBlocks** with a child theme. Never a multipurpose/page-builder theme.
- **Plugin budget: 10 or fewer.** Rank Math, LiteSpeed Cache, GenerateBlocks, ACF, ThirstyAffiliates, Fluent Forms Lite, WP Mail SMTP, UpdraftPlus, Solid Security Basic.
- **All affiliate links go through ThirstyAffiliates** as cloaked internal URLs (`/go/...`). Never paste raw GetYourGuide URLs into posts — this is what allows swapping OTAs later and applies `rel="sponsored nofollow"` consistently.
- **Content architecture**: `experience` CPT for tour reviews (with ACF structured fields), Posts for articles, Pages for pillars. `region` taxonomy is what makes national expansion structural.
- **Schema**: `Organization` ← linked → `Person` (Andrew) is the highest-value markup on the site. **Never use `Product`/`Offer`/aggregate `Review` schema** on tour pages — Prime Tours does not sell them.
- **`robots.txt` explicitly allows AI crawlers** (GPTBot, ClaudeBot, PerplexityBot, Google-Extended, CCBot).
- **Tracking ships before launch**, not after: GA4 via GTM with an `affiliate_click` event carrying destination, experience, page, and position.

### Content pipeline — automated drafting, manual publish gate

`Brief → Claude drafts markdown in this folder → automated pre-publish lint → Andrew fact-checks (THE GATE) → pushed to WP as DRAFT via REST API → Andrew publishes.`

**Never auto-publish.** The brand rests on first-hand claims only Andrew can verify. Automate drafting, formatting, checking, and pushing — never verifying.

The pre-publish lint must block on: banned operator phrasing, banned style phrases, unresolved `[CONFIRM]` placeholders, missing disclosure where an affiliate link exists, missing Quick Answer box / byline / `last_verified_date`, raw affiliate URLs bypassing ThirstyAffiliates, and empty required ACF fields.

## Access Status — OPEN ITEM

Confirmed: Hostinger Business plan, Arizona US data centre.

Still needed before anything can be pushed:

1. **GitHub repository** created, with access confirmed.
2. **Hostinger SSH credentials** for GitHub Actions deployment — stored as GitHub Secrets, never in the repo.
3. **WordPress Application Password** (WP Admin → Users → Profile) for REST API drafts — required once staging exists.
4. **Cloudflare account** — confirm who holds it (the current site shows Cloudflare email obfuscation, so the domain is likely already proxied).

Do not assume access exists — confirm with Andrew before attempting to connect to or edit any environment. Nothing touches the live site without explicit approval.

### To confirm with Andrew
- Photography audit: what original images exist from the operating years

## Known Issues / Backlog

Found during initial site review (Aug 2026), not yet actioned:

- Several nav links and CTAs are dead placeholders (`#`): "View All Tours," "Create Your Tour," "Book today!," "View Tours," and the Multi-Day Tours / Custom Tours nav items.
- "Specials & Packages" section shows three identical placeholder cards ("TBA / COVID SPECIALS / Domestic Tours") — stale, points to Facebook instead of real content.
- Twitter/X icon links to generic twitter.com, not an actual Prime Tours profile.
- Site last modified June 2023 — check for outdated plugins/WordPress core version once we have access.

**Superseded by the redesign** — the current theme is placeholder-heavy and should be replaced outright rather than patched. The items above are recorded for context, not as a fix list.

### Open pre-launch actions
- Secure GetYourGuide affiliate approval (compare Awin ~7%, Travelpayouts ~8%, TradeDoubler ~5%); Travelpayouts is typically the easiest entry.
- Apply to Viator affiliate program to run in parallel.
- Draft affiliate disclosure + update site positioning away from "operator" language. Have reviewed professionally.
- Set up analytics and outbound click tracking before launch, segmented so AOV and conversion can be read per page.

Update this list as items are fixed or new issues are found.

## Content & Voice Rules

Full voice guide is in `strategy.md` §3. Summary:

**Positioning**: Prime Tours is the Cape's straight-talking travel companion — written by people who have actually driven these roads, and who say which tours are worth the money and which are not.

- **Lived-in, not researched.** Specifics over generalities (times, queues, weather, prices), not adjectives.
- **Honest even when it costs a booking.** Say when a tour isn't worth it. This is the conversion strategy and the Google-quality defence.
- **Warm but unsentimental.** One concrete image beats a paragraph of adjectives.
- **Calm and practical** about safety, driving, tipping, load-shedding, seasons — international visitors' real anxieties.
- **Respectful** about apartheid history and heritage sites; never used as marketing colour.
- **British English**, neutral international register. Prices in ZAR + USD/GBP/EUR. Distances in km + miles. Explain the southern-hemisphere seasonal inversion often.
- **Banned phrases**: "hidden gem," "nestled," "breathtaking," "must-see," "unforgettable experience," "world-class," "a feast for the senses," "like no other," and overuse of "the Mother City."
- Confirm factual claims (tour inclusions, pricing, timing) with Andrew before publishing — do not invent details.
- Content ratio target: ~70% genuinely useful planning content, ~30% commercial.

## SEO & GEO Rules

- Build **topic clusters** (pillar page + supporting articles), not a flat blog.
- **Front-load answers**: the first 150–200 words of any page must directly answer its core question — AI retrieval weights openings heavily.
- Write **extractable** content: question-phrased headings, short declarative answers, comparison tables, explicit durations/prices/seasons/distances.
- Schema markup on every page: `Article`, `FAQPage`, `BreadcrumbList`, `Organization`, `ItemList`.
- Ensure `robots.txt` allows AI crawlers.
- Real named authorship with a credible Cape Town operational bio. Original photography over stock.
- Refresh and re-date content seasonally.

## Workflow Rules

- Draft and review all content/code changes locally first; do not push or publish directly to the live site without explicit approval.
- Use this folder for final deliverables; work-in-progress drafts stay in the temporary working folder until approved.
- Flag any destructive or irreversible action (deleting content, overwriting live files) before doing it.
- Keep this file updated as ground rules, access details, or the backlog change — it should stay the source of truth for the project.

## Folder Index — `index.md`

This folder maintains an `index.md` file listing every file in the folder with a short summary of its contents, used as quick-reference context.

- Before starting work, check `index.md` for relevant existing files/context rather than re-reading everything from scratch.
- Whenever a file is added, removed, or materially changed in this folder, update `index.md` accordingly — add new files with a summary, update summaries that are stale, and remove entries for deleted files.
- Keep summaries brief (1–3 sentences) but specific enough to know when a file is relevant.
