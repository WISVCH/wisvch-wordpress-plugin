<?php

namespace WISVCH\CPT\JobOpening;

/**
 * WP Query modifications for custom post type.
 *
 * @package   WISVCH\JobOpening
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
    static function randomize_archive(\WP_Query $query)
    {

        if ($query->is_main_query() && $query->is_post_type_archive('job_opening')) {

            $query->set('orderby', 'rand');
            $query->set('posts_per_page', -1);
        }

        return $query;
    }
}
