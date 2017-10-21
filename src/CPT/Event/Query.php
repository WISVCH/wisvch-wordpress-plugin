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
        add_action('init', [__CLASS__, 'add_rewrite_tag']);
        add_filter('post_type_link', [__CLASS__, 'event_permalink'], 1, 3);
        add_action('parse_query', [__CLASS__, 'event_permalink_check'], 20);
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
     * Short-circuit queries with an event_year parameter, but without an event parameter in the URL.
     *
     * @param $query WP_Query object.
     */
    static function event_permalink_check($query)
    {

        $year = array_key_exists('event_year', $query->query_vars) ? $query->query_vars['event_year'] : false;

        if ($query->is_main_query() && ! empty($year) && ! isset($query->query['event'])) {

            $query->set_404();
            status_header(404);
        }
    }
}
