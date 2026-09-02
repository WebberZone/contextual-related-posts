# AGENTS.md

Guidance for AI coding agents working in this repository.

## Response Rules

- Return only the changed function or section, not the full file
- No explanation unless asked
- No out-of-scope suggestions
- Skip preamble and trailing summaries

## Release Notes

See `dev-tools/CLAUDE.md`'s Changelog convention.

## Links

- GitHub (pro): <https://github.com/WebberZone/contextual-related-posts-pro>
- GitHub (free): <https://github.com/WebberZone/contextual-related-posts>
- Documentation: <https://webberzone.com/support/product/contextual-related-posts/>
- webberzone.com (free): <https://webberzone.com/plugins/contextual-related-posts/>
- webberzone.com (pro): <https://webberzone.com/plugins/contextual-related-posts/pro/>

## Plugin Overview

Contextual Related Posts Pro is a WordPress plugin displaying related posts via FULLTEXT search. Namespace: `WebberZone\Contextual_Related_Posts`. Prefix: `crp`. Requires WordPress 6.6+, PHP 7.4+.

**Current WIP version: 4.4.1.** Use for all `@since` tags on new code until released.

Constants defined in `contextual-related-posts.php`: `WZ_CRP_VERSION`, `WZ_CRP_PLUGIN_FILE`, `WZ_CRP_PLUGIN_DIR`, `WZ_CRP_PLUGIN_URL`, `WZ_CRP_DEFAULT_THUMBNAIL_URL`, `CRP_MAX_WORDS`, `CRP_CACHE_TIME`, `WZ_CRP_DB_VERSION`.

Settings prefix/key: `crp` / `crp_settings` (wp_options). Access via `crp_get_option($key)` / `crp_get_settings()`.

## Commands

### PHP

```bash
composer phpcs          # Lint PHP (WordPress coding standards)
composer phpcbf         # Auto-fix PHP code style
composer phpstan        # Static analysis (level 5)
composer phpcompat      # Check PHP 7.4–8.6 compatibility
composer test           # Run all checks (phpcs + phpcompat + phpstan)
composer build:vendor   # Build production vendor autoloader (no-dev, optimize)
composer zip            # Create distribution zip
```

### JavaScript/CSS Blocks

```bash
pnpm run build           # Build free blocks
pnpm run build:pro       # Build pro blocks (query, featured-image, related-posts-pro)
pnpm run build:all       # Build all blocks
pnpm start               # Watch free blocks
pnpm run start:pro       # Watch pro blocks
pnpm run build:assets    # Minify CSS/JS, generate RTL CSS (node build-assets.js)
pnpm run lint:js         # ESLint
pnpm run lint:css        # Stylelint
pnpm run zip             # Create distribution zip
ncu -u && pnpm install   # Update dependencies to latest and reinstall
```

## Architecture

### Entry Point & Bootstrap

`contextual-related-posts.php` defines constants (`WZ_CRP_VERSION`, `WZ_CRP_PLUGIN_FILE`, `WZ_CRP_PLUGIN_DIR`, etc.), registers the custom PSR-4 autoloader, loads Freemius, and calls `\WebberZone\Contextual_Related_Posts\load()`.

**Autoloader convention:** Namespace segments become path segments; underscores → hyphens, lowercase, last segment prefixed with `class-`. e.g. `WebberZone\Contextual_Related_Posts\Admin\Settings` → `includes/admin/class-settings.php`. Traits use `trait-` instead.

### Core Components

- **`includes/class-main.php`** — Singleton. Instantiates all subsystems on `plugins_loaded`.
- **`includes/class-hook-loader.php`** — Centralizes WordPress hook registration (content filters, query hooks, init hooks).
- **`includes/util/class-hook-registry.php`** — Static registry tracking all registered actions/filters; prevents duplicates.

### Query Engine

- **`CRP_Query`** extends `WP_Query`; uses FULLTEXT search on the posts table.
- **`CRP_Core_Query`** (`includes/class-crp-core-query.php`, ~47 KB) — Core algorithm: builds SQL, joins, ordering. The most complex file in the codebase.

