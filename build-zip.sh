#!/bin/bash
# Build script for creating distribution zip
# Only includes production files and runtime dependencies

set -e

PLUGIN_SLUG="contextual-related-posts"
BUILD_DIR="build"
TEMP_DIR="$BUILD_DIR/$PLUGIN_SLUG"

echo "Creating distribution zip for $PLUGIN_SLUG..."

# Install production-only vendor dependencies
echo "Installing Composer production dependencies..."
composer install --no-dev --optimize-autoloader --classmap-authoritative --quiet

# Clean build directory
rm -rf "$BUILD_DIR"
mkdir -p "$TEMP_DIR"

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

# Restore dev dependencies for local development
echo "Restoring Composer dev dependencies..."
composer install --quiet
