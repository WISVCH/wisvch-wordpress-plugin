<?php

namespace WISVCH\CPT\Board;

/**
 * WP Query modifications for custom post type.
 *
 * @package   WISVCH\CPT\Board
 */
class Query
{
    /**
     * Hook into WordPress.
     */
    static function register_hooks()
    {
        add_action('pre_get_posts', [__CLASS__, 'admin_order']);
        add_action('pre_get_posts', [__CLASS__, 'archive_order']);

        // Fix unintended consequences of Post Type Order plug-in
        add_filter('pto/posts_orderby/ignore', [__CLASS__, 'fix_pto'], 10, 3);
    }

    /**
     * Alter main query.
     */
    static function archive_order($query)
    {

        if ($query->is_main_query() && $query->is_post_type_archive('board')) {
            $query->set('orderby', 'post_title');
            $query->set('posts_per_page', -1);
        }

        return $query;
    }

    /**
     * Alter admin query.
     */
    static function admin_order($query)
    {

        $q_cond = $query->is_admin && $query->get('post_type') === 'board';

        if ($q_cond) {

            if ($query->get('order') === '') {
                $query->set('order', 'desc');
            }

            if ($query->get('orderby') === '') {
                $query->set('orderby', 'title');
            }
        }

        return $query;
    }

    static function fix_pto($invalid, $orderby, $query)
    {
        if ($query->is_admin && $query->get('post_type') === 'board') {
            $invalid = true;
        }

        return $invalid;
    }
}
