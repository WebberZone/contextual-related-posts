# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Plugin Overview

Contextual Related Posts is a WordPress plugin (v4.2.3) that displays related posts using FULLTEXT search. Namespace: `WebberZone\Contextual_Related_Posts`. Requires WordPress 6.6+, PHP 7.4+.

## Commands

### PHP
```bash
composer phpcs          # Lint PHP (WordPress coding standards)
composer phpcbf         # Auto-fix PHP code style
composer phpstan        # Static analysis (level 5)
composer phpcompat      # Check PHP 7.4–8.5 compatibility
composer test           # Run all checks (phpcs + phpcompat + phpstan)
```

### JavaScript/CSS
```bash
npm run build           # Build the free related-posts block
npm start               # Watch free block
npm run build:assets    # Minify CSS/JS, generate RTL CSS (via build-assets.js)
npm run lint:js         # ESLint
npm run lint:css        # Stylelint
```

Note: `package.json` also defines `build:pro` / `build:all` / `start:pro` scripts targeting `includes/pro/blocks/`, but this repo has no `includes/pro/` directory — those scripts are inherited from the shared config and will fail if run.

## Architecture

### Entry Point & Bootstrap
`contextual-related-posts.php` defines constants (`WZ_CRP_VERSION`, `WZ_CRP_PLUGIN_FILE`, `WZ_CRP_PLUGIN_DIR`, `WZ_CRP_PLUGIN_URL`, `WZ_CRP_DEFAULT_THUMBNAIL_URL`, `CRP_MAX_WORDS`, `CRP_CACHE_TIME`, `WZ_CRP_DB_VERSION`), loads Freemius, registers the custom autoloader, and calls `\WebberZone\Contextual_Related_Posts\load()` on `plugins_loaded`.

The entry file also handles mutual exclusivity: activating the free plugin deactivates the pro plugin and vice versa (via `crp_deactivate_other_instances()`).

**Autoloader convention:** Namespace segments become path segments; underscores → hyphens, lowercase, last segment prefixed with `class-`. e.g. `WebberZone\Contextual_Related_Posts\Admin\Settings` → `includes/admin/class-settings.php`. Traits follow the same pattern with a `trait-` prefix instead.

### Core Components
- **`includes/class-main.php`** — Singleton. Instantiates all subsystems on `plugins_loaded`.
- **`includes/class-hook-loader.php`** — Centralizes WordPress hook registration (content filters, query hooks, init hooks).
- **`includes/util/class-hook-registry.php`** — Static registry tracking all registered actions/filters; prevents duplicates.

### Query Engine
- **`CRP_Query`** extends `WP_Query`; uses FULLTEXT search on the posts table.
- **`CRP_Core_Query`** (`includes/class-crp-core-query.php`, ~45 KB) — Core algorithm: builds SQL, joins, ordering. The most complex file in the codebase.

### Frontend
- **`Display`** (`includes/frontend/class-display.php`) — Renders related posts HTML.
- **`Media_Handler`** (`includes/frontend/class-media-handler.php`) — Resolves thumbnails via a priority strategy chain: custom meta → ACF field → FIFU plugin → featured image → content scan → first child attachment → video meta → configured default → site icon. Subclasses override `get_option()` to use their own options function; never call `crp_get_option()` directly inside the class.
- **`Shortcodes`** — `[crp]` shortcode.
- **`Blocks`** — Single free block at `includes/frontend/blocks/src/related-posts/`.
- **`Widgets`** — Legacy widget at `includes/frontend/widgets/class-related-posts-widget.php`.
- **`REST_API`** — REST endpoints for block editor.
- **`Styles_Handler`** / **`Language_Handler`** — Enqueue plugin CSS and handle i18n for JS.

### Admin
- **`Settings`** (`includes/admin/class-settings.php`) — Settings page with tabs for General, Content, Exclusions, Cache, Advanced. Settings stored as a single `crp_settings` array in `wp_options`; access via `crp_get_option($key)` / `crp_get_settings()`.
- **`Network\Admin`** — Multisite network admin support (`includes/admin/network/`).
- **`Settings_Wizard`** / **`Bulk_Edit`** / **`Tools_Page`** / **`Metabox`** — Additional admin UI components.
- Settings API helpers split across `includes/admin/settings/`: `class-settings-api.php`, `class-settings-form.php`, `class-settings-sanitize.php`, `class-settings-wizard-api.php`, `class-metabox-api.php`.

### Utilities (`includes/util/`)
- **`Cache`** — Caches query output per post (configurable TTL, default 1 week).
- **`Helpers`** — Shared helper functions.
- **`Migration_Service`** — Database migration utilities.

## Key Patterns

- **Settings access:** Always use `crp_get_option($key, $default)` rather than accessing `$crp_settings` directly.
- **Hook registration:** Add hooks through `Hook_Registry::add_action()` / `Hook_Registry::add_filter()` (not directly via WordPress functions) so they're tracked and deduplication is handled.
- **No pro directory:** This is the free version. There is no `includes/pro/` directory and no pro feature gating. The pro version is a separate plugin (`contextual-related-posts-pro`); only one can be active at a time.
- **Block builds:** Only the `related-posts` block exists in this repo. Run `npm run build` after editing `includes/frontend/blocks/src/related-posts/`.
