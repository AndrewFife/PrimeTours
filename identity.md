# Prime Tours — Brand Identity

**Version 1.0 — August 2026**
Decision: retain the name *Prime Tours*, reframe it from operator to independent guide. Site built around Andrew as the named expert.

> ⚠️ Items marked **[CONFIRM]** are placeholders awaiting factual detail from Andrew. Do not publish these until verified.

---

## 1. The Core Move

Prime Tours stops being a company that sells tours and becomes a **publication that judges them**. The operating history is not discarded — it is promoted from *offer* to *credential*.

The entire repositioning fits in one sentence, and this sentence should be visible above the fold on the homepage:

> **We used to run these tours ourselves. Now we're independent — so we can tell you which ones are actually worth your money.**

This single line does four jobs at once: it explains the name, converts the operator history into authority, establishes the honesty promise, and pre-frames why bookings happen elsewhere. Everything below is an elaboration of it.

---

## 2. Name & Lockup

**Name:** Prime Tours (unchanged)
**Domain:** primetours.co.za (retained — the ccTLD is a modest ranking handicap in US/UK searches but an authenticity asset, and Google now weights it less heavily than it once did)

The descriptor carries the repositioning. The word "Tours" stays, but it is immediately recontextualised by what follows it.

| Use | Lockup |
|---|---|
| **Primary (Phase 1)** | Prime Tours — Independent Cape Town Travel Guide |
| **Primary (Phase 2+)** | Prime Tours — Independent South Africa Travel Guide |
| **Title tag pattern** | `[Page topic] \| Prime Tours` |
| **Short form** | Prime Tours |

Never use "Prime Tours" alone in a masthead or hero position without the descriptor. On its own it reads as an operator; that is the entire problem being solved.

**Tagline (emotional, for hero and social bios):**
> Straight answers about Cape Town.

**Alternates**, if a longer line is needed:
- "We drove these roads for a living. Now we'll tell you the truth about them."
- "Honest guides to the Cape's best experiences — and its worst."

### Words to retire

| Never write | Write instead |
|---|---|
| "Our tours" / "We offer" / "Book with us" | "The tours we recommend" / "Where to book" |
| "Prime Tours specialises in…" | "Prime Tours reviews and recommends…" |
| "Create your tour" | "Plan your trip" |
| "Our guides" | "The operators we rate" |
| "Book today!" | "Check availability on GetYourGuide" |

Any sentence implying Prime Tours staffs, insures, operates, or sells a tour is a compliance problem, not a style problem. Treat it as a blocker.

---

## 3. Author Identity — Andrew

The site is built around a named human. This is simultaneously the trust fix, the defence against Google's helpful-content system, and the strongest available GEO signal — AI answer engines cite identifiable expertise far more readily than anonymous brands.

### Requirements

- **Real name and photograph** on the about page, in the site footer, and in a byline block on every article.
- **A persistent `Person` schema entity** linked from `Organization` schema, with `sameAs` pointing to any professional profiles (LinkedIn, industry bodies, published work).
- **A byline block** at the top of every substantial page: photo, name, one line of credential, last-updated date.
- **First person, used sparingly but unmistakably.** "I've done this drive perhaps two hundred times" is worth more than three paragraphs of brand voice.

### The framing decision (Aug 2026)

Three facts, and the weakest one is the most tempting:

- **Registered tourist guide, WC7622 — registration is current** ← the credential
- **Guided in Cape Town from 2012** ← the experience
- **Ran Prime Tours as an operator, 2018–2022** ← a chapter within it

**Always write "from 2012", never "14 years".** A duration is stale by January and has to be maintained across every page on the site; a date is permanent, and a reader can check it. This applies to schema, bios and bylines alike.

### Registration status — current, but not actively guiding

Registration **WC7622 is valid**, issued by the **Western Cape Government**. Andrew does not currently take guiding work because he is travelling and living outside South Africa.

**Do not name a country of residence** — not in copy, not in schema, not in an author bio. "Seeing more of the world outside South Africa" says everything a reader needs: he isn't there, and he isn't guiding. The specific location is his to disclose and adds nothing to the reader.

Both halves matter and both are safe to state:

- **"Registered tourist guide"** in the present tense is accurate, and it is a *regulated* status under South Africa's Tourism Act rather than a self-description. A verifiable registration number is worth far more than any amount of asserted "experience", so use it.
- **He is not currently guiding**, so no page may imply readers can book him or that he will be their guide. That would be the same operator-impersonation error the whole rebrand exists to avoid.

| ❌ Never | ✅ Instead |
|---|---|
| "Book a tour with Andrew" | "The tours Andrew rates" |
| "Andrew will show you the Peninsula" | "Andrew drove this route from 2012" |
| "14 years' experience" | "Guiding from 2012" |

**Where the independence claim now sits.** It no longer rests on having left the profession — Andrew hasn't. It rests on two things that are cleaner anyway: Prime Tours no longer operates tours, and Andrew takes no guiding work in Cape Town. He holds the credential without holding a stake in any operator he writes about. That is a stronger position than "ex-guide", not a weaker one.

