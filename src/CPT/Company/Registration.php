<?php

namespace WISVCH\CPT\Company;

/**
 * Register post types and taxonomies.
 *
 * @package WISVCH\Company;
 */
class Registration
{
    public $post_type = 'company';

    public $taxonomies = ['company_offerings', 'company_study'];

    public function init()
    {
        // Add the team post type and taxonomies
        add_action('init', [$this, 'register']);
    }

    /**
     * Initiate registrations of post type and taxonomies.
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
     */
    protected function register_post_type()
    {
        $labels = [
            'name' => 'Companies',
            'singular_name' => 'Company',
            'add_new' => 'Add Company',
            'add_new_item' => 'Add Company',
            'edit_item' => 'Edit Company',
            'new_item' => 'New Company',
            'view_item' => 'View Company',
            'search_items' => 'Search Company',
            'not_found' => 'No companies found',
            'not_found_in_trash' => 'No companies in the trash',
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

        register_post_type($this->post_type, $args);
    }

    /**
     * Register Company Offerings.
     */
    protected function register_taxonomy_category()
    {
        $labels = [
            'name' => 'Offerings',
            'singular_name' => 'Offering',
            'menu_name' => 'Offerings',
            'edit_item' => 'Edit Offerings',
            'update_item' => 'Update Offering',
            'add_new_item' => 'Add New Offering',
            'new_item_name' => 'New Offering Name',
            'parent_item' => 'Parent Offering',
            'parent_item_colon' => 'Parent Offering:',
            'all_items' => 'All Types',
            'search_items' => 'Search Offerings',
            'popular_items' => 'Popular Offerings',
            'separate_items_with_commas' => 'Separate types with commas',
            'add_or_remove_items' => 'Add or remove Offerings',
            'choose_from_most_used' => 'Choose from the most used types',
            'not_found' => 'No Offerings found.',
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
     * Register Company Study.
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
