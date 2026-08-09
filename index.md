# Prime Tours Folder Index

This file summarizes every file in this folder so it can be used as quick-reference context without re-reading each file in full. Keep it up to date — see the "Folder Index" rule in CLAUDE.md.

## Strategy & ground rules

| File | Summary |
|---|---|
| `CLAUDE.md` | Project ground rules and starting context. The Aug 2026 pivot to a content-led affiliate model, the six Phase 1 focus tours, the mandatory "not an operator" positioning rule and banned phrasing, identity decisions, build decisions (DDEV/GitHub/Hostinger), the content pipeline with its manual publish gate, access status, brand voice, and SEO/GEO rules. **Read first every session.** |
| `strategy.md` | Business model strategy (v1.0). Affiliate unit economics and the traffic needed to hit revenue targets; the HCU/thin-affiliate risk and why Andrew's operating history is the moat; the six selected Cape Town focus tours with rationale plus Phase 2 holds; full brand voice guide with US/UK/Europe market calibration and banned phrases; content architecture, SEO, GEO, monetisation layering, and an 18-month sequencing plan. |
| `identity.md` | Brand identity spec (v1.0) for the operator→publisher repositioning. The core reframing line; retained name plus mandatory descriptor lockup and tagline; banned operator phrasing with replacements; Andrew's named-author spec with bios and schema requirements; trust architecture (how-we-make-money page, disclosure microcopy, the booking handoff moment); visual direction; draft page copy; pre-launch checklist. `[CONFIRM]` marks unverified facts. |
| `build.md` | WordPress build specification (v1.0). DDEV → Hostinger Business staging → production pipeline; repo structure and the "code flows up, content flows down" rule; branching and GitHub Actions deployment; why the Arizona origin is kept and mitigated with Cloudflare edge HTML caching; GeneratePress theme choice; the 9-plugin stack and why affiliate link management is non-negotiable; content architecture (`experience` CPT, `region` taxonomy, ACF fields); the seven page components; schema rules including what must NOT be marked up; tracking; AI crawler policy; the content pipeline; URL structure and the 301 redirect map; launch checklist; six-phase build sequence. |
| `index.md` | This file. |

## Build scaffold

| File | Summary |
|---|---|
| `CLAUDE_CODE_PROMPT.md` | Paste-ready kickoff prompts for Claude Code: first a one-off prompt to clear the Drive conflict folder and commit the scaffold, then Phase 0/1 (environment, ACF field groups, the seven components, templates, verification), plus follow-ups for the flagship Cape Peninsula page and staging deployment. Ends with a table of the things Claude Code cannot work out for itself — SSH details, app password, Andrew's unverified biographical facts. |
| `README.md` | Repo entry point: layout, repo location (this Drive folder) with Drive-sync mitigations, getting-started commands, the code-up/content-down rule, branching and deployment table, content pipeline summary, and what is and isn't tracked. |
| `.gitignore` | Enforces "track code, never content". Ignores WP core, uploads, third-party plugins, secrets, database dumps, DDEV runtime, and Google Drive sync artefacts; re-includes the child theme and custom mu-plugins. |
| `.env.example` | Template for local environment variables (WP app password for the content pipeline, affiliate IDs, analytics). Also documents the GitHub Secrets required for deployment, which live in repo settings and never in a file. |
| `scripts/prepublish_lint.py` | Blocking pre-publish quality gate. Checks frontmatter, unresolved `[CONFIRM]` placeholders, banned operator phrasing, banned style phrases, raw affiliate URLs bypassing ThirstyAffiliates, missing disclosure, missing Quick Answer box and byline; warns on thin content, missing question-headings, and missing currency/distance conversions. Tested and working. Does **not** check factual accuracy — that is Andrew's gate and stays manual. |
| `.github/workflows/deploy-staging.yml` | Auto-deploys `develop` to Hostinger staging. Code only — never database or uploads. Lints PHP, rsyncs tracked paths, flushes caches, and re-asserts `blog_public 0` on every deploy so staging can never become indexable. |
| `.github/workflows/deploy-production.yml` | Deploys `main` to production behind a GitHub environment approval gate. Adds a banned-operator-phrasing grep that fails the build, a pre-deploy database backup, and post-deploy checks on homepage status and robots.txt. |

## `dev-site/` — WordPress install (DDEV project)

| File | Summary |
|---|---|
| `dev-site/.ddev/config.yaml` | DDEV project config, running on OrbStack. PHP 8.3 and MariaDB 10.11 to match Hostinger production; project root as webroot. **Mutagen disabled** (`performance_mode: none`) — it is DDEV's macOS default but would add a second async sync layer on top of Google Drive sync; OrbStack is fast enough without it. Notes that production runs LiteSpeed while DDEV uses nginx, so cache behaviour must be verified on staging rather than locally. |
| `dev-site/composer.json` | Composer manifest managing WordPress core, GeneratePress, and the nine-plugin stack via wpackagist. Core installs to the project root (not Bedrock) for straightforward shared-hosting deployment. |
| `dev-site/wp-content/themes/primetours/style.css` | GeneratePress child theme. Design tokens from `identity.md` §5 — landscape palette, serif typography, reading measure — plus styling for all seven components (`.pt-quick-answer`, `.pt-byline`, `.pt-booking`, `.pt-verdict`, `.pt-table`, `.pt-disclosure`, `.pt-verified`). |
| `dev-site/wp-content/themes/primetours/functions.php` | Child theme functions: stylesheet enqueuing, front-end weight trimming (emoji scripts, conditional block CSS), theme supports, editorial image sizes, and an editor colour palette mirroring the CSS tokens. |
| `dev-site/wp-content/mu-plugins/primetours-core.php` | Content model and site policy, in mu-plugins so it survives theme changes. ACF JSON sync (the anti-drift mechanism), the `experience` CPT, `region` and `experience_type` taxonomies, Organization↔Person schema graph, AI crawler allowances in robots.txt with a staging guard, and a backstop filter forcing `rel="sponsored nofollow"` onto any raw OTA link that slips past ThirstyAffiliates. Deliberately omits Product/Offer/Review schema. |
| `dev-site/scripts/setup.sh` | Idempotent local bootstrap: Composer install, WP install, theme and plugin activation, permalinks and options, deletion of WordPress default content, and seeding of the `region` and `experience_type` taxonomy terms. |
