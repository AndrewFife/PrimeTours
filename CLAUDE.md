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
- **Named author: Andrew Fife.** Real name, photo, bio, `Person` schema linked to `Organization`, byline block on every substantial page. This is the primary E-E-A-T and GEO signal — do not publish anonymous brand-voice content.
- **Registration WC7622 is CURRENT**, issued by the **Western Cape Government** (verified Aug 2026). "Registered tourist guide" in the present tense is accurate and should be used — it is a *regulated* status under South Africa's Tourism Act, not a self-description, and a checkable number outweighs any amount of asserted experience. Marked up as `hasCredential` in schema. **If it ever lapses, remove the schema block and switch every claim to past tense.**
- **Andrew no longer lives in South Africa and takes no guiding work.** Never imply a reader can book him or that he will be their guide — that is the same operator-impersonation error the rebrand exists to avoid.
- **Never name his country of residence** — not in copy, not in schema, not in an author bio. The about page says "seeing rather more of the world outside South Africa", which tells the reader everything relevant: he isn't there, and he isn't guiding. The byline carries the credential and the route only.
- **Where independence comes from**: Prime Tours no longer operates tours, and Andrew takes no guiding work in Cape Town. He holds the credential without holding a stake in any operator he writes about.
- **Lead with the guiding, not the business.** Andrew has driven the Cape **from 2012**; Prime Tours operated only **2018–2022**. The guiding is the stronger claim; the company is context, not the headline.
- **Prime Tours closed in 2022 when Andrew went travelling** — by choice, not failure, and *after* the pandemic rather than because of it. Say so plainly where the closure comes up. **Never imply COVID ended it.**
- **Always write "from 2012", never a duration.** "14 years" is stale by January and has to be chased across every page; a date is permanent and checkable. Applies to bylines, bios and schema.
- **Standard byline**: `Andrew Fife · Registered Cape Town tour guide WC7622 · drove this route from 2012`. Vary the second half by topic where honest, but never claim a route or region Andrew did not personally work.
- **"How we make money" page** gets a main-nav slot. Disclosure microcopy near the first affiliate link on every page, not just the footer.
- **Handoff framing required** on every booking CTA: name the destination, show price and cancellation terms, state that Prime Tours is not the operator.

**Banned operator phrasing**: "our tours", "we offer", "book with us", "our guides", "create your tour", "Prime Tours specialises in". Use "the tours we recommend", "where to book", "the operators we rate", "plan your trip". Any sentence implying Prime Tours staffs, insures, operates, or sells a tour is a blocker, not a style note.

**Confirmed (Aug 2026)**: name Andrew Fife; driving the Cape from 2012; tourist guide registration **WC7622, current and valid, Western Cape Government**; living outside South Africa and taking no guiding work (country not to be named); **Prime Tours operated 2018–2022, closed when Andrew went travelling**; Andrew both drove and ran the business.

**Still unverified**: trip volume, and which routes Andrew personally drove versus which other guides covered. Marked `[CONFIRM]` in `identity.md` — never publish these without checking.

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

### Affiliate programmes — see `affiliates.md`

**Both approved (Aug 2026):** Viator `P00148357` (30-day cookie) and GetYourGuide `2ANVBLS` (31-day cookie). **Both pay GBP into a UK account.**

- **Report revenue in GBP, not converted ZAR.** Income arrives in GBP; tours are priced in ZAR. Tracking in ZAR makes exchange-rate movement look like performance change. Reader-facing prices stay ZAR-first with USD/GBP/EUR equivalents — that is a separate thing.

- **Both pay ~8%, so commission rate is NOT the deciding factor.** Choose per experience based on which listing is genuinely better for the reader — price, review depth, cancellation terms, departure times. That is both the honest call and the higher-converting one; conversion tracks listing quality far more than platform.
- Partner IDs are **not secrets** — they appear in every outbound link and are safe to commit. Payout credentials and API keys are not.
- **Never hand-edit `pid`/`mcid` (Viator) or `partner_id` (GetYourGuide).** A dropped parameter is an unattributed, unpaid booking. Generate links in the dashboard and paste them whole.
- **Don't use `gyg.me` short links** — extra redirect hop, and ThirstyAffiliates already does the cloaking.
- **`/go/` slugs name the experience, never the platform** (`/go/cape-peninsula-full-day`, not `/go/viator-cape-peninsula`). This is what makes switching OTAs a one-field change across every article.
- Switch destinations **deliberately, one experience at a time**, after reading `affiliate_click` data against commission reports. Never alternate randomly per click — it makes attribution unreadable.

### Legal & entity (confirmed Aug 2026)

- **Legal entity: Andrew Fife, sole trader established in the United Kingdom, trading as Prime Tours.** He is also the data controller.
- **Compliance standard: EU GDPR**, as the strictest applicable regime. UK GDPR is substantially aligned, so meeting the EU bar covers both. Rights are extended to all visitors regardless of location.
- **Governing law: England and Wales**, with a carve-out preserving mandatory consumer rights elsewhere.
- **Hosting moving to the United Kingdom** — see `build.md` §2. This supersedes the earlier "keep Arizona" recommendation; the facts changed.
- **Cookie consent: Complianz free tier.** Region-aware, self-hosted, WP Consent API + Consent Mode v2. Takes the plugin count to 10.
- **Consent does NOT cost commission.** Affiliate cookies are set by the OTA, on the OTA's domain, *after* the visitor leaves — `/go/` links are plain redirects with no third-party script. A declined consent costs GA4 visibility only; the booking still attributes. **This holds only while `/go/` stays a plain redirect** — adding an embedded GYG/Viator widget would put third-party scripts on our pages and change the analysis. Revisit `build.md` §4 if one is ever added.
- **AI clause: content yes, visitor data no.** AI and search may crawl, quote and cite published articles — that is the GEO strategy and `robots.txt` says so. Visitor behaviour is never supplied for AI training. The two must stay consistent; terms that contradict `robots.txt` are worse than either position alone.

**⚠️ Publishing a contact address conflicts with the never-name-his-location rule.** A UK sole trader needs a reachable geographic address. Default would be a home address, which the identity rules forbid. **Use a service / mail-forwarding address** (~£20–50/yr). Do not resolve this by silently publishing a home address.

Still for the reviewer, sent as **one bundle**: `how-we-make-money.md`, `privacy.md`, `terms.md`, plus two questions — whether **POPIA still applies** at all now the controller and hosting are UK, and whether an **EU representative under Article 27** is required given the deliberately EU-targeted audience.

### Open pre-launch actions
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
