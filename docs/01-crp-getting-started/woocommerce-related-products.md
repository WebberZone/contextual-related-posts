---
slug: woocommerce-related-products
title: "WooCommerce Related Products with Contextual Related Posts Pro"
products: [contextual-related-posts]
sections: ["01-crp-getting-started"]
tags: [contextual-related-posts, settings, woocommerce]
status: publish
order: 0
---

[kbtoc]

This guide explains how the WooCommerce integration in [Contextual Related Posts Pro](https://webberzone.com/plugins/contextual-related-posts/pro/) works and how to configure it effectively for product recommendations.

The integration allows CRP to generate relevant related products using WooCommerce-aware logic, while keeping performance predictable on busy sites.

## Quick checklist

Before enabling the WooCommerce integration, make sure that:

- Contextual Related Posts Pro is updated to version 4.3.0 or higher
- WooCommerce is installed and active
- [Efficient Content Storage and Indexing (ECSI)](https://webberzone.com/support/knowledgebase/efficient-content-storage-and-indexing/) is enabled
- Posts and Products have been indexed after enabling custom tables
- At least one product meets the stock and visibility criteria

This guide assumes familiarity with basic WooCommerce and CRP Pro concepts, as well as administrator access to configure settings.

## Overview of the integration

The WooCommerce integration does more than display products as related items. It adapts CRP’s matching logic to work with WooCommerce product data and catalog rules, creating a product recommendation system that behaves like native WooCommerce functionality.

Specifically, the integration:

- Indexes product-specific data such as SKUs, attributes, and descriptions
- Respects WooCommerce catalog logic, including stock status and visibility
- Renders output using native WooCommerce templates
- Provides granular control over which product elements are displayed

## Enabling the integration

To activate the WooCommerce integration:

1. Navigate to **Related Posts → Settings** in your WordPress admin
2. Open the **WooCommerce** tab
3. Enable **WooCommerce Integration**
4. Click **Save Changes**

The integration requires ECSI (custom tables) to be enabled.

## Core settings explained

All settings ship with sensible defaults. You can enable the integration and leave the remaining options unchanged to get immediate, production-safe results.

### Product indexing options

These settings control which product data is indexed for matching:

- **Index SKU**: Includes product SKUs in the index (enabled by default)
- **Index product attributes**: Indexes custom attributes such as color, size, or material (enabled by default)
- **Index purchase note**: Includes purchase notes in the index (disabled by default)

### Display mode

The **Display Mode** setting controls how CRP interacts with WooCommerce’s native related products:

- **Replace**: Removes WooCommerce’s native related products and displays only CRP’s recommendations (enabled by default)
- **Coexist**: Displays both WooCommerce’s native related products and CRP’s recommendations

Replace mode is useful when you want CRP to handle all related product logic while maintaining a single related products section.

Other display settings include:

- **Number of related products to display**: This will be the *maximum* number of related products that will be displayed.
- **Related products heading**: Heading text displayed above the related products list. Default: `Related products`. Leave empty to hide the heading.

### Output Customization

Control which elements appear in each related product:

- Product thumbnail
- Sale badge
- Price
- Product Rating
- Add to cart button

All display elements are enabled by default and can be disabled individually as needed.

### Product filtering

Fine-tune which products appear in recommendations:

- **Exclude hidden products**: Filters products excluded from catalog or search (enabled by default)
- **Exclude out-of-stock products**: Disabled by default
- **Same product category only**: Limits results to matching categories (enabled by default)
- **Same product tag only**: Limits results to matching tags (disabled by default)

The above settings will reduce the number of related products that the plugin can find. The Out-of-stock flag is potentially more aggressive if your store has several products that are unavailable to purchase.

## How product matching works

The integration uses CRP’s matching engine, adapted for WooCommerce product data. The plugin will use the [List Tuning settings](https://webberzone.com/support/knowledgebase/contextual-related-posts-list-tuning-settings/), along with the indexed data in the custom table, to find related products.

Advanced users can filter `crp_wc_related_products_query_args` to filter the array of arguments that are passed to [CRP_Query](https://webberzone.com/support/knowledgebase/crp-query/).

```php
/**
 * Filter the query arguments for related products.
 *
 * @param array $query_args        Query arguments.
 * @param int   $source_product_id Source product ID.
 */
$query_args = apply_filters( 'crp_wc_related_products_query_args', $query_args, $source_product_id );
```

## Output and templates

Related products are rendered using native WooCommerce templates, ensuring visual consistency with your theme.

### Template structure

```html
<section class="related products crp-related-products">
    <h2>Related products</h2>
    <ul class="products">
        <li class="product">
            <!-- WooCommerce product template -->
        </li>
    </ul>
</section>
```

### Customization options

Output can be customized using:

- WooCommerce hooks and filters
- Custom CSS targeting `.crp-related-products`

You can also override the output using `crp_wc_related_products_html` to build your own output.

```php
/**
 * Filters the final related products HTML output.
 *
 * Note: This output is still passed through wp_kses_post() before echo.
 *
 * @param string  $output              HTML output.
 * @param int     $source_product_id   Source product ID.
 * @param int[]   $product_ids         Product IDs.
 */
$output = apply_filters( 'crp_wc_related_products_html', $output, $source_product_id, $product_ids );
```

## Advanced configuration

### Per-product customization

You can override global settings on individual products using the CRP metabox, including:

- Manually defined related products
- Excluding products from recommendations
- Custom keywords for improved matching

### Performance optimization

For large product catalogs:

- Review the cache configuration of CRP and/or other caching plugins. I highly recommend using an object cache like Redis
- Consider using the Server Load Threshold feature

### Integration with other CRP features

The WooCommerce integration works alongside:

- Bot Protection
- Cache management
- WP-CLI commands for bulk operations

## Cart Related Products *(Pro only)*

When enabled, CRP displays a section on the WooCommerce cart page showing products the customer could add to reach the free shipping threshold. The section only appears when the cart subtotal is below the free shipping minimum for the customer's shipping zone.

CRP uses the most expensive item in the cart as the relevance anchor and filters candidates to a configurable price band around the remaining gap — so the suggestions are both contextually related and priced to close the gap.

### Enabling cart related products

1. Navigate to **Related Posts → Settings** in your WordPress admin.
2. Open the **WooCommerce** tab.
3. Enable **Enable cart related products**.
4. Click **Save Changes**.

The base WooCommerce integration must also be enabled.

### Cart settings

**Enable cart related products** — Displays the cart section when the cart subtotal is below the free shipping minimum. Disabled by default.

**Number of cart related products** — Maximum number of products to show. Default: `4`.

**Price upper bound (%)** — Controls the width of each price band. CRP queries both a full-gap band and a half-gap band; this percentage widens both. Example: gap = $20, upper bound = 20% → full-gap band covers $20–$24, half-gap band covers $10–$12. Default: `20`. Range: 0–200.

**Cart section heading** — Heading text displayed above the cart section. Leave empty to use the automatic "Add $X more for free shipping" message, where $X is the remaining gap formatted in the store currency. Default: empty.

**Cart display position** — The WooCommerce action hook where the section is injected on the classic cart page. Options:

- **After cart table** (default) — `woocommerce_after_cart_table`
- **Before cart collaterals** — `woocommerce_before_cart_collaterals`
- **Cart collaterals (sidebar)** — `woocommerce_cart_collaterals`
- **After cart section** — `woocommerce_after_cart`

### How cart matching works

CRP resolves the free shipping threshold from the WooCommerce shipping zone matched to the customer's current package. For guests with no address on file, it falls back to the lowest `min_amount` across all zones so the nudge still appears on single-zone stores. If no free shipping method applies, the section is suppressed.

The remaining gap is calculated from the cart's displayed subtotal minus any applied coupon discount (and its tax portion, when prices are displayed including tax), mirroring how WooCommerce itself checks the free shipping threshold. This keeps the suggested gap accurate when a customer has a discount coupon applied.

The most expensive product in the cart is used as the relevance anchor for CRP's FULLTEXT ranking. Products already in the cart are excluded, and the existing **Exclude hidden products** and **Exclude out-of-stock products** settings apply.

CRP queries two price bands and mixes the results:

- **Full-gap band** — products priced between the remaining gap and `gap × (1 + upper bound %)`. One product from this band closes the gap alone.
- **Half-gap band** — products priced between half the gap and `(gap / 2) × (1 + upper bound %)`. Two products from this band together close the gap.

The majority of slots go to full-gap products (so that a single item always closes the gap when the limit is 1), with the remainder filled from the half-gap band. Any unfilled slots are topped up from whichever band has leftover candidates. The final list is shuffled before display so the order varies between page loads.

### Filters

**`crp_wc_free_shipping_threshold`** — Override the resolved threshold for stores with custom shipping logic.

```php
add_filter( 'crp_wc_free_shipping_threshold', function( float $threshold ): float {
    return 50.0; // Fixed threshold regardless of zone.
} );
```

**`crp_wc_cart_related_products_query_args`** — Modify the query arguments before the cart product query runs.

```php
/**
 * @param array $query_args Query arguments passed to CRP_Query or WP_Query.
 * @param float $min_price  Lower bound of the price band.
 * @param float $max_price  Upper bound of the price band.
 * @param float $gap        Remaining amount until free shipping.
 */
add_filter( 'crp_wc_cart_related_products_query_args', function( array $query_args, float $min_price, float $max_price, float $gap ): array {
    // Limit to a specific product category.
    $query_args['tax_query'][] = array(
        'taxonomy' => 'product_cat',
        'field'    => 'slug',
        'terms'    => array( 'accessories' ),
    );
    return $query_args;
}, 10, 4 );
```

**`crp_wc_cart_related_products_heading`** — Customize the heading text after it has been resolved.

```php
/**
 * @param string $heading     Heading HTML/text.
 * @param float  $gap         Remaining amount until free shipping.
 * @param int[]  $product_ids Matched product IDs.
 */
add_filter( 'crp_wc_cart_related_products_heading', function( string $heading, float $gap, array $product_ids ): string {
    return 'You might also need';
}, 10, 3 );
```

**`crp_wc_cart_related_products_html`** — Filter or replace the complete cart section HTML before it is output.

```php
/**
 * @param string $output      Full section HTML.
 * @param float  $gap         Remaining amount until free shipping.
 * @param int[]  $product_ids Matched product IDs.
 */
add_filter( 'crp_wc_cart_related_products_html', function( string $output, float $gap, array $product_ids ): string {
    return '<div class="my-cart-upsell">' . $output . '</div>';
}, 10, 3 );
```

### Styling

The cart section is wrapped in `<section class="crp-cart-related-products crp-cart-position-{position}">`, where `{position}` reflects the chosen cart display position — for example `crp-cart-position-after_cart_table` or `crp-cart-position-cart_collaterals`. CRP injects scoped inline styles on the cart page to normalize image sizes, apply responsive grid layouts per position, and ensure add-to-cart buttons remain visible.

Target `.crp-cart-related-products` in your theme CSS to override defaults, or use the position-specific class (`.crp-cart-position-after_cart_table`, `.crp-cart-position-cart_collaterals`, etc.) to apply overrides only for a particular cart hook.

## Troubleshooting

### Related Products from CRP are not showing

1. Confirm ECSI is enabled
2. Verify products have been indexed
3. Check stock status and visibility
4. Review query filtering settings

### Display issues

1. Check theme compatibility with WooCommerce templates
2. Review display element settings
3. Test with a default WooCommerce theme to isolate conflicts

### Performance concerns

1. Enable custom tables if not already active
2. Review cache settings
3. Consider enabling Server Load Threshold

## Code examples

The following examples are optional and intended for developers who need finer control over output or behavior.

### Customizing the heading based on Source Product ID

```php
/**
 * Change the related products heading based on product ID.
 *
 * @param string $heading         The heading text.
 * @param int    $source_product_id The ID of the current product.
 * @param array  $product_ids     Array of related product IDs.
 * @return string Modified heading.
 */
add_filter( 'crp_wc_related_products_heading', function( $heading, $source_product_id, $product_ids ) {
    // Example 1: Specific product ID.
    if ( 42 === $source_product_id ) {
        return 'Special Recommendations for You';
    }

    // Example 2: Product category check.
    if ( has_term( 'clothing', 'product_cat', $source_product_id ) ) {
        return 'Complete Your Outfit';
    }

    // Example 3: Product tag check.
    if ( has_term( 'sale', 'product_tag', $source_product_id ) ) {
        return 'More Great Deals';
    }

    // Example 4: Product type check.
    $product = wc_get_product( $source_product_id );
    if ( $product && $product->is_type( 'variable' ) ) {
        return 'More Color & Size Options';
    }

    // Default fallback.
    return 'You Might Also Like';
}, 10, 3 );
```

### Filtering related products

```php
add_filter( 'crp_wc_related_products_query_args', function( $query_args, $source_product_id ) {
    $query_args['post__not_in'] = array( 123, 456, 789 );
    return $query_args;
}, 10, 2 );
```

### Custom output HTML

```php
add_filter( 'crp_wc_related_products_html', function( $output, $product_ids, $source_product_id ) {
    return '<div class="custom-related-wrapper">' . $output . '</div>';
}, 10, 3 );
```

## Best practices

1. Start with the default settings before making changes
2. Test recommendations across different product types
3. Rebuild indexes after major product updates
4. Adjust settings based on customer behavior
5. Ensure that Caching is enabled on production sites
6. Monitor performance using Query Monitor or similar tools

## Frequently asked questions

**Why aren't cart related products showing?**
The section only appears when all of the following are true: the base WooCommerce integration is enabled, the cart feature is enabled, the customer is on the cart page, and the cart subtotal is below the free shipping threshold for their zone. If no free shipping method is configured in WooCommerce, or if the cart total already meets the threshold, the section is suppressed. CRP searches two price bands (full-gap and half-gap), so products from either band qualify. If **Price upper bound (%)** is set very low and your catalog has limited price points near the gap, neither band may return results — try raising the percentage.

**Does this work with product variations?**
Variations are indexed through their parent product, and recommendations link to the main product page. Individual product variations will not appear as related products.

**Can related products be shown on non-product pages?**
The integration is designed for single product pages, but CRP shortcodes and blocks can be used elsewhere.

**How does this affect page load times?**
With ECSI enabled, related product queries are typically fast. However, related queries tend to be heavy, so it is advisable to enable caching.

**Can the “Related products” heading be translated?**
CRP includes a wpml-config file, which supports WPML and Polylang. You can also use code to filter `crp_wc_related_products_heading` for incremental translation.

## Getting help

If you need assistance:

- Review the [Contextual Related Posts knowledge base](https://webberzone.com/support/product/contextual-related-posts/)
- Submit a support ticket
- Refer to the [CRP CLI documentation](https://webberzone.com/support/knowledgebase/contextual-related-posts-wp-cli/) for advanced management

The WooCommerce integration builds on CRP’s existing relevance engine while respecting WooCommerce’s catalog rules, allowing you to create product recommendations that remain fast, accurate, and predictable under load.
