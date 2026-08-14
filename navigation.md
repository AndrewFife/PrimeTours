# Prime Tours — Navigation & Site Structure

**Version 1.0 — August 2026**
Main nav, footer, and the URL map. Implemented as WordPress menus; see `build.md` §11 for the redirect map from the old site.

---

## Main navigation

```
Plan Your Trip  ·  Cape Town Tours  ·  About  ·  How We Make Money
```

Four items. Deliberately short — a nav that lists everything helps nobody, and every extra item costs attention on mobile.

| Item | URL | Why it's here |
|---|---|---|
| **Plan Your Trip** | `/plan/` | Top-of-funnel. Captures visitors before they know what they want to book, which is where the durable traffic lives. |
| **Cape Town Tours** | `/cape-town-tours/` | The `experience` archive. Commercial intent. |
| **About** | `/about/` | Named authorship is the E-E-A-T signal; every byline links here. |
| **How We Make Money** | `/how-we-make-money/` | Earns its slot. Visible transparency is the differentiator — burying it in the footer would undercut the whole positioning. |

**Deliberately not in the main nav:** Contact, Editorial Standards, region landing pages. All reachable, none competing for attention at the top.

### As the site expands nationally

`region` is hierarchical (`build.md` §5), so national expansion becomes a dropdown rather than a rebuild:

```
Destinations ▾
  Cape Town
  Winelands
  Garden Route
  Kruger & Safari
```

Don't add this until there are at least two regions with real content. A dropdown with one child looks broken.

---

## Footer

Three columns, then a legal strip.

**Explore** — Cape Town Tours · Plan Your Trip · All Experiences

**About** — About Andrew · How We Make Money · Editorial Standards · Contact

**Newsletter** — single email field. Belongs here rather than on the contact page, where it would compete with that page's actual job.

**Legal strip:**

> Prime Tours is an independent travel guide. We are not a tour operator and do not sell or run tours. We earn commission from bookings made through our links, at no additional cost to you. [How we make money →](/how-we-make-money/)
>
> © Prime Tours · Privacy · Terms

This disclosure appears site-wide, in addition to the inline disclosure required near the first affiliate link on every page (`identity.md` §4b). Footer alone is not sufficient for FTC/ASA/EU purposes — it is the backstop, not the disclosure.

---

## URL map

### Live or drafted

| URL | Page | Status |
|---|---|---|
| `/` | Homepage | Drafted |
| `/about/` | About Andrew Fife | Drafted |
| `/how-we-make-money/` | Affiliate disclosure | Drafted — **needs legal review** |
| `/editorial-standards/` | Editorial standards | Drafted |
| `/contact/` | Contact | Drafted |
| `/cape-peninsula-cape-point-tour/` | Cape Peninsula experience | Drafted |

### Planned

| URL | Page |
|---|---|
| `/cape-town-tours/` | `experience` archive |
| `/table-mountain/` | Experience |
| `/robben-island/` | Experience |
| `/cape-winelands-tour/` | Experience |
| `/safari-day-trip/` | Experience |
| `/shark-cage-gansbaai/` | Experience |
| `/plan/` | Planning hub |
| `/plan/tour-costs/` | What Cape Town tours actually cost |
| `/plan/best-time-to-visit/` | Best time to visit Cape Town |
| `/plan/is-cape-town-safe/` | Is Cape Town safe? |
| `/privacy/` | Privacy policy |
| `/terms/` | Terms |
| `/go/…` | Cloaked affiliate links — **noindexed** |

---

## Rules

**`/go/` must be excluded from indexing and from the sitemap.** Rank Math handles this; verify it after launch. Indexed affiliate redirects are an avoidable quality problem.

**Every experience page links to `/about/`** via the byline component, and to `/how-we-make-money/` via the inline disclosure. These aren't decorative — they're the trust path, and they're why the About page had to exist before any tour page publishes.

**Breadcrumbs on every page** except the homepage (`BreadcrumbList` schema, `build.md` §7).

**No orphan pages.** Anything reachable only from the sitemap either gets a home in the nav, the footer, or a contextual link — or it shouldn't exist.

---

## Outstanding

- **Privacy and terms pages** — not drafted. Required before launch, and the privacy policy needs to cover GA4 and affiliate cookies for GDPR/POPIA purposes. Worth including in the same legal review as the disclosure.
- **`.pt-trust-strip` CSS class** — used on the homepage, not yet in `style.css`.
- **Newsletter provider** — not chosen. Footer signup assumes one exists.
