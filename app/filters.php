<?php

/**
 * Theme filters.
 */

namespace App;

/**
 * Add "… Continued" to the excerpt.
 *
 * @return string
 */
add_filter('excerpt_more', function () {
    return sprintf(' &hellip; <a href="%s">%s</a>', get_permalink(), __('Continued', 'sage'));
});

/**
 * Modifies the query for the store archive page.
 *
 * @param  \WP_Query $query
 * @return void
 */
add_filter('pre_get_posts', function ($query): void {
    if (! is_admin() && is_tax('product_category') && $query->is_main_query()) {
        $query->set('posts_per_page', 14);
    }
});

/**
 * Filters the query arguments for ACF relationship fields.
 *
 * This filter modifies the query arguments for ACF relationship fields when searching for 'pci_product' post types.
 * If a search term is provided, it performs a meta query to find posts with a 'pci_id' meta key that matches the search term.
 * If matching posts are found, it updates the query arguments to include only those posts.
 *
 * @param array  $args    The query arguments.
 * @param array  $field   The field array containing all field settings.
 * @param int    $post_id The post ID where the field is being displayed.
 *
 * @return array Modified query arguments.
 */
add_filter('acf/fields/relationship/query', function ($args, $field, $post_id) {
    if (is_array($args['post_type']) && in_array('pci_product', $args['post_type']) && !empty($args['s'])) {
        global $wpdb;

        $meta_query = $wpdb->prepare("
            SELECT post_id FROM {$wpdb->postmeta}
            WHERE meta_key = %s AND meta_value LIKE %s
        ", 'pci_id', '%' . $wpdb->esc_like($args['s']) . '%');

        $meta_results = $wpdb->get_col($meta_query);

        if (!empty($meta_results)) {
            unset($args['s']);
            $args['post__in'] = !empty($args['post__in'])
                ? array_intersect($args['post__in'], $meta_results)
                : $meta_results;
        }
    }

    return $args;
}, 10, 3);