**Publishing WC7622:** yes. A live, checkable registration number is the single most verifiable credential on the site.

Lead with the guiding, always. Driving these roads from 2012 is what earns the right to say a viewpoint is ruined by eleven o'clock. Three years of running a small company earns very little by comparison, and leading with it would undersell a genuinely strong position. Prime Tours the business is context, not the claim.

**Note the dates:** Prime Tours operated **2018–2022**, so it ran through the pandemic and closed afterwards, when Andrew went travelling. It was not a casualty — it was wound up by choice. That distinction is worth preserving in any copy that touches the closure.

The guiding is also a **formal, regulated credential** rather than a self-description — registration WC7622 under South African tourism regulation. That is categorically stronger than "years of experience" asserted by someone with nothing checkable to point at.

### Bio — long form (about page)

> I'm Andrew Fife, a registered South African tourist guide — **WC7622**, Western Cape Government — and I've been driving the Cape **from 2012**. From **2018 to 2022** I ran Prime Tours, taking visitors from Britain, Europe and the States around the Peninsula, the Winelands and the city. I closed it in 2022 and went travelling.
>
> These days I'm seeing rather more of the world outside South Africa, and I take no guiding work in Cape Town. That is exactly why this site can say what it says: I keep the credential and the knowledge, but I have no tour to sell you and no operator to protect.
>
> I don't run tours any more. What I have instead is an unusual amount of specific knowledge: which operators actually show up on time, which viewpoints are ruined by eleven o'clock, which "full day" tours are four hours of driving, and which of the expensive ones are genuinely worth it.
>
> This site is that knowledge, written down. I earn a commission when you book through the links here, which I'll always tell you about. It doesn't change what you pay, and it doesn't change what I write — the whole point of this site is that I no longer have a tour of my own to sell you.

### Bio — short form (article bylines)

> **Andrew Fife** — registered South African tourist guide (WC7622), driving the Cape from 2012 and owner of Prime Tours from 2018 to 2022. Now writes about which tours are worth booking. [More →]

### Byline component (every substantial page)

```
Andrew Fife
Registered Cape Town tour guide WC7622 · drove this route from 2012
Verified [date]
```

Vary the second half by topic where it is honest — "drove this route from 2012" on the Peninsula page, "guided the Winelands from 2012" on wine pages. Never claim a route or region Andrew did not personally work.

### Decided — location stays on the about page, unnamed

**Byline:** credential and route only. **About page:** "seeing rather more of the world outside South Africa" — no country named.

The reasoning: the site's differentiator is lived-in local knowledge, and putting a foreign address above every article invites readers to discount everything below it, even though the knowledge was earned on the ground from 2012. Meanwhile the fact that actually matters to a reader — *he isn't guiding you and has no stake in who does* — is fully conveyed without a place name.

This is not concealment. The about page says plainly that he is no longer in South Africa and takes no guiding work; it simply declines to publish a home address, which no reader needs. Bylines carry credentials, biography pages carry biography.

### How the operating years ended — settled

**Prime Tours closed in 2022 when Andrew went travelling.** Say it plainly, in those terms.

It is a better line than any hedge, and worth a sentence rather than a euphemism, because "closed when I went travelling" tells a reader three useful things at once: the business ended by choice rather than failure; it survived the pandemic that took a great many Cape Town operators; and Andrew has no residual commercial interest in the trade. That last point is the independence claim, arriving as biography instead of assertion.

**Do not imply the pandemic ended it.** I nearly wrote that from the dates alone, and it was wrong.

### Still to confirm
- **Trip volume** — an approximate number would strengthen the bio ("somewhere past 300 runs down the Peninsula").
- **Which routes Andrew personally drove** versus which Prime Tours ran with other guides. Governs where first-person claims are permitted.
- **Approximate trip volume.** "Somewhere past 300 runs down the Peninsula" is the kind of number that makes a bio land.
- **Which routes Andrew personally drove** versus which Prime Tours ran with other guides. This governs where first-person claims are permitted.
- **A photograph** — on location, not a studio headshot.

---

## 4. Trust Architecture

This is where affiliate sites either build credibility or quietly destroy it. Three components:

### a) "How we make money" — a real page, not buried fine print

Give it a nav slot. Explain the model in plain language: Prime Tours earns a commission from GetYourGuide and other booking platforms when a reader books through a link; the price is identical either way; recommendations are not sold. State explicitly what would get an operator removed from the site.

Counter-intuitively this **raises** conversion. It is the mechanism that makes the honesty promise believable, and vagueness here reads as evasion.

### b) Disclosure microcopy at the point of use

A single short line near the first affiliate link on every page, not only in the footer. Required for FTC (US), ASA/CAP (UK), and EU consumer rules — all three of your target markets.

> *We earn a commission if you book through this link. It costs you nothing extra and doesn't affect what we recommend.*

### c) The handoff moment — the most under-designed part of any affiliate site

The instant a reader leaves for GetYourGuide is where trust is won or lost. Never use a bare "Book Now" button that dumps them onto an unfamiliar checkout. Frame it:

