#!/usr/bin/env bash
#
# Prime Tours — local WordPress bootstrap.
#
# Idempotent: safe to re-run. Run from dev-site/ with DDEV already started.
#   cd dev-site && ddev start && bash scripts/setup.sh
#
set -euo pipefail

SITE_URL="https://primetours.ddev.site"
SITE_TITLE="Prime Tours — Independent Cape Town Travel Guide"
ADMIN_USER="andrew"
ADMIN_PASS="primetours-local-only"
ADMIN_EMAIL="andrew@ucc.co.za"

say() { printf '\n\033[1;32m==>\033[0m %s\n' "$1"; }

if ! command -v ddev >/dev/null 2>&1; then
  echo "DDEV not found. Install: https://ddev.readthedocs.io/en/stable/users/install/"
  exit 1
fi

# ---------------------------------------------------------------- core
say "Installing Composer dependencies"
ddev composer install

say "Installing WordPress"
if ddev wp core is-installed 2>/dev/null; then
  echo "Already installed — skipping."
else
  ddev wp core install \
    --url="${SITE_URL}" \
    --title="${SITE_TITLE}" \
    --admin_user="${ADMIN_USER}" \
    --admin_password="${ADMIN_PASS}" \
    --admin_email="${ADMIN_EMAIL}" \
    --skip-email
fi

# ------------------------------------------------------------- theme
say "Activating theme"
ddev wp theme activate primetours

# ----------------------------------------------------------- plugins
say "Activating plugins"
for plugin in \
  seo-by-rank-math \
  litespeed-cache \
  generateblocks \
  advanced-custom-fields \
  thirstyaffiliates \
  fluentform \
  wp-mail-smtp \
  better-wp-security
do
  ddev wp plugin activate "${plugin}" 2>/dev/null || echo "  ! ${plugin} not installed yet"
done

# UpdraftPlus is production-only; no value locally.
ddev wp plugin deactivate updraftplus 2>/dev/null || true

# -------------------------------------------------------- permalinks
say "Configuring permalinks and options"
ddev wp rewrite structure '/%postname%/' --hard
ddev wp rewrite flush --hard

ddev wp option update blogdescription "Straight answers about Cape Town"
ddev wp option update timezone_string "Africa/Johannesburg"
ddev wp option update date_format "j F Y"
ddev wp option update start_of_week 1

# Local is never indexed.
ddev wp option update blog_public 0

# Discard WordPress defaults.
ddev wp post delete 1 --force 2>/dev/null || true   # Hello world
ddev wp post delete 2 --force 2>/dev/null || true   # Sample page

# ------------------------------------------------------- taxonomies
say "Seeding regions"
ddev wp term create region "Cape Town" --slug=cape-town 2>/dev/null || true
CT_ID=$(ddev wp term list region --slug=cape-town --field=term_id 2>/dev/null | tr -d '\r')
for child in "City Bowl:city-bowl" "Cape Peninsula:cape-peninsula" "Winelands:winelands" "Overberg:overberg"; do
  name="${child%%:*}"; slug="${child##*:}"
  ddev wp term create region "${name}" --slug="${slug}" --parent="${CT_ID}" 2>/dev/null || true
done

say "Seeding experience types"
for t in "Wildlife:wildlife" "Wine:wine" "History:history" "Adventure:adventure" "Scenic:scenic" "Cultural:cultural"; do
  name="${t%%:*}"; slug="${t##*:}"
  ddev wp term create experience_type "${name}" --slug="${slug}" 2>/dev/null || true
done

# ------------------------------------------------------------ acf
say "Ensuring ACF JSON sync directory"
mkdir -p wp-content/acf-json

say "Done"
cat <<EOF

  Site:  ${SITE_URL}
  Admin: ${SITE_URL}/wp-admin
  User:  ${ADMIN_USER}
  Pass:  ${ADMIN_PASS}   (local only — never reuse)

  Next: create ACF field groups for the 'experience' post type.
        They will auto-sync to wp-content/acf-json/ and must be committed.
        Field list: build.md §5

EOF
