# AGENTS.md

This file provides guidance to Codex (Codex.ai/code) when working with code in this repository.

## Plugin Overview

Contextual Related Posts Pro is a WordPress plugin (v4.2.3) that displays related posts using FULLTEXT search. Namespace: `WebberZone\Contextual_Related_Posts`. Requires WordPress 6.6+, PHP 7.4+.

## Commands

### PHP
```bash
composer phpcs          # Lint PHP (WordPress coding standards)
composer phpcbf         # Auto-fix PHP code style
composer phpstan        # Static analysis (level 5)
composer phpcompat      # Check PHP 7.4–8.5 compatibility
composer test           # Run all checks (phpcs + phpcompat + phpstan)
```

### JavaScript/CSS Blocks
```bash
npm run build           # Build free blocks
npm run build:pro       # Build pro blocks (query, featured-image, related-posts-pro)
npm run build:all       # Build all blocks
npm start               # Watch free blocks
npm run start:pro       # Watch pro blocks
npm run lint:js         # ESLint
npm run lint:css        # Stylelint
npm run zip             # Create distribution zip
```

### Asset Building
```bash
node build-assets.js    # Minify CSS/JS, generate RTL CSS
```

## Architecture

### Entry Point & Bootstrap
`contextual-related-posts.php` defines constants (`WZ_CRP_VERSION`, `WZ_CRP_PLUGIN_FILE`, `WZ_CRP_PLUGIN_DIR`, etc.), registers the custom PSR-4 autoloader, loads Freemius, and calls `\WebberZone\Contextual_Related_Posts\load()`.

**Autoloader convention:** Namespace segments become path segments; underscores → hyphens, lowercase, last segment prefixed with `class-`. e.g. `WebberZone\Contextual_Related_Posts\Admin\Settings` → `includes/admin/class-settings.php`. Traits follow the same pattern with a `trait-` prefix instead.

### Core Components
- **`includes/class-main.php`** — Singleton. Instantiates all subsystems on `plugins_loaded`.
- **`includes/class-hook-loader.php`** — Centralizes WordPress hook registration (content filters, query hooks, init hooks).
- **`includes/util/class-hook-registry.php`** — Static registry tracking all registered actions/filters; prevents duplicates.

### Query Engine
- **`CRP_Query`** extends `WP_Query`; uses FULLTEXT search on the posts table.
- **`CRP_Core_Query`** (`includes/class-crp-core-query.php`, ~45 KB) — Core algorithm: builds SQL, joins, ordering. The most complex file in the codebase.

### Frontend
- **`Display`** (`includes/frontend/class-display.php`, ~32 KB) — Renders related posts HTML.
- **`Media_Handler`** (`includes/frontend/class-media-handler.php`) — Resolves thumbnails via a priority strategy chain: custom meta → ACF field → FIFU plugin → featured image → content scan → first child attachment → video meta → configured default → site icon. Designed for multi-plugin reuse: subclasses override `get_option()` to use their own options function; never call `crp_get_option()` directly inside the class.
- **`Shortcodes`** — `[crp]` shortcode.
- **`Blocks`** — Free block at `includes/frontend/blocks/src/related-posts/`.
- **`REST_API`** — REST endpoints for block editor.
- **`Styles_Handler`** / **`Language_Handler`** — Enqueue plugin CSS and handle i18n for JS, respectively.

### Admin
- **`Settings`** (`includes/admin/class-settings.php`, ~87 KB) — Settings page with tabs for General, Content, Exclusions, Cache, Advanced.
- Settings stored as a single `crp_settings` array in `wp_options`. Access via `crp_get_option($key)` / `crp_get_settings()`.

### Pro Features (`includes/pro/`)
- **`Query_Modifier`** — Advanced filtering/sorting.
- **`Bot_Protection_Module`** — Excludes bot traffic.
- **`Custom_Tables`** — Optimized database tables for large sites.
- **`WooCommerce\WooCommerce_Module`** — WooCommerce product relations.
- **`CLI\CLI_Manager`** — WP-CLI commands (cache, database, table, content operations).
- **Pro Blocks:** `query/`, `featured-image/`, `related-posts-pro/` at `includes/pro/blocks/src/`.

Pro features are gated by `crp_freemius()->is__premium_only()` or `crp_freemius()->can_use_premium_code__premium_only()`.

### Utilities (`includes/util/`)
- **`Cache`** — Caches query output per post (configurable TTL, default 1 week).
- **`Helpers`** — Shared helper functions.
- **`Migration_Service`** — Database migration utilities.

## Key Patterns

- **Settings access:** Always use `crp_get_option($key, $default)` rather than accessing `$crp_settings` directly.
- **Hook registration:** Add hooks through `Hook_Registry::add_action()` / `Hook_Registry::add_filter()` (not directly via WordPress functions) so they're tracked and deduplication is handled.
- **Pro gating:** Wrap pro-only code with `if ( crp_freemius()->is__premium_only() )` checks.
- **Block builds:** Free blocks built with `wp-scripts`; pro blocks use a separate webpack entry. Run the appropriate build command after editing block source files.
