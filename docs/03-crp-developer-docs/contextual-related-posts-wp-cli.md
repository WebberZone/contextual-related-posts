---
slug: contextual-related-posts-wp-cli
title: "Contextual Related Posts CLI Overview"
products: [contextual-related-posts]
sections: ["03-crp-developer-docs"]
tags: [contextual-related-posts, developer, pro, wp-cli]
status: publish
toc: true
featured_image: "https://webberzone.com/wp-content/uploads/2025/12/Contextual-Related-Posts-CLI.webp"
---

[toc]

Contextual Related Posts CLI (CRP-CLI) offers an efficient way to manage Contextual Related Posts via the command line. This tool is part of [Contextual Related Posts Pro](https://webberzone.com/plugins/contextual-related-posts/pro/) starting from version 4.2.0.

![](https://webberzone.com/wp-content/uploads/2025/12/Contextual-Related-Posts-CLI-1024x683.webp)

## About WP-CLI

WP-CLI is a set of command-line tools for managing WordPress installations. You can update plugins, configure multisite installations, and much more, all without using a web browser. For more information, visit the [official WP-CLI website](http://wp-cli.org/).

## Getting Started with CRP-CLI

To begin using CRP-CLI, ensure that WP-CLI is installed and that you are running CRP Pro 4.2.0 or later. The CLI commands are accessed through the `wp crp` command. For a complete list of available commands, type `wp crp` in your command-line interface.

## CRP-CLI Command Tree

```bash
wp crp
├── cache
│   ├── clear     # Clear all cached related posts data
│   ├── enable    # Enable the CRP cache system
│   ├── disable   # Disable the CRP cache system
│   ├── warm      # Warm up cache by pre-generating related posts
│   ├── keys      # Show cache keys for posts
│   └── cleanup   # Remove expired cache entries
├── db
│   ├── status           # Show database migration and index status
│   ├── migrate-meta     # Migrate from crp_post_meta to _crp_* keys
│   ├── rollback-meta    # Rollback migration - will be deprecated in the next version
│   └── indexes
│       ├── create    # Create FULLTEXT indexes on core WordPress tables
│       ├── delete    # Delete FULLTEXT indexes from core WordPress tables
│       ├── recreate  # Recreate FULLTEXT indexes on core WordPress tables
│       └── status    # Check status of FULLTEXT indexes on core WordPress tables
├── tables
│   ├── create          # Create custom tables for CRP Pro
│   ├── drop            # Drop custom tables
│   ├── recreate        # Drop and recreate custom tables from scratch
│   ├── convert-innodb  # Convert the custom table engine to InnoDB
│   ├── status          # Show custom tables status
│   └── index           # Index posts into custom tables
├── tables indexes
│   ├── create    # Create FULLTEXT indexes for custom tables
│   ├── delete    # Delete FULLTEXT indexes from custom tables
│   ├── recreate  # Recreate FULLTEXT indexes for custom tables
│   └── status    # Check status of FULLTEXT indexes
├── related      # Show related posts for a specific post (requires post ID)
├── settings
│   ├── export    # Export plugin settings to file
│   ├── import    # Import plugin settings from file
│   ├── get       # Get a specific setting value
│   └── set       # Set a specific setting value
└── status       # Show comprehensive status of all CRP components
```

## Available Commands

All commands are prefixed with `wp crp`. For example: `wp crp cache clear`

### Cache Commands (wp crp cache)

Manage the plugin’s cache system.

#### wp crp cache clear

Clear all cached related posts data.

**Options:**

- `--network` – Clear cache for all sites in a multisite network
- `--url=<url>` – Clear cache for specific site URL (multisite only)
- `--blog-id=<id>` – Clear cache for specific blog ID (multisite only)
- `--verbose` – Show detailed output

**Examples:**

```bash
wp crp cache clear
wp crp cache clear --network
wp crp cache clear --verbose
```

#### wp crp cache enable

Enable the CRP cache system.

**Options:**

- `--type=<type>` – Cache type to enable (posts, html, all) (default: all)
- `--url=<url>` – Specific site URL to enable cache for (multisite only)
- `--network` – Enable cache for all sites in the network
- `--blog-id=<id>` – Specific blog ID to enable cache for (multisite only)

**Examples:**

```bash
wp crp cache enable
wp crp cache enable --type=html
wp crp cache enable --network
```

#### wp crp cache disable

Disable the CRP cache system.

**Options:**

- `--type=<type>` – Cache type to disable (posts, html, all) (default: all)
- `--url=<url>` – Specific site URL to disable cache for (multisite only)
- `--network` – Disable cache for all sites in the network
- `--blog-id=<id>` – Specific blog ID to disable cache for (multisite only)

**Examples:**

```bash
wp crp cache disable
wp crp cache disable --type=html
wp crp cache disable --network
```

#### wp crp cache warm

Warm up the cache by pre-generating related posts data.

**Options:**

- `--post-ids=<ids>` – Comma-separated list of post IDs to warm cache for
- `--post-type=<type>` – Specific post type to process (default: post)
- `--limit=<number>` – Number of posts to process (default: 100)
- `--recent=<count>` – Warm cache for the most recent N posts
- `--all` – Warm cache for all posts
- `--popular` – Warm cache for popular posts (by comment count)
- `--html` – Warm HTML cache instead of posts cache
- `--batch-size=<size>` – Batch size for processing (default: 50)
- `--network` – Warm cache for all sites in the network
- `--blog-id=<id>` – Specific blog ID to warm cache for (multisite only)
- `--force` – Force override existing lock file
- `--dry-run` – Show what would be processed without making changes
- `--verbose` – Show detailed output

**Examples:**

```bash
wp crp cache warm --all
wp crp cache warm --post-ids=1,2,3
wp crp cache warm --post-type=page --limit=50
wp crp cache warm --recent=10
wp crp cache warm --network
```

#### wp crp cache keys

Show cache keys for posts.

**Options:**

- `--post-id=<ids>` – Comma-separated list of post IDs to show keys for
- `--url=<url>` – Specific site URL (multisite only)
- `--blog-id=<id>` – Specific blog ID (multisite only)
- `--verbose` – Show detailed output including cache values (posts only)

**Examples:**

```bash
wp crp cache keys
wp crp cache keys --post-id=123
wp crp cache keys --post-id=1,2,3
wp crp cache keys --verbose
```

#### wp crp cache cleanup

Clean up expired cache entries.

**Options:**

- `--network` – Clean up for all sites in the network
- `--blog-id=<id>` – Specific blog ID to clean up for (multisite only)
- `--dry-run` – Show what would be cleaned without making changes
- `--verbose` – Show detailed output

**Examples:**

```bash
wp crp cache cleanup
wp crp cache cleanup --dry-run
wp crp cache cleanup --network
```

### Database Commands (wp crp db)

Manage database operations and migrations.

#### wp crp db migrate-meta

Migrate meta data from `crp_post_meta` array to individual `_crp_*` keys.

**Options:**

- `--dry-run` – Show what would be migrated without making changes
- `--verbose` – Show detailed error messages
- `--batch-size=<number>` – Number of posts to process in each batch (default: 100)
- `--network` – Migrate meta on all sites in the network
- `--blog-id=<id>` – Specific blog ID to migrate meta for (multisite only)

**Examples:**

```bash
wp crp db migrate-meta
wp crp db migrate-meta --dry-run
wp crp db migrate-meta --batch-size=50
wp crp db migrate-meta --network
```

#### wp crp db rollback-meta

Rollback meta migration from individual keys back to array format.

This command will be removed in version v4.3.0 and is not recommended for production sites.

**Options:**

- `--dry-run` – Show what would be rolled back without making changes
- `--verbose` – Show detailed error messages
- `--batch-size=<number>` – Number of posts to process in each batch (default: 100)
- `--network` – Rollback meta on all sites in the network
- `--blog-id=<id>` – Specific blog ID to rollback meta for (multisite only)
- `--force` – Force rollback without confirmation

**Examples:**

```bash
wp crp db rollback-meta --dry-run
wp crp db rollback-meta --batch-size=50
wp crp db rollback-meta --force
```

#### wp crp db status

Show database status and migration information.

**Options:**

- `--network` – Show status for all sites in the network
- `--blog-id=<id>` – Specific blog ID to show status for (multisite only)
- `--format=<format>` – Output format (table, json, csv, yaml) (default: table)

**Examples:**

```bash
wp crp db status
wp crp db status --network
wp crp db status --format=json
```

### Database Index Commands (wp crp db indexes)

Manage FULLTEXT indexes on core WordPress tables.

#### wp crp db indexes create

Create FULLTEXT indexes for related posts functionality.

**Options:**

- `--dry-run` – Show what would be created without making changes
- `--verbose` – Show detailed output

**Examples:**

```bash
wp crp db indexes create
wp crp db indexes create --dry-run
```

#### wp crp db indexes delete

Delete FULLTEXT indexes.

**Options:**

- `--dry-run` – Show what would be deleted without making changes
- `--force` – Force deletion without confirmation

**Examples:**

```bash
wp crp db indexes delete --force
wp crp db indexes delete --dry-run
```

#### wp crp db indexes recreate

Recreate FULLTEXT indexes (delete then create).

**Options:**

- `--dry-run` – Show what would be recreated without making changes
- `--verbose` – Show detailed output

**Examples:**

```bash
wp crp db indexes recreate
wp crp db indexes recreate --dry-run
```

#### wp crp db indexes status

Show FULLTEXT index status.

**Examples:**

```bash
wp crp db indexes status
wp crp db indexes status --format=json
```

### Tables Commands (wp crp tables)

Manage custom tables for efficient content storage.

#### wp crp tables create

Create custom tables for CRP Pro.

**Options:**

- `--dry-run` – Show what would be created without making changes
- `--verbose` – Show detailed output

**Examples:**

```bash
wp crp tables create
wp crp tables create --dry-run
```

#### wp crp tables drop

Drop custom tables.

**Options:**

- `--force` – Force table deletion without confirmation
- `--dry-run` – Show what would be dropped without making changes

**Examples:**

```bash
wp crp tables drop --force
wp crp tables drop --dry-run
```

#### wp crp tables status

Show the custom tables’ status.

**Options:**

- `--network` – Show status for all sites in the network
- `--blog-id=<id>` – Specific blog ID to show status for (multisite only)
- `--format=<format>` – Output format (table, json, csv, yaml) (default: table)

**Examples:**

```bash
wp crp tables status
wp crp tables status --network
wp crp tables status --format=json
```

#### wp crp tables recreate

Drop and recreate custom tables from scratch. All indexed data will be permanently lost. Run `wp crp tables index` afterwards to repopulate the tables.

**Options:**

- `--force` – Skip confirmation prompt
- `--dry-run` – Show what would be done without making changes

**Examples:**

```bash
wp crp tables recreate --force
```

#### wp crp tables convert-innodb

Convert the custom table storage engine to InnoDB. FULLTEXT indexes are automatically regenerated after conversion. This operation may take some time on large tables.

**Options:**

- `--force` – Skip confirmation prompt
- `--dry-run` – Show what would be done without making changes

**Examples:**

```bash
wp crp tables convert-innodb
wp crp tables convert-innodb --force
```

#### wp crp tables index

Index posts into custom tables.

**Options:**

- `--batch-size=<size>` – Batch size for processing (default: 50)
- `--network` – Index posts for all sites in the network
- `--blog-id=<id>` – Specific blog ID to index posts for (multisite only)
- `--force` – Force indexing even if a lock file exists
- `--dry-run` – Show what would be indexed without making changes
- `--verbose` – Show detailed output

**Examples:**

```bash
wp crp tables index
wp crp tables index --batch-size=100
wp crp tables index --network
```

### Tables Index Commands (wp crp tables indexes)

Manage FULLTEXT indexes on custom tables.

#### wp crp tables indexes create

Create FULLTEXT indexes for custom tables.

**Options:**

- `--network` – Create indexes for all sites in a multisite network
- `--dry-run` – Show what would be created without making changes
- `--verbose` – Show detailed output

**Examples:**

```bash
wp crp tables indexes create
wp crp tables indexes create --network
wp crp tables indexes create --dry-run
```

#### wp crp tables indexes delete

Delete FULLTEXT indexes from custom tables.

**Options:**

- `--network` – Delete indexes for all sites in a multisite network
- `--dry-run` – Show what would be deleted without making changes
- `--force` – Force deletion without confirmation
- `--verbose` – Show detailed output

**Examples:**

```bash
wp crp tables indexes delete
wp crp tables indexes delete --network
wp crp tables indexes delete --force
```

#### wp crp tables indexes recreate

Recreate FULLTEXT indexes for custom tables.

**Options:**

- `--network` – Recreate indexes for all sites in a multisite network
- `--dry-run` – Show what would be recreated without making changes
- `--verbose` – Show detailed output

**Examples:**

```bash
wp crp tables indexes recreate
wp crp tables indexes recreate --network
wp crp tables indexes recreate --dry-run
```

#### wp crp tables indexes status

Show FULLTEXT index status for custom tables.

**Options:**

- `--network` – Show status for all sites in the network
- `--blog-id=<id>` – Specific blog ID to show status for (multisite only)
- `--format=<format>` – Output format (table, json, csv, yaml) (default: table)

**Examples:**

```bash
wp crp tables indexes status
wp crp tables indexes status --network
wp crp tables indexes status --format=json
```

### Related Commands (wp crp related)

Show related posts for a specific post.

**Arguments:**

- `<post-id>` – Post ID to show related posts for
