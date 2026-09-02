#!/bin/bash
# Build script for creating distribution zip
# Only includes production files and runtime dependencies

set -e

PLUGIN_SLUG="contextual-related-posts"
BUILD_DIR="build"
TEMP_DIR="$BUILD_DIR/$PLUGIN_SLUG"

echo "Creating distribution zip for $PLUGIN_SLUG..."

# Clean build directory
rm -rf "$BUILD_DIR"
mkdir -p "$TEMP_DIR"

# Build production vendor with Composer autoloader.
echo "Building production vendor..."
composer build:vendor

# Copy plugin files (excluding dev/build artifacts and all of vendor)
echo "Copying plugin files..."
rsync -av --exclude-from=- . "$TEMP_DIR/" <<EOF
.*
.git/
.github/
node_modules/
phpcompat-tools/
phpunit/
/build/
vendor/
dev-helpers/
dev-tools/
wporg-assets/
test-tools/
docs/
build-assets.js
*.dist
*.yml
*.neon
composer.json
composer.lock
package.json
package-lock.json
pnpm-lock.yaml
pnpm-workspace.yaml
phpstan-bootstrap.php
build-zip.sh
CODE_OF_CONDUCT.md
CONTRIBUTING.md
ISSUE_TEMPLATE.md
PULL_REQUEST_TEMPLATE.md
CLAUDE.md
AGENTS.md
EOF

# Copy runtime Composer dependencies and generated autoloader.
# The vendor dirs are derived from composer.lock's non-dev packages, so adding a
# runtime dependency to composer.json ships it automatically — no edit here.
echo "Copying vendor dependencies..."
if [ ! -f "vendor/autoload.php" ] || [ ! -d "vendor/composer" ]; then
    echo "Error: Composer autoloader not found. Run 'composer build:vendor' first." >&2
    exit 1
fi

VENDOR_DIRS=$(php -r '$lock = json_decode( (string) @file_get_contents( "composer.lock" ), true ); if ( empty( $lock["packages"] ) ) { exit( 1 ); } $dirs = array(); foreach ( $lock["packages"] as $package ) { $dirs[ explode( "/", $package["name"] )[0] ] = 1; } echo implode( " ", array_keys( $dirs ) );') || {
    echo "Error: could not derive runtime vendor dirs from composer.lock." >&2
    exit 1
}

if [ -z "$VENDOR_DIRS" ]; then
    echo "Error: no runtime vendor dirs derived from composer.lock." >&2
    exit 1
fi

mkdir -p "$TEMP_DIR/vendor"
for vendor_dir in $VENDOR_DIRS; do
    if [ ! -d "vendor/$vendor_dir" ]; then
        echo "Error: vendor/$vendor_dir not found. Run 'composer build:vendor' first." >&2
        exit 1
    fi
    cp -r "vendor/$vendor_dir" "$TEMP_DIR/vendor/"
    echo "  + vendor/$vendor_dir"
done
cp -r vendor/composer "$TEMP_DIR/vendor/"
cp vendor/autoload.php "$TEMP_DIR/vendor/"

# Create zip
echo "Creating zip file..."
cd "$BUILD_DIR"
zip -r "$PLUGIN_SLUG.zip" "$PLUGIN_SLUG/" -q

echo "✓ Distribution zip created: $BUILD_DIR/$PLUGIN_SLUG.zip"
cd ..

# Show zip contents summary
echo ""
echo "Zip contents summary:"
unzip -l "$BUILD_DIR/$PLUGIN_SLUG.zip" | tail -1
