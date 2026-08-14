---
title: Privacy Policy
slug: privacy
author: Andrew
last_verified_date: 2026-08-09
post_type: page
legal_review_required: true
---

> ⚠️ **DRAFT FOR LEGAL REVIEW — DO NOT PUBLISH AS-IS.**
> A structured starting point intended to reduce a solicitor's billable time, not a finished policy. I am not a lawyer and this is not legal advice. Resolve every `[CONFIRM]` and have the document reviewed before it goes live.
>
> **Compliance standard: EU GDPR**, as the strictest applicable regime. UK GDPR is substantially aligned, so meeting the EU standard covers both. See the note at the foot on whether POPIA still applies at all.

# Privacy Policy

**Last updated: [CONFIRM date of publication]**

## Who we are

Prime Tours is an independent travel guide publishing at primetours.co.za. We are not a tour operator and do not sell, book or fulfil tours.

The site is owned and operated by **Andrew Fife**, trading as Prime Tours — a sole trader established in the United Kingdom.

**Data controller:** Andrew Fife, trading as Prime Tours
**Contact address:** [CONFIRM — see note below on using a service address]
**Data protection contact:** [CONFIRM email address, e.g. privacy@primetours.co.za]

We hold ourselves to the **EU GDPR** standard for all visitors, wherever you are. It is the strictest regime that applies to us, and applying one standard to everyone is simpler than working out which rules you fall under.

## What we collect

### When you contact us
If you use our contact form we collect your name, email address, and whatever you write in the message. We use this to reply to you and for nothing else. We do not add you to a mailing list on the basis of a contact form submission.

### When you subscribe to the newsletter
[CONFIRM — only if a newsletter launches] Your email address, and the date you subscribed. You can unsubscribe from any email.

### Automatically, when you browse
- **Analytics data** via Google Analytics 4: pages viewed, approximate location (country/city level), device and browser type, how you arrived, and which outbound links you clicked.
- **Server logs** kept by our host, including IP address, for security and troubleshooting.
- **Affiliate tracking cookies** set by GetYourGuide and Viator when you click through to them. See below.

We do not collect payment details, because we never take payments. Bookings happen on the booking platform's own site under their privacy policy.

## Cookies

### Cookies we set

| Type | Purpose | Set by | Lifetime | Needs consent |
|---|---|---|---|---|
| Analytics | Understanding what's read and what's useful | Google (GA4) | Up to 2 years | **Yes** |
| Functional | Site operation, caching, security | Prime Tours, Cloudflare | Session to 1 year | No — strictly necessary |

Analytics cookies are set **only if you agree**. Decline and the site works exactly as before; we simply learn less about what's useful.

### Affiliate cookies, which are not ours

This is the part most travel sites gloss over, so here it is plainly.

When you click a booking link here, you leave this site. The booking platform then sets a cookie **on its own domain, on its own site**, identifying us as the referrer. If you book within its lifetime, we're credited with a commission. **GetYourGuide's cookie lasts 31 days; Viator's 30 days.**

