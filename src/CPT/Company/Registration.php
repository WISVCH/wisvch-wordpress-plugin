<?php

namespace WISVCH\CPT\Company;

/**
 * Register post types and taxonomies.
 *
 * @package W3Cie_Post_Types\Company;
 */
class Registration
{
    public $post_type = 'company';

    public function init()
    {
        // Add the team post type and taxonomies
        add_action('init', [$this, 'register']);
    }

    /**
     * Initiate registrations of post type and taxonomies.
     *
     * @uses Team_Post_Type_Registrations::register_post_type()
     * @uses Team_Post_Type_Registrations::register_taxonomy_category()
     */
    public function register()
    {
        $this->register_post_type();
    }

    /**
     * Register the custom post type.
     *
     * @link http://codex.wordpress.org/Function_Reference/register_post_type
     */
    protected function register_post_type()
    {
        $labels = [
            'name' => __('Companies', 'team-post-type'),
            'singular_name' => __('Company', 'team-post-type'),
            'add_new' => __('Add Company', 'team-post-type'),
            'add_new_item' => __('Add Company', 'team-post-type'),
            'edit_item' => __('Edit Company', 'team-post-type'),
            'new_item' => __('New Company', 'team-post-type'),
            'view_item' => __('View Company', 'team-post-type'),
            'search_items' => __('Search Company', 'team-post-type'),
            'not_found' => __('No companies found', 'team-post-type'),
            'not_found_in_trash' => __('No companies in the trash', 'team-post-type'),
        ];

        $supports = [
            'title',
            'editor',
            'excerpt',
            'thumbnail',
            'revisions',
        ];

        $args = [
            'labels' => $labels,
            'supports' => $supports,
            'public' => true,
            'has_archive' => true,
            'capability_type' => 'page',
            'rewrite' => [
                'slug' => 'career/companies',
                'with_front' => false,
                'feeds' => null,
                'pages' => false,
            ],
            'menu_position' => 40,
            'menu_icon' => 'dashicons-portfolio',
        ];

        $args = apply_filters('company_post_type_args', $args);

        register_post_type($this->post_type, $args);
    }
}