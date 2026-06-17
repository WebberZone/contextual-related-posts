---
slug: efficient-content-storage-and-indexing
title: "Efficient Content Storage and Indexing (ECSI) in Contextual Related Posts Pro and Better Search Pro"
products: [better-search,contextual-related-posts]
sections: [02-bs-advanced,02-crp-advanced]
tags: [contextual-related-posts,pro,related-posts,settings]
status: publish
order: 0
---

[kbtoc]

**Efficient Content Storage & Indexing (ECSI)** is an advanced data storage system for [Contextual Related Posts Pro](https://webberzone.com/plugins/contextual-related-posts/pro/) and [Better Search Pro](https://webberzone.com/plugins/better-search/pro/), significantly improving the quality of related posts and search results with enhanced performance. The post content is stored in custom database tables optimized for search and retrieval. This feature was first introduced in [Contextual Related Posts Pro v4](https://webberzone.com/announcements/contextual-related-posts-v4-0-0/) and Better Search Pro v4.2.

By creating custom database tables with intelligent indexing, ECSI delivers better performance without requiring technical expertise. WordPress multisite users will particularly benefit as queries are simpler and faster.

## Overview

ECSI moves processed post content into dedicated, optimized tables, rather than using WordPress’s native post tables. This approach:

- Post content is pre-processed for shortcodes and blocks, eliminating the superfluous markup created by WordPress when saving post content.
- Custom FULLTEXT indices to improve contextual matching.
- Provides better support for multisite WordPress installations.
- Advanced related posts storage and tracking (coming soon in a future version of CRP Pro)

> [!NOTE]
> ⓘ Efficient Content Storage and Indexing is only available in Contextual Related Posts Pro.

## Benefits of ECSI

1. **Faster Queries**
    - Pre-processed content eliminates runtime processing
    - Optimized table structure for related posts/search queries
    - Efficient FULLTEXT indices for content searching
2. **Better Resource Usage**
    - Reduced database load
    - Optimized storage format
    - Efficient taxonomy relationship handling
3. **Scalability**
    - Handles large post volumes efficiently
    - Optimized for multisite installations
    - Batch processing for large indexing operations

## How ECSI works behind the scenes

When you enable ECSI, the plugin creates a custom table `wp_wz_posts` to store the pre-processed content, which includes:

- **Multisite Support:** Optimized for sites with multiple networks.
- **FULLTEXT Indices:** Speed up title, content, and excerpt searches.
- **JSON Columns:** Store taxonomy terms and relationships in a flexible format.
- **Generated Columns:** Pre-compute values to boost query performance.

Contextual Related Posts Pro and Better Search Pro use the same tables, eliminating any duplication if you’re running both plugins.

When you save a post, ECSI processes it in several steps:

- Renders blocks and shortcodes
- Strips HTML tags and Gutenberg comments
- Optimizes content length (max 50,000 characters)
- Extracts and stores taxonomy relationships
- Maintains primary term information
- Stores processed data in the custom table

This approach eliminates processing time during content retrieval, resulting in faster queries and reduced server load. ECSI queries fully support caching; just remember to enable caching on the settings page.

| Feature | Free Version | Pro Version (with ECSI) |
| ---- | ---- | ---- |
| Storage | Uses WordPress's native `wp_posts` table | Uses optimized custom tables |
| Indexing | MySQL FULLTEXT indices on raw post data | MySQL FULLTEXT indices on pre-processed and indexed content |
| Query Performance | Processes content during each query | Faster processing from pre-stored content |
| Taxonomy Integration | Fetched and processed on each load | Efficient taxonomy integration |
| Performance | Basic | Better performance on large sites |
| Multisite Support | Not optimized for multisite | Multisite-aware optimization |
| Content Sync | None | Automatic content synchronization |

## Enabling ECSI

### Prerequisites

- Contextual Related Posts Pro and/or Better Search Pro activated
- Super Admin for Multisite or Administrator access to your WordPress site

### Enable for a single site

1. Go to **Settings → Related Posts** or **Better Search → Settings**
2. Click the **Performance** tab
3. Toggle **Use Custom Tables** to ON
4. Save changes
5. Go to **Tools → Related Posts Tools** or **Better Search → Tools**
6. Click **Reindex Custom Tables**
7. Optionally, check **Force reindex** to delete existing posts and resync.
8. Wait for the process to complete

### Enable on a multisite network

1. In Network Admin, go to **Contextual Related Posts → Settings** or **Better Search → Settings**
2. Under the Performance settings section, select **Enable Enhanced Content Search Index on all sites**.
3. Save settings
4. Go to **Network Admin → Contextual Related Posts **→** Tools** or **Network Admin → Better Search **→** Tools**
5. Select specific sites or leave all unchecked to process the entire network
6. Click **Reindex Custom Tables**

> [!NOTE]
> ⓘ The reindexing process runs in batches of 25 posts. For large sites, this may take several minutes.

## Content Synchronization

### Automatic Updates

ECSI maintains data consistency through:

1. Real-time synchronization on post updates
2. Automatic removal of deleted posts
3. Handling of post status changes
4. Ignores revisions and autosaves
5. Multisite synchronization

### Batch Processing

For bulk operations:

- Processes posts in batches of 25
- Shows progress indicators
- Allows stopping and resuming
- Marks tables as ready at 80% completion
- Handles network-wide operations

## Compatibility

ECSI is compatible with:

- WordPress multisite networks
- Custom post types (if enabled in Contextual Related Posts and/or Better Search settings)
- Most caching plugins
- Popular WordPress themes and plugins

## Frequently Asked Questions

### How long does reindexing take?

Reindexing processes posts in batches of 25. A site with:

- 500 posts: about 2-3 minutes
- 5,000 posts: about 15-20 minutes
- 50,000+ posts: may take several hours

This will depend on the server resources on which your site is hosted. The process can run in the background using WordPress’ cron, which is particularly useful if you have a large number of posts.

### Will enabling ECSI affect my existing related posts?

Existing related posts will be updated once the cache is invalidated and served from the custom table. You can manually clear the cache to speed up this process.

### What happens if I disable ECSI later?

The plugin will revert to using standard WordPress tables. Custom tables remain in your database until manually removed.

### Do I need to reindex after adding new content?

No. New and updated content is automatically processed and added to the custom tables.

## Troubleshooting

| Issue | Possible Causes | Solutions |
| ---- | ---- | ---- |
| Empty or missing related posts | Reindexing not completed; Custom tables are not enabled properly | Check if custom tables are enabled; Run a force reindex; Verify that the `wp_wz_posts` table exists and contains data |
| Synchronization issues | WordPress cron is not running; Post hooks are not firing correctly | Check WordPress cron status; Review error logs; Try a force reindex |
| Performance issues | Database server load; Large tables; Insufficient server resources | Monitor database server load; Optimize the database if performance drops; Consider database server upgrades if necessary |
| Admin notice: "Some FULLTEXT indexes are missing" | Custom tables are enabled but one or more FULLTEXT indexes were not created or were dropped | Go to **Tools → Related Posts Tools** and click **Recreate FULLTEXT Indexes**; alternatively run `wp crp tables indexes recreate` |
| Admin notice: "Custom tables require MySQL 5.7.8+ or MariaDB 10.2.7+" | Database server version is too old to support the JSON columns ECSI requires | Upgrade your database server, or disable **Use Custom Tables** in the Performance settings and use standard FULLTEXT search instead |

## Best practices

### For optimal performance

- Enable ECSI before adding large amounts of content
- Reindex during off-peak hours
- Regularly monitor database performance
- Use a good-quality hosting provider

### For multisite networks

- Consider storage requirements before enabling network-wide
- Monitor performance on individual sites
- Use network-wide reindexing for consistency
