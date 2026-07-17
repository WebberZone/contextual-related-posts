# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Plugin Overview

Contextual Related Posts is a WordPress plugin that displays related posts using FULLTEXT search. Namespace: `WebberZone\Contextual_Related_Posts`. Prefix: `crp`. Requires WordPress 6.6+, PHP 7.4+.

This repository contains the **free version** of the plugin. The Pro add-on (`contextual-related-posts-pro`) is a separate plugin; this codebase has integration points for it (e.g. the `Pro\Pro` property on `Main`, asset paths under `includes/pro/`) but the Pro code is not present here.

**Current work-in-progress version: 4.3.0.** Use `4.3.0` for all `@since` tags on new code until this version is released.

webberzone.com: <https://webberzone.com/plugins/contextual-related-posts/>

Constants defined in `contextual-related-posts.php`: `WZ_CRP_VERSION`, `WZ_CRP_PLUGIN_FILE`, `WZ_CRP_PLUGIN_DIR`, `WZ_CRP_PLUGIN_URL`, `WZ_CRP_DEFAULT_THUMBNAIL_URL`, `CRP_MAX_WORDS`, `CRP_CACHE_TIME`, `WZ_CRP_DB_VERSION`.

Settings prefix/key: `crp` / `crp_settings` (wp_options). Access via `crp_get_option($key)` / `crp_get_settings()`.

## Commands

### PHP

```bash
composer phpcs          # Lint PHP (WordPress coding standards)
composer phpcbf         # Auto-fix PHP code style
composer phpstan        # Static analysis (level 5)
composer phpcompat      # Check PHP 7.4–8.5 compatibility
composer test           # Run all checks (phpcs + phpcompat + phpstan)
composer zip            # Create distribution zip (./build-zip.sh)
```

### JavaScript/CSS Blocks

```bash
npm run build           # Build free blocks
npm run build:pro       # Build pro blocks (query, featured-image, related-posts-pro)
npm run build:all       # Build all blocks
npm start               # Watch free blocks
npm run start:pro       # Watch pro blocks
npm run build:assets    # Minify CSS/JS, generate RTL CSS (node build-assets.js)
npm run lint:js         # ESLint
npm run lint:css        # Stylelint
npm run zip             # Create plugin zip (wp-scripts plugin-zip)
```

## Architecture

### Entry Point & Bootstrap

`contextual-related-posts.php` defines constants (`WZ_CRP_VERSION`, `WZ_CRP_PLUGIN_FILE`, `WZ_CRP_PLUGIN_DIR`, etc.), registers the custom PSR-4 autoloader, loads Freemius (`load-freemius.php`, `is_premium => false` — used for the upgrade/account flow), and calls `\WebberZone\Contextual_Related_Posts\load()` on `plugins_loaded`. It also directly loads `includes/options-api.php`, `includes/class-crp-query.php`, and `includes/functions.php`.

**Autoloader convention:** Namespace segments become path segments; underscores → hyphens, lowercase, last segment prefixed with `class-`. e.g. `WebberZone\Contextual_Related_Posts\Admin\Settings` → `includes/admin/class-settings.php`. Traits follow the same pattern with a `trait-` prefix instead.

### Core Components

- **`includes/class-main.php`** — Singleton (`Main::get_instance()`). Instantiates frontend subsystems immediately and admin components on `init`. Holds typed properties for `Admin\Admin`, `Admin\Network\Admin`, `Frontend\Shortcodes`, `Frontend\Blocks\Blocks`, `Frontend\Styles_Handler`, `Frontend\Language_Handler`, and `Pro\Pro` (null in the free version).
- **`includes/class-hook-loader.php`** — Centralizes WordPress hook registration (content filters, query hooks, init hooks, cache hooks).
- **`includes/util/class-hook-registry.php`** — Static registry tracking all registered actions/filters; prevents duplicates.

### Query Engine

- **`CRP_Query`** (`includes/class-crp-query.php`) extends `WP_Query`; uses FULLTEXT search on the posts table.
- **`CRP_Core_Query`** (`includes/class-crp-core-query.php`, ~47 KB) — Core algorithm: builds SQL, joins, ordering. The most complex file in the codebase.

### Frontend

- **`Display`** (`includes/frontend/class-display.php`, ~33 KB) — Renders related posts HTML.
- **`Media_Handler`** (`includes/frontend/class-media-handler.php`) — Resolves thumbnails via a priority strategy chain: custom meta → ACF field → FIFU plugin → featured image → content scan → first child attachment → video meta → configured default → site icon. Designed for multi-plugin reuse: subclasses override `get_option()` to use their own options function; never call `crp_get_option()` directly inside the class.
- **`Shortcodes`** — `[crp]` shortcode.
- **`Blocks`** — Free block at `includes/frontend/blocks/src/related-posts/`.
- **`REST_API`** (`includes/frontend/class-rest-api.php`) — Extends `WP_REST_Controller`; REST endpoints for block editor.
- **`Styles_Handler`** / **`Language_Handler`** — Enqueue plugin CSS and handle i18n for JS, respectively.
- **`Widgets\Related_Posts_Widget`** (`includes/frontend/widgets/`) — Classic WordPress widget, registered on `widgets_init`.

### Admin

- **`Settings`** (`includes/admin/class-settings.php`, ~92 KB) — Settings page with tabs for General, Performance, List tuning, Output, Thumbnail, Styles, Feed, and WooCommerce (conditional on `crp_is_woocommerce_active()`).
- Settings stored as a single `crp_settings` array in `wp_options`. Access via `crp_get_option($key)` / `crp_get_settings()`.
- Additional admin classes: `Admin\Admin` (loader), `Admin\Network\Admin`, `Admin\Activator`, `Admin\Metabox`, `Admin\Bulk_Edit`, `Admin\DB`, `Admin\Admin_Notices` / `Admin\Admin_Notices_API`, `Admin\Admin_Banner`, `Admin\Tools_Page`, `Admin\Settings_Wizard`. Settings framework lives under `includes/admin/settings/` (`Settings_API`, `Settings_Form`, `Settings_Sanitize`, `Metabox_API`, `Settings_Wizard_API`).

### Pro Integration

The `Main` class declares a `?Pro\Pro $pro` property (null in the free version) and several admin classes reference asset paths under `includes/pro/` for when the Pro plugin is active. The Pro plugin itself is not part of this repository.

### Utilities (`includes/util/`)

- **`Cache`** — Caches query output per post (configurable TTL, default 1 week). Clears cache on post trash/untrash.
- **`Helpers`** — Shared helper functions.
- **`Migration_Service`** — Database migration utilities.

## Key Patterns

- **Settings access:** Always use `crp_get_option($key, $default)` rather than accessing `$crp_settings` directly.
- **Hook registration:** Add hooks through `Hook_Registry::add_action()` / `Hook_Registry::add_filter()` (not directly via WordPress functions) so they're tracked and deduplication is handled.
- **Freemius:** This free version loads Freemius with `is_premium => false`. Use `crp_freemius()->is_paying()` to gate upgrade/account UI; `is__premium_only()` / `can_use_premium_code()` are only relevant in the Pro plugin.
- **Block builds:** Free blocks built with `wp-scripts`; pro blocks use a separate webpack entry. Run the appropriate build command after editing block source files.
