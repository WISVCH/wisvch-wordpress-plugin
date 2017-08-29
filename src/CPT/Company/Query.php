<?php

namespace WISVCH\CPT\Company;

/**
 * WP Query modifications for custom post type.
 *
 * @package   WISVCH\Company
 */
class Query
{
    /**
     * Hook into WordPress.
     */
    static function register_hooks()
    {
        add_action('pre_get_posts', [__CLASS__, 'randomize_archive']);
    }

    /**
     * Alter main query.
     */
    static function randomize_archive($query)
    {

        if ($query->is_main_query() && $query->is_post_type_archive('company')) {

            $query->set('orderby', 'rand');
            $query->set('posts_per_page', -1);
        }

        return $query;
    }
}
