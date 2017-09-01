<?php

namespace WISVCH\CPT\Committee;

/**
 * WP Query modifications for custom post type.
 *
 * @package   WISVCH\CPT\Committee
 */
class Query
{
    /**
     * Hook into WordPress.
     */
    static function register_hooks()
    {
        add_action('pre_get_posts', [__CLASS__, 'archive_order']);
    }

    /**
     * Alter main query.
     */
    static function archive_order($query)
    {

        if ($query->is_main_query() && $query->is_post_type_archive('committee')) {

            $query->set('order', 'ASC');
            $query->set('orderby', 'post_title');
            $query->set('posts_per_page', -1);
        }

        return $query;
    }
}
