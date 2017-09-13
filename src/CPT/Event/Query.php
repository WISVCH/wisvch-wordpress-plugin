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
}
