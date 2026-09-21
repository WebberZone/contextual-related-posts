---
slug: contextual-related-posts-abilities-api
title: "Contextual Related Posts Abilities API"
products: [contextual-related-posts]
sections: ["03-crp-developer-docs"]
tags: [abilities-api, contextual-related-posts, developer]
status: publish
order: 0
toc: true
---

[toc]

[Contextual Related Posts](https://webberzone.com/plugins/contextual-related-posts/) 4.5.0 registers abilities with the WordPress Abilities API. The shared ability retrieves related posts; Contextual Related Posts Pro adds abilities to clear the cache and change a post's exclusion status.

## Requirements

- Contextual Related Posts 4.5.0 or later.
- WordPress 6.9 or later, which provides the Abilities API. On older versions the abilities are not registered and the rest of the plugin is unaffected.
- Contextual Related Posts Pro and the `manage_options` capability to run the Pro management abilities.

## Using an AI assistant

The Abilities API does not add a chat screen to WordPress. It makes plugin actions available to connected software. To use an AI chat assistant, connect your WordPress site to an AI client that supports MCP through an MCP server. One option is the separate MCP Adapter plugin for WordPress; Contextual Related Posts does not include the adapter. Installing the adapter alone does not connect an AI client. Follow the adapter and client's setup instructions to connect them.

Once connected, you can ask in plain English:

> Find posts related to WordPress post 123.

The assistant discovers `contextual-related-posts/get-related`, sends `{"post_id":123}` to the ability, and summarizes the returned posts. You do not need to enter JSON in the chat. The assistant uses the WordPress account configured for the MCP connection, so that account must be able to read the source post.

The ability requires a numeric post ID. It does not find a post from its title. If you want to ask by title, the connected AI client also needs a separate WordPress tool that searches posts and returns the ID.

## Related-post lookup

Both the free and Pro plugins register `contextual-related-posts/get-related`. It finds related posts for a source post and returns each readable result's ID, title, URL, and excerpt.

The input accepts these properties:

| Property | Type | Required | Description |
| --- | --- | --- | --- |
| `post_id` | Integer | Yes | ID of the source post. The current user must be allowed to read it. |
| `limit` | Integer | No | Maximum number of results. Defaults to `6`; accepted values are `1`–`100`. |
| `post_types` | String array | No | Post type names to include in the search. |
| `include_terms` | Integer array | No | Term taxonomy IDs that results must match. |
| `exclude_terms` | Integer array | No | Term taxonomy IDs to exclude from the results. |

For example, a client can pass an input object like this:

```json
{
  "post_id": 123,
  "limit": 6,
  "post_types": ["post", "page"],
  "include_terms": [12],
  "exclude_terms": [19]
}
```

The output is an array of objects with `id`, `title`, `url`, and `excerpt` properties. Results the current user cannot read are omitted.

The ability uses Contextual Related Posts' normal query and saved settings. It does not change the matching rules.

If the returned posts are empty or do not look relevant, compare them with the related posts shown on the site and check the [List Tuning settings](https://webberzone.com/support/knowledgebase/contextual-related-posts-list-tuning-settings/).

An empty array (`[]`) is a successful response that means no readable related posts matched the request. It is not an API error.

## Run the ability through the REST API

WordPress also exposes abilities through its REST API. The `get-related` ability only reads data, so run it with a `GET` request. Pass the ability input in the `input` query parameter:

```bash
curl --get \
  --data-urlencode 'input[post_id]=123' \
  'https://example.com/wp-json/wp-abilities/v1/abilities/contextual-related-posts/get-related/run'
```

Replace `example.com` and `123` with your site's URL and the source post ID. The request must be allowed to read the source post. The response is a JSON array:

```json
[
  {
    "id": 456,
    "title": "Example related post",
    "url": "https://example.com/example-related-post/",
    "excerpt": "An excerpt from the related post."
  }
]
```

If there are no matching posts, the response is `[]`.

## Pro management abilities

Contextual Related Posts Pro registers two abilities for site management. Both require the current user to have the `manage_options` capability.

### Clear the cache

`contextual-related-posts/clear-cache` clears the cache for one post or the entire site. Its input requires `confirm` to be `true`. Include `post_id` to clear that post's cache; omit it to clear the full Contextual Related Posts cache.

```json
{
  "post_id": 123,
  "confirm": true
}
```

The result contains a `cleared` boolean and a `count` integer.

### Set a post exclusion

`contextual-related-posts/exclude-post` sets whether a post is excluded from related-post results. Pass the post ID and the desired boolean state. Changing the exclusion clears the Contextual Related Posts cache.

```json
{
  "post_id": 123,
  "excluded": true
}
```

The result contains the post ID and its resulting exclusion state.

## Visibility and permissions

The plugin registers these abilities as public and makes them visible through the Abilities API. Public visibility lets compatible clients discover the abilities; it does not bypass their permission checks. The related-post lookup uses the plugin's REST API read-permission checks, while the Pro management abilities require `manage_options`.

## See also

- [WP REST API Integration](https://webberzone.com/support/knowledgebase/wp-rest-api-integration/)
