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
        is_admin() && add_action('pre_get_posts', [__CLASS__, 'admin_filter']);
        add_action('pre_get_posts', [__CLASS__, 'job_opening_query']);
        add_filter('query_vars', [__CLASS__, 'custom_query_vars']);
    }

    /**
     * Alter main query.
     */
    static function job_opening_query(\WP_Query $query)
    {

        if (! $query->is_admin && $query->is_main_query() && $query->get('post_type', false) === 'job_opening') {

            // All job openings depend on company
            $query->set('meta_query', [
                [
                    'key' => '_company_id',
                    'value' => static::_get_active_companies(),
                    'compare' => 'IN',
                ],
            ]);

            // Randomize archives
            if ($query->is_post_type_archive()) {

                $query->set('orderby', 'rand');
                $query->set('posts_per_page', -1);
            }
        }

        return $query;
    }

    /**
     * Register custom query vars.
     */
    static function custom_query_vars($vars)
    {
        $vars[] = "company_filter";

        return $vars;
    }

    /**
     * Implement admin company filter.
     */
    static function admin_filter(\WP_Query $query)
    {
        $q_cond = $query->is_admin && $query->get('post_type') === 'job_opening';

        if ($q_cond && $query->get('company_filter') !== '') {

            $company = get_post($query->get('company_filter'));

            // Check if company exists
            if ($company && $company->post_type === 'company') {

                $query->set('meta_key', '_company_id');
                $query->set('meta_value', $company->ID);
            }
        }

        return $query;
    }

    /**
     * Based on get_all_page_ids().
     *
     * @return array List of page IDs.
     */
    private static function _get_active_companies()
    {
        global $wpdb;

        $page_ids = wp_cache_get('all_company_ids', 'posts');
        if (! is_array($page_ids)) {
            $page_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = 'company' AND post_status = 'publish'");
            wp_cache_add('all_company_ids', $page_ids, 'posts');
        }

        return $page_ids;
    }
}