We don't set those cookies, can't read them, and they don't tell us who you are. They're governed by [GetYourGuide's](https://www.getyourguide.com/privacy_policy) and [Viator's](https://www.viator.com/support/privacyPolicy) own privacy policies and their own consent banners, which you'll see on their sites.

We describe them here because you should understand how this site is funded — not because we control them.

## Why we're allowed to process it

| Data | Lawful basis (EU GDPR) |
|---|---|
| Contact form | Legitimate interest — replying to you |
| Newsletter | Consent |
| Analytics (GA4) | **Consent** |
| Server logs | Legitimate interest — security |

Affiliate cookies don't appear here because we don't set them — see the section above.

## Who we share it with

We don't sell your data. We never have and we won't.

Third parties that process data on our behalf, or that you interact with through this site:

| Who | What for | Where |
|---|---|---|
| **Google** | Analytics (GA4), Tag Manager | US / global |
| **Hostinger** | Web hosting, server logs | [CONFIRM — United Kingdom once the planned migration completes] |
| **Cloudflare** | CDN, security | Global edge network |
| **[CONFIRM form provider]** | Contact form delivery | [CONFIRM] |
| **[CONFIRM newsletter provider]** | Email delivery | [CONFIRM] |
| **GetYourGuide, Viator** | Booking, when you click through | EU / US |

**International transfers.** Hosting is moving to the United Kingdom, so for UK visitors data stays in the UK. Some processing still happens outside the UK and EU — Google's analytics infrastructure and Cloudflare's global edge network in particular. [CONFIRM — the transfer mechanism relied on for those, e.g. Standard Contractual Clauses or the UK–US Data Bridge. Needs the reviewer.]

## How long we keep it

- **Contact form messages:** 24 months, then deleted
- **Newsletter subscriptions:** until you unsubscribe
- **Analytics:** 14 months (GA4 retention setting)
- **Server logs:** [CONFIRM — per Hostinger's policy]

## Your rights

You have the right to access your data, correct it, have it deleted, restrict or object to processing, take it elsewhere, and withdraw consent at any time. Withdrawing consent is as easy as giving it — the cookie settings link in our footer reopens the banner.

**We extend these rights to every visitor**, not only those in the UK and EU. It is simpler than working out which regime applies to you, and you should not have fewer rights because of where you happen to live.

Write to [CONFIRM email address] and we'll respond within 30 days.

You can also complain to a regulator: the **Information Commissioner's Office** (UK), or your national data protection authority if you're in the EU.

## Children

This site is for adults planning travel. We don't knowingly collect data from anyone under 18.

## Changes

If we change this policy we'll update the date at the top. Material changes will be flagged on the site.

## Contact

[CONFIRM email address] — or via our [contact page](/contact/).

---

## Notes for review

### ⚠️ Publishing a contact address conflicts with an existing project rule

As a UK sole trader you'll need to give a contact address on the site — a data controller has to be reachable, and UK e-commerce rules generally expect a geographic address rather than an email alone.

For a sole trader with no separate business premises, that usually defaults to a **home address**. Which directly contradicts the rule in `identity.md` that Andrew's location is never named anywhere on the site.

**The standard resolution is a service address** — a mail-forwarding or registered-office provider, typically £20–50 a year. It satisfies the requirement, keeps your home address off a public website, and doesn't compromise anything. I'd raise it with the reviewer rather than defaulting to a home address by omission.

### Two questions specifically for the reviewer

**1. Does POPIA still apply?** Probably not, and it would simplify things if not. The controller is now UK-established, hosting is moving to the UK, and no processing takes place in South Africa. The `.co.za` domain and South African subject matter don't obviously trigger POPIA on their own. Worth a definitive answer rather than complying with a regime that may not apply.

**2. Is an EU representative needed under Article 27?** A UK-established controller offering services to EU residents can fall within EU GDPR extraterritorially. Given a deliberately EU-targeted audience — Germany is the fastest-growing market — this needs checking. There are exemptions for occasional, low-risk processing that may well cover a content site with analytics, but it should be confirmed rather than assumed.

### 🔴 Cookie consent — recommendation now made

**Use Complianz (free tier).** Reasoning in `build.md` §4: self-hosted so no third-party script call, supports the WP Consent API and Google Consent Mode v2, and is **region-aware** so the banner shows only where consent is legally required.

**And a correction to something I flagged twice as a revenue risk:** consent does not cost you commission. Affiliate cookies are set by the OTA on the OTA's domain after the visitor has left this site — our `/go/` links are plain redirects with no third-party script. There is no OTA cookie on primetours.co.za for the banner to block. A declined consent costs **analytics visibility only**; the visitor can still click through, book, and be attributed normally.

That removes any reason to configure consent aggressively — there is nothing to gain.

⚠️ **This holds only while `/go/` stays a plain redirect.** Adding an embedded GetYourGuide or Viator widget would load third-party scripts on our pages, set cookies on our domain, and require consent. Revisit this section if one is ever added.

Once Complianz is configured, the cookie section above must describe what is actually implemented.

### Facts still needed

1. **Contact address** — see the service-address note above.
2. **Data protection email** — `privacy@primetours.co.za` would do.
3. **Form and newsletter providers**, once chosen.
4. **Whether a newsletter is launching at all** — if not, cut that section rather than describing something hypothetical.
5. **Hostinger's server log retention period.**

### One thing I'd keep

The affiliate cookie section is more explicit than most sites bother with, naming both platforms and their cookie lifetimes. It costs nothing, it is consistent with the "how we make money" page, and a reader who checks will find the site telling the truth about something it could easily have obscured.
