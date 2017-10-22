<?php

namespace WISVCH\CPT\JobOpening;

/**
 * Register post types and taxonomies.
 *
 * @package WISVCH\JobOpening;
 */
class Registration
{
    public $post_type = 'job_opening';

    public $taxonomies = ['job_type', 'job_study'];

    const PERMALINK_BASE = 'career/job-openings';

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
        // @link https://cnpagency.com/blog/the-right-way-to-do-wordpress-custom-taxonomy-rewrites/
        $this->register_taxonomy_category();
        $this->register_taxonomy_study();

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
            'name' => 'Job Openings',
            'singular_name' => 'Job Opening',
            'add_new' => 'Add Job Opening',
            'add_new_item' => 'Add Job Opening',
            'edit_item' => 'Edit Job Opening',
            'new_item' => 'New Job Opening',
            'view_item' => 'View Job Opening',
            'search_items' => 'Search Job Opening',
            'not_found' => 'No job openings found',
            'not_found_in_trash' => 'No job openings in the trash',
        ];

        $supports = [
            'title',
            'editor',
            'excerpt',
            'revisions',
        ];

        $args = [
            'labels' => $labels,
            'supports' => $supports,
            'public' => true,
            'has_archive' => true,
            'capability_type' => 'post',
            'rewrite' => [
                'slug' => self::PERMALINK_BASE,
                'with_front' => false,
                'feeds' => null,
                'pages' => false,
            ],
            'menu_position' => 40,
            'menu_icon' => 'dashicons-megaphone',
        ];

        register_post_type($this->post_type, $args);
    }

    /**
     * Register Job Opening Categories.
     */
    protected function register_taxonomy_category()
    {
        $labels = [
            'name' => 'Job Opening Types',
            'singular_name' => 'Job Opening Type',
            'menu_name' => 'Types',
            'edit_item' => 'Edit Job Opening Type',
            'update_item' => 'Update Job Opening Type',
            'add_new_item' => 'Add New Job Opening Type',
            'new_item_name' => 'New Job Opening Type Name',
            'parent_item' => 'Parent Job Opening Type',
            'parent_item_colon' => 'Parent Job Opening Type:',
            'all_items' => 'All Types',
            'search_items' => 'Search Job Opening Types',
            'popular_items' => 'Popular Job Opening Types',
            'separate_items_with_commas' => 'Separate types with commas',
            'add_or_remove_items' => 'Add or remove job opening types',
            'choose_from_most_used' => 'Choose from the most used types',
            'not_found' => 'No job opening types found.',
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true,
            'rewrite' => false,
            'show_admin_column' => true,
            'query_var' => true,
        ];

        register_taxonomy($this->taxonomies[0], $this->post_type, $args);
    }

    /**
     * Register Job Opening Study.
     */
    protected function register_taxonomy_study()
    {
        $labels = [
            'name' => 'Studies',
            'singular_name' => 'Study',
            'menu_name' => 'Studies',
            'edit_item' => 'Edit Study',
            'update_item' => 'Update Study',
            'add_new_item' => 'Add New Study',
            'new_item_name' => 'New Study Name',
            'parent_item' => 'Parent Study',
            'parent_item_colon' => 'Parent Study:',
            'all_items' => 'All Studies',
            'search_items' => 'Search Studies',
            'popular_items' => 'Popular Studies',
            'separate_items_with_commas' => 'Separate studies with commas',
            'add_or_remove_items' => 'Add or remove studies',
            'choose_from_most_used' => 'Choose from the most used studies',
            'not_found' => 'No studies found.',
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true,
            'rewrite' => false,
            'show_admin_column' => true,
            'query_var' => true,
        ];

        register_taxonomy($this->taxonomies[1], $this->post_type, $args);
    }
}
