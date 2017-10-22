<?php

namespace WISVCH\CPT\Event;

/**
 * WP Query modifications for custom post type.
 *
 * @package   WISVCH\CPT\Event
 */
class Query
{
    /**
     * Hook into WordPress.
     */
    static function register_hooks()
    {
        is_admin() && add_action('pre_get_posts', [__CLASS__, 'archive_order']);

        // Fix unintended consequences of Post Type Order plug-in
        add_filter('pto/posts_orderby/ignore', [__CLASS__, 'fix_pto'], 10, 3);

        // Add rewrite tag for event year
        add_action('init', [__CLASS__, 'add_rewrite_rule']);
        add_filter('post_type_link', [__CLASS__, 'process_link'], 10, 2);
    }

    /**
     * Alter admin query.
     */
    static function archive_order($query)
    {

        $q_cond = $query->is_admin && $query->get('post_type') === 'event';

        // Support custom sortable column
        if ($q_cond && ($query->get('orderby') == '' || $query->get('orderby') == 'event_date')) {

            $query->set('meta_query', [
                'event_order' => [
                    'key' => '_event_start_date',
                    'type' => 'DATETIME',
                ],
            ]);

            $query->set('orderby', 'event_order');
        }

        return $query;
    }

    static function fix_pto($invalid, $orderby, $query)
    {
        if ($query->is_admin && $query->get('post_type') === 'event') {
            $invalid = true;
        }

        return $invalid;
    }

    /**
     * Add event_year rewrite tag.
     */
    static function add_rewrite_tag()
    {
        add_rewrite_tag('%event_year%', '([0-9]+)');
    }

    /**
     * Replace event_year rewrite tag.
     */
    static function event_permalink($permalink, $post, $leavename)
    {

        // Abort early if the placeholder rewrite tag isn't in the generated URL
        if (false === strpos($permalink, '%event_year%')) {
            return $permalink;
        }

        // Fallback
        if (empty($post) || empty($post->ID)) {
            return $permalink;
        }

        // Get and parse date
        $date = get_post_meta($post->ID, '_event_start_date', true);

        // Use post date if event date not available
        $url_year = empty($date) ? get_the_date('Y', $post) : date('Y', strtotime($date));

        // Replace variable in permalink
        return str_replace('%event_year%', $url_year, $permalink);
    }

    /**
     * Add rewrite rule for event year in permalink.
     */
    static function add_rewrite_rule()
    {
        add_rewrite_rule("^".Registration::PERMALINK_BASE."/([^/]+)/([^/]+)(?:/([0-9]+))?/?$", 'index.php?event=$matches[2]&page=$matches[3]', 'top');
    }

    /**
     * Add event year to event permalinks.
     */
    static function process_link($post_link, $id = 0)
    {

        $post = get_post($id);

        if (is_wp_error($post) || 'event' !== $post->post_type) {
            return $post_link;
        }

        // Get company ID
        $date = get_post_meta($post->ID, '_event_start_date', true);

        // Use post date if event date not available
        $url_year = empty($date) ? get_the_date('Y', $post) : date('Y', strtotime($date));

        return home_url(user_trailingslashit(Registration::PERMALINK_BASE.'/'.$url_year.'/'.$post->post_name));
    }
}
