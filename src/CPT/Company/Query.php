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
        add_action('posts_orderby', [__CLASS__, 'admin_order'], 100, 2);
        add_action('pre_get_posts', [__CLASS__, 'company_queries']);
    }

    /**
     * Alter main query.
     */
    static function company_queries(\WP_Query $query)
    {

        if (! $query->is_admin && $query->is_main_query()) {

            if ($query->is_post_type_archive('company')) {

                $query->set('orderby', 'rand');
                $query->set('posts_per_page', -1);
            } elseif ($query->is_tax(['company_study', 'company_offerings'])) {
                $query->set_404();
                status_header(404);
            }
        }

        return $query;
    }

    /**
     * Alter admin query.
     *
     * Note: can't use pre_get_posts filter for this, because the Post Types Order plug-in alters the admin order for -all- post types. -.-'
     */
    static function admin_order($orderby, $query)
    {

        // Only sort for admin pages
        if ($query->is_admin && $query->get('post_type') == 'company') {
            global $wpdb;

            $orderby = $wpdb->posts.".post_title ASC";
        }

        return $orderby;
    }
}
