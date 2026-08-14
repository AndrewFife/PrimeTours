#!/usr/bin/env python3
"""
Prime Tours — pre-publish lint.

Mechanical rules, mechanically enforced. This exists so Andrew's review
time goes to the one thing only he can do: confirming the claims are true.

It does NOT check whether anything is factually correct. It cannot. That
is the human gate, and it never becomes automatic — see build.md §10.

Usage:
    python3 scripts/prepublish_lint.py content/drafts/article.md
    python3 scripts/prepublish_lint.py content/drafts/          # whole dir
    python3 scripts/prepublish_lint.py --warnings-as-errors ...

Exit codes:  0 clean (warnings allowed) · 1 blocking errors · 2 bad usage
"""

from __future__ import annotations

import argparse
import re
import sys
from dataclasses import dataclass, field
from pathlib import Path

# --------------------------------------------------------------------------
# Rules. Sourced from CLAUDE.md and identity.md — keep them in sync.
# --------------------------------------------------------------------------

# Positioning failures. These carry legal weight, not just style.
BANNED_OPERATOR = {
    r"\bour tours?\b": 'implies Prime Tours operates tours — use "the tours we recommend"',
    r"\bbook with us\b": 'implies direct sale — use "where to book"',
    r"\bwe offer\b": 'implies Prime Tours sells — use "the operators we rate offer"',
    r"\bour guides?\b": 'implies employed guides — use "the operators we rate"',
    r"\bcreate your (own )?tour\b": 'legacy operator CTA — use "plan your trip"',
    r"\bprime tours (specialises|specializes)\b": "legacy operator positioning",
    r"\bwe (run|operate|staff) (these |the )?tours?\b": "Prime Tours is not the operator",
    r"\bjoin us on\b": "implies Prime Tours hosts the tour",
    r"\bour (drivers?|vehicles?|fleet)\b": "implies operational assets",
}

# Voice failures. strategy.md §3.
BANNED_STYLE = {
    r"\bhidden gems?\b": None,
    r"\bnestled\b": None,
    r"\bbreathtaking\b": None,
    r"\bmust[- ]see\b": None,
    r"\bunforgettable experience\b": None,
    r"\bworld[- ]class\b": None,
    r"\ba feast for the senses\b": None,
    r"\blike no other\b": None,
    r"\bstunning vistas?\b": None,
    r"\bnestling\b": None,
}

MOTHER_CITY_LIMIT = 1  # per article

# Raw OTA URLs must never appear — everything goes through ThirstyAffiliates.
RAW_AFFILIATE = re.compile(
    r"https?://(?:www\.)?(getyourguide|viator|tiqets|klook)\.[a-z.]+"
    r"(?P<path>/[^\s\)\"'\]]*)?",
    re.I,
)

# ...except non-commercial pages on those same domains. Linking a reader to an
# OTA's help centre when their booking has gone wrong is a service to them and
# earns us nothing — it should not be cloaked, and it is not a leak.
NON_COMMERCIAL_PATH = re.compile(
    r"/(support|help|contact|customer|privacy|terms|legal)", re.I
)

CLOAKED_LINK = re.compile(r"/go/[a-z0-9\-/]+", re.I)

REQUIRED_FRONTMATTER = ["title", "slug", "last_verified_date", "author"]

DISCLOSURE_HINTS = ["earn a commission", "we earn commission", "affiliate", "costs you nothing extra"]


# --------------------------------------------------------------------------

@dataclass
class Report:
    path: Path
    errors: list[str] = field(default_factory=list)
    warnings: list[str] = field(default_factory=list)

    @property
    def ok(self) -> bool:
        return not self.errors


def split_frontmatter(text: str) -> tuple[dict[str, str], str, int]:
    """Return (frontmatter, body, body_line_offset)."""
    if not text.startswith("---"):
        return {}, text, 0
    end = text.find("\n---", 3)
    if end == -1:
        return {}, text, 0
    raw = text[3:end]
    meta: dict[str, str] = {}
    for line in raw.splitlines():
        if ":" in line and not line.strip().startswith("#"):
            k, _, v = line.partition(":")
            meta[k.strip().lower()] = v.strip().strip("\"'")
    offset = text[: end + 4].count("\n") + 1
    return meta, text[end + 4 :], offset


def line_of(body: str, index: int, offset: int) -> int:
    return body[:index].count("\n") + 1 + offset


def strip_code(body: str) -> str:
    """Blank out fenced code so examples don't trip the rules."""
    return re.sub(r"```.*?```", lambda m: "\n" * m.group(0).count("\n"), body, flags=re.S)