> **Book on GetYourGuide** — free cancellation up to 24 hours before. From R1,450pp.
> *We're not the operator. GetYourGuide handles the booking and your money; we earn a small commission.*

Set the expectation of leaving, name the destination, show the price and the cancellation terms. A reader who knows what is about to happen converts; one who is surprised bounces.

### Editorial independence statement

Publish, and mean, a short standards note: recommendations are based on first-hand experience and operator track record; commission rates never determine ranking or inclusion; negative assessments are published even where a commercial relationship exists. This is the promise the whole brand rests on — it is also the reason the site survives future Google quality updates.

---

## 5. Visual Direction

Move from **tour brochure** to **editorial masthead**. The current 2020 logo and theme read as a small operator; the new identity should read as a publication with a point of view.

**Logotype.** A restrained serif or high-contrast sans wordmark, set with the descriptor in a smaller weight beneath or beside it. Avoid the entire vocabulary of tour-operator branding: no compasses, no sun-over-mountain marks, no safari silhouettes, no swooshes. If a mark is needed at all, keep it abstract and small — the wordmark should do the work.

**Palette.** Draw from the actual landscape rather than travel-brochure saturation: granite grey, fynbos green, Atlantic blue-grey, sand, off-white. One restrained accent for links and CTAs. Muted reads as considered; saturated reads as selling.

**Typography.** A serif for headlines and body — serif signals editorial authority and reads as a publication rather than a shopfront. Generous line height and measure. Optimise ruthlessly for long-form reading on mobile, which is where most of this traffic will land.

**Photography.** Original images only, wherever it is possible. This is a hard rule: stock photography is a visible quality signal in the wrong direction and undermines the "we were actually there" claim on which everything else depends. Prefer real conditions — cloud on the mountain, a queue, weather — over postcard perfection. Caption images with specifics: location, time of day, month.

**Layout.** Article-first. Wide, comfortable text column; tables and comparison blocks as first-class design elements, since they serve both readers and AI extraction; the author byline block visible near the top; booking modules that look like considered recommendations rather than banner ads.

---

## 6. Core Page Copy

Drafts to adapt, not final copy. Prices and specifics marked **[CONFIRM]**.

### Homepage hero

> # Straight answers about Cape Town
>
> We used to run private tours here. Now we're independent — which means we can tell you which tours are worth your money, which are a waste of a day, and where to book them.
>
> [Start planning your trip] [The six tours we get asked about most]

### Homepage — trust strip (immediately below hero)

> **[X] years driving these routes** · **Independent — we don't run the tours** · **We tell you when something isn't worth it**

### Navigation

`Plan Your Trip` · `Cape Town Tours` · `Winelands` · `Safari & Wildlife` · `About Andrew` · `How We Make Money`

Nav phrased as a guide's table of contents, not an operator's product catalogue. "How We Make Money" earns its slot — visible transparency is a differentiator, not an admission.

### About page opener

> ## We're not a tour company any more. That's the point.
>
> Prime Tours spent **[X]** years as a private tour operator in Cape Town. We know what these days actually look like from the driver's seat — the timings that work, the ones that don't, and the operators who quietly cut corners.
>
> We've stopped running tours. We now recommend other people's, and earn a commission when you book. That trade is deliberate: we no longer have a tour of our own to sell you, so we can be straight about everyone else's.

### How we make money — opener

> ## How we make money
>
> When you book a tour through a link on this site, we earn a commission — usually around 8% — from the booking platform, most often GetYourGuide. **You pay exactly the same price you would pay going direct.**
>
> Here's what that does and doesn't buy: it does not buy a recommendation, a ranking, or a good review. We write about tours we think are worth doing and say plainly when one isn't, including tours we'd earn money from. A site that only ever said yes would be worth nothing to you, and we'd rather you came back next time.

### Footer disclosure (site-wide)

> Prime Tours is an independent travel guide. We are not a tour operator and do not sell or run tours. We earn commission from bookings made through our links, at no additional cost to you. [How we make money →]

---

## 7. Implementation Checklist

**Before launch — blocking**
- [ ] Remove every instance of operator language from the live site (see §2 word list)
- [ ] Affiliate disclosure drafted and **professionally reviewed** for FTC / ASA / EU compliance
- [ ] "How we make money" page live and in the main navigation
- [ ] Andrew's bio finalised with verified dates, credentials, and photograph
- [ ] `Organization` + `Person` schema deployed and linked

**Before launch — important**
- [ ] New logotype and descriptor lockup
- [ ] Byline component built into the article template
- [ ] Handoff/booking module component with price, cancellation terms, and disclosure
- [ ] Original photography audit — identify what exists and what must be shot
- [ ] Editorial standards note published

**Open questions for Andrew**
1. Operating dates, trip volume, and formal guiding credentials
2. Preferred public name and an on-location photograph
3. Are there past operator relationships worth disclosing — or worth monetising directly at better than 8%?
4. Is there an existing customer list or Facebook audience that can be told about the relaunch?

---

## Related Files

- `strategy.md` — business model evaluation, six focus tours, full brand voice guide, SEO/GEO plan
- `CLAUDE.md` — project ground rules and positioning constraints
