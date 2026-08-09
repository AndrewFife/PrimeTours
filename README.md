# Prime Tours

Independent South Africa travel guide. Content-led affiliate site referring bookings to GetYourGuide and comparable OTAs.

**Prime Tours is not a tour operator.** Nothing in this repo should imply otherwise — see `CLAUDE.md` for the positioning rules that gate every piece of copy.

---

## Repository layout

```
CLAUDE.md            Project ground rules — read first, every session
strategy.md          Business model, six focus tours, brand voice, SEO/GEO plan
identity.md          Positioning, naming, author identity, trust architecture
build.md             Full technical build specification
index.md             Folder index with per-file summaries

content/
  briefs/            Article briefs (topic, target query, cluster)
  drafts/            Markdown drafts awaiting review
scripts/
  prepublish_lint.py Blocking quality checks before anything is pushed
dev-site/            WordPress install (DDEV project)
.github/workflows/   Deployment automation
```

---

## Getting started

**Prerequisites:** [DDEV](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/), [OrbStack](https://orbstack.dev/) as the Docker provider (not Docker Desktop), Git.

Confirm OrbStack is active before starting:

```bash
docker context ls        # expect "orbstack" marked *
ddev debug dockercheck
```

Pause Google Drive syncing first.

```bash
cd dev-site
ddev start
ddev composer install
bash scripts/setup.sh        # installs WP, activates theme + plugins
ddev launch
```

Login credentials are printed by `setup.sh`.

---

## Repo location

This repo lives in the Google Drive synced folder:

```
~/Library/CloudStorage/GoogleDrive-andrew@andrewfife.net/My Drive/CoWork/UtilityCons/PrimeTours
```

### Drive sync — known friction

Drive will sync every file WordPress creates, which is a few thousand once core and plugins install. Expect two symptoms:

1. **Conflict folders** (`dev-site 2/`) when Drive sees simultaneous writes. Delete them; they're empty artefacts. One appeared during scaffolding already.
2. **Slower DDEV file operations**, since Drive watches the directory tree.

Mitigations, in order of usefulness:

- **Mutagen is disabled** in `.ddev/config.yaml` (`performance_mode: none`). This matters more than it looks: Mutagen is DDEV's macOS default and adds a *second* asynchronous sync layer over the same files — Drive ↔ host ↔ Mutagen ↔ container. Two async syncers over one directory tree is how conflict folders and lost writes happen. OrbStack's native file sharing is fast enough without it. **Don't re-enable Mutagen to fix a speed problem** — exclude `dev-site/` from Drive sync instead.
- **Pause Drive syncing while running DDEV.** Pause before `ddev start`, resume when you stop working.
- **Exclude `dev-site/` from sync** in Drive preferences if your setup allows folder-level exclusion. Everything in there is either Composer-managed or in git, so losing it from Drive costs nothing.
- **Push to GitHub often.** GitHub is the real backup — Drive is convenience, not safety.

If `.git` ever reports corruption, re-clone from GitHub rather than trying to repair it.

---

## The rule that prevents disaster

> **Code flows up. Content flows down.**
>
> Local → staging → production for code.
> Production → staging → local for the database.

Content moves upward exactly once, at cutover. After launch, **production is the sole source of truth for content** — pushing a local database upward would destroy everything published since.

---

## Branching & deployment

| Branch | Target | Trigger |
|---|---|---|
| `develop` | staging | auto-deploy on push |
| `main` | production | manual approval gate |

Deploy credentials live in **GitHub Secrets**. Never in the repo. See `.env.example` for the required secret names.

---

## Content pipeline

```
Brief → draft (markdown) → prepublish lint → Andrew fact-checks → push as WP DRAFT → Andrew publishes
```

**Nothing auto-publishes.** The brand rests on first-hand claims only Andrew can verify. Automate drafting, formatting, checking and pushing — never verifying.

```bash
python3 scripts/prepublish_lint.py content/drafts/my-article.md
```

---

## What is and isn't tracked

**Tracked:** DDEV config, Composer manifest, child theme, mu-plugins, ACF field group JSON, scripts, workflows.

**Never tracked:** WordPress core, uploads, third-party plugins, `wp-config.php`, `.env`, database dumps.

ACF field groups **must** sync to `wp-content/acf-json/`. That is what stops environments drifting apart — it is enabled in `primetours-core.php` and must stay enabled.