def lint(path: Path) -> Report:
    rep = Report(path)
    text = path.read_text(encoding="utf-8")
    meta, body, offset = split_frontmatter(text)
    prose = strip_code(body)

    # -- frontmatter -------------------------------------------------------
    for key in REQUIRED_FRONTMATTER:
        if not meta.get(key):
            rep.errors.append(f"frontmatter: missing required field '{key}'")

    if (d := meta.get("last_verified_date")) and not re.match(r"^\d{4}-\d{2}-\d{2}$", d):
        rep.errors.append(f"frontmatter: last_verified_date '{d}' must be YYYY-MM-DD")

    # -- unresolved placeholders ------------------------------------------
    for m in re.finditer(r"\[CONFIRM[^\]]*\]", text, re.I):
        rep.errors.append(
            f"line {line_of(text, m.start(), 0)}: unresolved placeholder {m.group(0)}"
        )

    # -- positioning -------------------------------------------------------
    for pattern, why in BANNED_OPERATOR.items():
        for m in re.finditer(pattern, prose, re.I):
            rep.errors.append(
                f"line {line_of(prose, m.start(), offset)}: operator phrasing "
                f'"{m.group(0)}" — {why}'
            )

    # -- voice -------------------------------------------------------------
    for pattern in BANNED_STYLE:
        for m in re.finditer(pattern, prose, re.I):
            rep.errors.append(
                f'line {line_of(prose, m.start(), offset)}: banned phrase "{m.group(0)}"'
            )

    mother_city = list(re.finditer(r"\bmother city\b", prose, re.I))
    if len(mother_city) > MOTHER_CITY_LIMIT:
        rep.warnings.append(
            f'"the Mother City" used {len(mother_city)}x — limit is {MOTHER_CITY_LIMIT}'
        )

    # -- affiliate hygiene -------------------------------------------------
    raw_links = [
        m
        for m in RAW_AFFILIATE.finditer(prose)
        if not NON_COMMERCIAL_PATH.match(m.group("path") or "")
    ]
    for m in raw_links:
        rep.errors.append(
            f"line {line_of(prose, m.start(), offset)}: raw affiliate URL "
            f"({m.group(1)}) — must route through ThirstyAffiliates as /go/..."
        )

    has_affiliate = bool(raw_links or CLOAKED_LINK.search(prose))
    if has_affiliate:
        low = prose.lower()
        if not any(h in low for h in DISCLOSURE_HINTS):
            rep.errors.append(
                "affiliate link present but no disclosure found — required by "
                "FTC (US), ASA (UK) and EU rules"
            )

    # -- required components ----------------------------------------------
    # The homepage is exempt: its hero already front-loads the answer, and a
    # Quick Answer box directly beneath it would be duplication, not clarity.
    is_homepage = meta.get("slug") in ("/", "home", "homepage")

    # Legal pages are exempt too. The Quick Answer box and question-phrased
    # headings exist to make editorial content extractable by AI answer
    # engines. A privacy policy or terms page is a legal instrument, not
    # query-targeted content, and should be structured for a lawyer and a
    # regulator rather than for retrieval.
    is_legal = str(meta.get("legal_review_required", "")).lower() == "true"

    if not (is_homepage or is_legal) and "pt-quick-answer" not in body and "## Quick answer" not in body:
        rep.errors.append(
            "missing Quick Answer box — the first 150-200 words must answer "
            "the page's core question (GEO requirement, build.md §6)"
        )

    if "pt-byline" not in body and not meta.get("author"):
        rep.errors.append("missing byline — named authorship is the primary E-E-A-T signal")

    # -- structure ---------------------------------------------------------
    words = len(re.findall(r"\b\w+\b", prose))
    if words < 600:
        rep.warnings.append(f"{words} words — thin for a commercial page")

    if not is_legal and not re.search(r"^##\s+.*\?", prose, re.M):
        rep.warnings.append(
            "no question-phrased heading — these are what AI answer engines extract"
        )

    if re.search(r"\bR\d", prose) and not re.search(r"[$£€]", prose):
        rep.warnings.append(
            "ZAR prices without USD/GBP/EUR equivalents — audience is US/UK/EU"
        )

    if re.search(r"\b\d+\s?km\b", prose, re.I) and not re.search(r"\bmiles?\b", prose, re.I):
        rep.warnings.append("distances in km without miles")

    return rep


def main() -> int:
    ap = argparse.ArgumentParser(description="Prime Tours pre-publish lint")
    ap.add_argument("paths", nargs="+", type=Path)
    ap.add_argument("--warnings-as-errors", action="store_true")
    args = ap.parse_args()

    targets: list[Path] = []
    for p in args.paths:
        if p.is_dir():
            targets.extend(sorted(p.rglob("*.md")))
        elif p.is_file():
            targets.append(p)
        else:
            print(f"not found: {p}", file=sys.stderr)
            return 2

    if not targets:
        print("No markdown files found.", file=sys.stderr)
        return 2

    failed = 0
    for path in targets:
        rep = lint(path)
        blocked = rep.errors or (args.warnings_as_errors and rep.warnings)

        if blocked:
            failed += 1
            print(f"\n\033[1;31m✗ {path}\033[0m")
        elif rep.warnings:
            print(f"\n\033[1;33m~ {path}\033[0m")
        else:
            print(f"\n\033[1;32m✓ {path}\033[0m")

        for e in rep.errors:
            print(f"  \033[31mERROR\033[0m  {e}")
        for w in rep.warnings:
            print(f"  \033[33mwarn\033[0m   {w}")

    print()
    if failed:
        print(f"\033[1;31m{failed} of {len(targets)} file(s) blocked from publishing.\033[0m")
        return 1

    print(f"\033[1;32mAll {len(targets)} file(s) passed mechanical checks.\033[0m")
    print("Facts still need Andrew's verification — that gate is not automated.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