### Frontend

- **`Display`** (`includes/frontend/class-display.php`, ~32 KB) — Renders related posts HTML.
- **`Media_Handler`** (`includes/frontend/class-media-handler.php`) — Resolves thumbnails via a priority strategy chain: custom meta → ACF field → FIFU plugin → featured image → content scan → first child attachment → video meta → configured default → site icon. Designed for multi-plugin reuse: subclasses override `get_option()` for their own options function; never call `crp_get_option()` directly in the class.
- **`Shortcodes`** — `[crp]` shortcode.
- **`Blocks`** — Free block at `includes/frontend/blocks/src/related-posts/`.
- **`REST_API`** — REST endpoints for block editor.
- **`Styles_Handler`** / **`Language_Handler`** — Enqueue plugin CSS and handle i18n for JS, respectively.

### Admin

- **`Settings`** (`includes/admin/class-settings.php`, ~90 KB) — Settings page with tabs for General, Performance, List tuning, Output, Thumbnail, Styles, Feed (plus WooCommerce when active).
- Settings stored as a single `crp_settings` array in `wp_options`. Access via `crp_get_option($key)` / `crp_get_settings()`.

### Pro Features (`includes/pro/`)

- **`Query_Modifier`** — Advanced filtering/sorting.
- **`Bot_Protection_Module`** — Excludes bot traffic.
- **`Lazy_Load_Module`** — Lazy-loads related posts output.
- **`Custom_Tables`** — Optimized database tables for large sites.
- **`WooCommerce\WooCommerce_Module`** — WooCommerce product relations.
- **`CLI\CLI_Manager`** — WP-CLI commands: `crp cache`, `crp related`, `crp db`, `crp db indexes`, `crp tables`, `crp tables indexes`, `crp settings`, `crp status`.
- **Pro Blocks:** `query/`, `featured-image/`, `related-posts-pro/` at `includes/pro/blocks/src/`.

Pro features are gated by `crp_freemius()->is__premium_only()` or `crp_freemius()->can_use_premium_code__premium_only()`.

### Utilities (`includes/util/`)

- **`Cache`** — Caches query output per post (configurable TTL, default 1 week).
- **`Helpers`** — Shared helper functions.
- **`Migration_Service`** — Database migration utilities.

## Key Patterns

- **Settings access:** Use `crp_get_option($key, $default)`, not `$crp_settings` directly.
- **Hook registration:** Add hooks via `Hook_Registry::add_action()` / `add_filter()` (not WP functions directly) for tracking and dedup.
- **Pro gating:** Wrap pro-only code with `if ( crp_freemius()->is__premium_only() )` checks.
- **Block builds:** Free blocks build with `wp-scripts`; pro blocks use a separate webpack entry — run the matching build command after editing block source.

## Shared framework files: `@since` convention

The Settings API (`includes/admin/settings/*.php`) and Admin Banner (`includes/admin/class-admin-banner.php`) are copy-pasted shared framework files, canonical source the `Settings_API` repo. Special `@since` rules keep tags meaningful across syncs:

- Each file carries **exactly one** `@since` tag, on its **class docblock**, set to the version that class was **first introduced into this plugin** (per-file — wizard, metabox and banner classes were generally added later than core Settings API classes).
- **Do not** add `@since` to methods, functions or properties in these files.
- When syncing from another plugin or the canonical `Settings_API` repo, **do not overwrite the class-level `@since`** — it is plugin-specific; re-apply the values below after any sync.

| File | `@since` |
|---|---|
| `includes/admin/settings/class-settings-api.php` | 3.5.0 |
| `includes/admin/settings/class-settings-form.php` | 3.5.0 |
| `includes/admin/settings/class-settings-sanitize.php` | 3.5.0 |
| `includes/admin/settings/class-settings-wizard-api.php` | 4.1.0 |
| `includes/admin/settings/class-metabox-api.php` | 3.5.0 |
| `includes/admin/class-admin-banner.php` | 4.2.0 |
