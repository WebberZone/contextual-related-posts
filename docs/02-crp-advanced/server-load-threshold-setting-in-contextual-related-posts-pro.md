---
slug: server-load-threshold-setting-in-contextual-related-posts-pro
title: "Server Load Threshold Setting in Contextual Related Posts Pro"
products: [contextual-related-posts]
sections: ["02-crp-advanced"]
tags: [contextual-related-posts, pro, settings]
status: publish
order: 0
---

The **Server Load Threshold** setting was introduced in Contextual Related Posts Pro v4.2.0 and exists for one simple reason: your database should stay responsive, even when traffic spikes.

> [!NOTE]
> ⓘ Related posts are useful. They are not essential to serving a page. When the database is already under pressure, CRP Pro steps aside rather than make things worse.

This setting lets you define *how busy is too busy*. You can find this setting under the [Performance tab](https://webberzone.com/support/knowledgebase/contextual-related-posts-performance-settings/) in the Related Posts settings page.

## What this setting does

MySQL can only execute a limited number of queries at the same time. As concurrency increases, performance drops. Locks take longer. Queries wait longer. Everything feels slower.

The Server Load Threshold checks how many queries are actively executing on the database server when CRP wants to run.

If that number exceeds your configured limit, CRP Pro skips generating related posts for that request.

Nothing breaks. The page still loads. Visitors won’t see related posts for that view.

Think of it as good manners for your database.

## How MySQL load is measured

MySQL exposes a status value called `Threads_running`. This represents the number of threads that are *currently executing queries* across the entire server.

It is not a queue length. MySQL does not queue queries in the way people often imagine. A high `Threads_running` value means the server is busy doing real work.

CRP Pro checks this value before running its own queries. If the number is already too high, CRP backs off immediately.

No queries are started. No partial work is done.

## Configuring the Threshold

You can find this setting under:

**Settings → Related Posts → Performance**

## Choosing a Sensible Value

There is no perfect number. The right threshold depends on how much CPU your database actually has.

### Shared Hosting

Shared hosting is unpredictable and usually constrained.

Start with **10–15**. A lower value reduces the risk of slow pages or provider throttling.

### VPS Hosting

For VPS environments, CPU cores are a useful guide:

- **1–2 cores**: 8–12
- **2–4 cores**: 15–20
- **4–8 cores**: 20–30

A practical rule of thumb is roughly **twice your CPU core count**.

### Dedicated Servers

Dedicated servers with plenty of CPU headroom can safely use higher values, typically **30–50** or more, depending on workload.

If you’re unsure, leave the threshold at **15**. It works well for most WordPress sites and errs on the side of caution.

> [!NOTE]
> ⓘ Set it to **0** to bypass this feature. This is perfect if you're not worried about sever load. *It will help you save a query with the feature off.*

## When Should You Adjust This?

You may want to tweak the threshold if patterns emerge.

Increase it or set it to **0** if:

- Your database rarely shows signs of stress.
- Related posts often disappear during regular traffic.
- Your server has plenty of unused CPU capacity.

Decrease it if:

- Pages slow down during traffic spikes.
- Database queries time out under load.
- Your host warns about excessive database usage and particularly queries related to Contextual Related Posts.

Tools like [Query Monitor](https://webberzone.com/support/knowledgebase/debugging-with-query-monitor/) can help you observe database activity and guide fine-tuning.

## How this works with caching

Server Load Threshold works alongside CRP’s caching. When related posts are already cached, CRP can display them without running expensive queries. In those cases, the load check effectively becomes irrelevant.

The result is layered protection:

- First-time requests avoid adding load.
- Cached pages stay fast.
- During spikes, essential queries get priority.

## Why CRP Skips Instead of Waiting

When a database is already under load, waiting is rarely helpful.

MySQL does not maintain a neat queue of queries that are processed one by one. Queries either execute immediately, wait on locks, or compete for CPU time. Adding more work increases contention and makes everything slower.

CRP Pro deliberately avoids waiting for the database to “calm down”. Instead, it makes a quick decision:

- If the database is busy, skip related posts.
- If it is not, proceed normally.

This keeps page generation predictable. It also avoids tying up PHP workers while waiting on database resources that may not free up in time.

In short, skipping is safer than stalling.

## When Related Posts do not display

If related posts disappear occasionally, especially during traffic spikes, this setting is often the reason.

CRP Pro may be intentionally skipping related posts to protect your database. Once the load drops, related posts resume automatically.

For a broader checklist of causes and fixes, see [Related Posts do not display knowledge base article](https://webberzone.com/support/knowledgebase/related-posts-do-not-display/).

## Technical Notes

CRP Pro checks the server load using MySQL’s `SHOW GLOBAL STATUS` command and reads the `Threads_running` value.

This approach is intentionally boring:

- Lightweight
- Reliable
- Well understood

Most importantly, the check runs *before* any related-post queries execute. If the database is already busy, CRP does nothing and steps aside.
