#!/usr/bin/env bash
#
# Builds the installable plugin zip.
#
# It regenerates the precomputed results, runs every test, and refuses to
# package anything if a suite fails, because a zip that installs cleanly and
# calculates wrongly is worse than no zip at all.
#
# Usage: bash tools/build-zip.sh

set -euo pipefail

PLUGIN_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SLUG="calculatorr-core"
VERSION="$(grep -m1 "^ \* Version:" "$PLUGIN_DIR/$SLUG.php" | awk '{print $3}')"
OUT_DIR="${1:-$PLUGIN_DIR/../../../dist}"

cd "$PLUGIN_DIR"

echo "==> Regenerating precomputed results"
php tools/build-defaults.php > /tmp/calcr-defaults-in.json
node tools/build-defaults.js /tmp/calcr-defaults-in.json

echo "==> Linting"
for f in *.php includes/*.php calculators/*.php tests/*.php tools/*.php; do
	php -l "$f" > /dev/null
done
for f in assets/js/*.js tests/*.js tools/*.js; do
	node --check "$f"
done
# A JSON calculator that will not parse is a calculator that quietly vanishes
# from the site after an upgrade, which is the kind of failure nobody notices
# for a month.
for f in calculators/json/*.json; do
	[ -e "$f" ] || continue
	node -e "JSON.parse(require('fs').readFileSync('$f','utf8'))"
done
echo "    php and js clean"

echo "==> Refreshing the field dump the sandbox parity test runs against"
php tools/build-fixtures.php

echo "==> Tests"
php  tests/test-integrity.php  | tail -2
node tests/test-formulas.js    | tail -1
node tests/test-robustness.js  | tail -1
node tests/test-sandbox.js     | tail -1
php  tests/test-render.php     | tail -2
php  tests/test-settings.php   | tail -1
php  tests/test-seo-handover.php | tail -1
php  tests/test-json-calculators.php | tail -1

echo "==> Packaging $SLUG $VERSION"
mkdir -p "$OUT_DIR"
ZIP="$OUT_DIR/$SLUG-$VERSION.zip"
rm -f "$ZIP"

# Zipped from the parent so the archive contains one calculatorr-core folder,
# which is what WordPress expects from an uploaded plugin. Tests and build
# tooling are excluded: useful here, dead weight on a server.
( cd "$PLUGIN_DIR/.." && zip -qr "$ZIP" "$SLUG" \
	-x "$SLUG/tests/*" \
	-x "$SLUG/tools/*" \
	-x "$SLUG/content/*" \
	-x "$SLUG/design/*" \
	-x "$SLUG/.git*" \
	-x "$SLUG/node_modules/*" \
	-x "$SLUG/*.zip" \
	-x "$SLUG/.DS_Store" )

echo "==> Built $ZIP"
echo "    $(unzip -l "$ZIP" | tail -1 | awk '{print $2}') files, $(du -h "$ZIP" | cut -f1)"
