# Claude Code — Kickoff Prompt

Repo root:

```
~/Library/CloudStorage/GoogleDrive-andrew@andrewfife.net/My Drive/CoWork/UtilityCons/PrimeTours
```

Paste the blocks below into Claude Code with the repo open.

**Pause Google Drive syncing before running DDEV.** The repo lives in a synced folder by design; pausing avoids conflict folders and keeps file operations quick. Push to GitHub often — that, not Drive, is the real backup.

---

## Prompt — commit the scaffold (run this first, once)

```
The Prime Tours repo is scaffolded but has no commits yet. Repo root is the
Google Drive folder at:
  /Users/andrewfife/Library/CloudStorage/GoogleDrive-andrew@andrewfife.net/My Drive/CoWork/UtilityCons/PrimeTours

Do this:

1. Delete the empty "dev-site 2" folder if present — a Drive sync conflict
   artefact, not real content. Confirm it is empty before removing.
2. Sanity-check the scaffold: 13 files, .git present, branch main, no commits.
   Verify .gitignore correctly excludes wp-content/uploads, wp-config.php,
   .env and third-party plugins while keeping the primetours child theme and
   mu-plugins.
3. Commit:
     git add .
     git commit -m "Initial scaffold: strategy docs, DDEV WordPress build, deploy workflows, content lint"
4. Tell me the exact commands to add the GitHub remote and push, including
   creating the `develop` branch. Don't run them — I need to create the repo
   on GitHub first.
```

---

## Prompt — Phase 0 & 1 (foundation)

```
You are building Prime Tours: an independent Cape Town travel guide that earns
affiliate commission by referring bookings to GetYourGuide. It is NOT a tour
operator, and copy implying otherwise is a blocking bug, not a style note.

Read these first, in order — they are the source of truth and they override
your defaults:
  CLAUDE.md     ground rules and positioning constraints
  build.md      the technical specification you are implementing
  identity.md   voice, naming, trust architecture, page copy
  strategy.md   business model and the six focus tours

The repo is already scaffolded. Your job is to get a working local site and
complete Phase 1 of build.md §13.

Environment notes:
- DDEV runs on OrbStack, not Docker Desktop.
- Mutagen is deliberately disabled (performance_mode: none) because the repo
  sits in a Google Drive synced folder and two async sync layers over one
  directory tree causes conflicts. Do not re-enable it.
- Ask me to pause Drive syncing before you run DDEV, and remind me to push to
  GitHub when we finish. GitHub is the backup, not Drive.

TASKS

1. Verify the environment
   - docker context ls  (confirm orbstack is active)
   - cd dev-site && ddev start && ddev composer install
   - bash scripts/setup.sh
   - Confirm the site loads and the Prime Tours child theme is active.
   - Fix anything broken in the scaffold rather than working around it.

2. ACF field groups for the `experience` post type
   Create them in the WP admin (or via WP-CLI/PHP, your call), matching the
   field list in build.md §5:
     price_from_zar, duration_hours, departure_point, best_months,
     gyg_affiliate_link, viator_affiliate_link, cancellation_terms,
     includes, excludes, physical_demand, verdict_short,
     worth_it (select: yes/no/depends), last_verified_date

   CRITICAL: ACF JSON sync is already enabled in primetours-core.php. After
   creating the groups, confirm JSON files appear in wp-content/acf-json/ and
   commit them. If they don't appear, stop and fix the sync — without it the
   environments drift and the whole deployment model breaks.

3. Build the seven components from build.md §6
   Implement as ACF blocks or block patterns. CSS classes are already defined
   in the child theme's style.css — use those exact class names:
     .pt-quick-answer   front-loaded answer, 40-60 words (the GEO workhorse)
     .pt-byline         Andrew's photo, name, credential, dates
     .pt-booking        price, duration, cancellation, disclosure, CTA
     .pt-verdict        worth it / not worth it / depends, with reasoning
     .pt-table          responsive comparison table
     .pt-disclosure     inline affiliate disclosure
     .pt-verified       last-verified stamp, pulled from ACF

   The booking module must always state that Prime Tours is not the operator,
   name the destination platform, and show price and cancellation terms.
   See identity.md §4c — this is the highest-stakes component on the site.

4. Templates
   - single-experience.php  and  archive-experience.php
   - single.php for articles
   - Byline block on every substantial page
   - Schema is handled in primetours-core.php — do NOT add Product, Offer or
     aggregate Review schema anywhere. Prime Tours does not sell these tours.

5. Verify before you report done
   - php -l on every PHP file you touched
   - Load the site and confirm no notices or warnings
   - Create one draft `experience` post exercising every component
   - Check the JSON-LD output validates (Rich Results Test or equivalent)
   - Confirm wp-content/acf-json/ contains the field groups and is committed
   - Run: python3 ../scripts/prepublish_lint.py --help   (confirm it runs)

RULES
- Never use: "our tours", "we offer", "book with us", "our guides",
  "create your tour", "Prime Tours specialises". Deploy will reject these.
- Never paste raw getyourguide.com URLs into content or templates. All
  affiliate links route through ThirstyAffiliates as /go/... internal URLs.
- Never commit secrets, wp-config.php, uploads, or database dumps.
- Andrew's operating dates, surname and credentials are unverified. Leave
  them as [CONFIRM] placeholders — do not invent them.
- Code flows up, content flows down. Never push a database upward.
- British English. Prices in ZAR with USD/GBP/EUR. Distances in km and miles.

Work through the tasks in order. Commit at each logical step with clear
messages. If the spec is ambiguous or you disagree with it, say so before
implementing rather than guessing.
```

---

## Follow-up prompts

**Phase 2 — the flagship page**

```
Build the Cape Peninsula & Cape Point experience page as the template
prototype for the other five focus tours.

Read strategy.md §2 for why this tour is the flagship, and §3 for voice.
Use every component. Front-load the answer in the first 150-200 words.

Do NOT invent specifics — prices, timings, operator names, conditions. Mark
every factual claim [CONFIRM] for Andrew to verify. The whole brand rests on
first-hand accuracy, so a confident guess is worse than a visible gap.
```

**Phase 4 — staging deployment**

```
Set up deployment to Hostinger staging.

The workflows in .github/workflows/ are scaffolded but untested. Required
GitHub Secrets are listed in .env.example.

Verify: staging returns noindex; the redirect map in build.md §11 is
configured in Rank Math; the deploy touches no database or uploads.
Do not deploy to production — that is gated on Andrew's approval.
```

---

## What to hand Claude Code that it can't work out

| Needed | Where from |
|---|---|
| SSH host, port, user, paths | Hostinger hPanel → Advanced → SSH Access |
| WP Application Password | WP Admin → Users → Profile (once staging exists) |
| Andrew's real operating dates, surname, credentials | You — currently `[CONFIRM]` throughout |
| Original photography | The operating-years archive |
| GetYourGuide / Viator partner IDs | Once affiliate applications are approved |

Everything else is in the repo.
