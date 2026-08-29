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
echo "Copying vendor dependencies..."
if [ -d "vendor/freemius" ] && [ -f "vendor/autoload.php" ] && [ -d "vendor/composer" ]; then
    mkdir -p "$TEMP_DIR/vendor"
    cp -r vendor/freemius "$TEMP_DIR/vendor/"
    cp -r vendor/composer "$TEMP_DIR/vendor/"
    cp vendor/autoload.php "$TEMP_DIR/vendor/"
else
    echo "Error: vendor files not found. Run 'composer build:vendor' first."
    exit 1
fi

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
