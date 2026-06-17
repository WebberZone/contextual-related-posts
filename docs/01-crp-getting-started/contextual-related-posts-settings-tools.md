---
slug: contextual-related-posts-settings-tools
title: "Contextual Related Posts Settings – Tools"
products: [contextual-related-posts]
sections: ["01-crp-getting-started"]
tags: [contextual-related-posts, tools-page]
status: publish
order: 0
---

The Tools page in [Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) provides a set of utilities to help you manage, troubleshoot, and optimize the plugin’s performance and data. You can access this via **Tools > Related Posts Tools** in your WordPress admin area.

Below is a summary of each tool available, including Pro features.

## Clear cache

Use this tool to clear the Contextual Related Posts cache. This is helpful if you want to refresh the related posts cache across your site immediately.

> [!WARNING]
> ⚠️ Clearing the cache might take a while if you have a large number of posts.

## Recreate FULLTEXT index

Recreate the FULLTEXT index that Contextual Related Posts uses to find relevant related posts. This is useful if you suspect the index is corrupted or not performing optimally, or after a significant content update.

- Click the **Recreate Index** button to start the process.
- If the button fails, you can manually run the provided SQL queries in phpMyAdmin or Adminer.
- This process might take a long time on large sites.

> [!CAUTION]
> ⚠️ Always back up your database before running manual queries.

## Export/Import settings

Allows you to export the plugin settings as a `.json` file for backup or migration purposes, and import settings from another site.

- **Export:** Download your current settings for safekeeping or to migrate to another site.
- **Import:** Upload a previously exported settings file to restore or migrate your configuration.

## Migration Status

[Contextual Related Posts v4.2](https://webberzone.com/announcements/contextual-related-posts-v4-2-0/) changed how metadata was stored. Per‑post settings are no longer stored in a single `crp_post_meta` array. Each setting now lives in its own `_crp_*` meta key. This button allows you to migrate meta keys to the new format.

## Custom Tables *(Pro only)*

Use this tool to reindex all posts in the custom tables. This is especially useful if you have just enabled the custom tables feature or suspect the tables are out of sync.

- On multisite installations, you can select specific sites to reindex.
- The tool displays the current status, including the number of entries in the content table, the number of published posts, and the index percentage.
- Option to **Force complete reindex** (deletes all existing data and reindexes all posts).
- Progress is shown with a status bar and percentage.

### Clean Up Orphaned Records *(Pro only)*

Removes orphaned records from the custom tables. Orphaned records are entries that no longer correspond to existing posts, which can occur after bulk deletions or migrations.

- Click the **Clean Up Orphaned Records** button to run the cleanup.
- On multisite, you can target specific sites.

## Recreate Custom Table FULLTEXT Indexes *(Pro only)*

Manually drop and recreate the FULLTEXT indexes on the custom tables. This is useful if you suspect the indexes are corrupted or not performing optimally.

- Click **Recreate FULLTEXT Indexes** to start the process.
- If the button fails, manual SQL queries are provided for use in phpMyAdmin or Adminer.

> [!CAUTION]
> ⚠️ Always back up your database before running manual queries.

## Recreate Custom Tables *(Pro only)*

Drops and recreates the custom table structure from scratch. All indexed data is permanently lost; run the reindex tool afterwards to repopulate the table.

- Click **Recreate Custom Tables** and confirm the prompt to proceed.
- If the button fails, manual SQL queries are provided for use in phpMyAdmin or Adminer.
- You can also run this via WP-CLI: `wp crp tables recreate --force`

> [!CAUTION]
> ⚠️ Always back up your database before proceeding.

## Convert Custom Table to InnoDB *(Pro only)*

Converts the custom table storage engine to InnoDB and automatically regenerates the FULLTEXT indexes after conversion. This is recommended if the table is using MyISAM, which offers no crash recovery or row-level locking.

- The tool displays the current storage engine before you act.
- Click **Convert to InnoDB & Regenerate Indexes** and confirm the prompt to proceed.
- This operation may take some time on large tables.
