<?php

namespace WISVCH\CPT\Event;

/**
 * Register post types and taxonomies.
 *
 * @package WISVCH\CPT\Event;
 */
class Registration
{
    public $post_type = 'event';

    public $taxonomies = ['event_category'];

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
        $this->register_post_type();
        $this->register_taxonomy_category();
    }

    /**
     * Register the custom post type.
     *
     * @link http://codex.wordpress.org/Function_Reference/register_post_type
     */
    protected function register_post_type()
    {
        $labels = [
            'name' => __('Events', 'wisvch-plugin'),
            'singular_name' => __('Event', 'wisvch-plugin'),
            'add_new' => __('Add Event', 'wisvch-plugin'),
            'add_new_item' => __('Add Event', 'wisvch-plugin'),
            'edit_item' => __('Edit Event', 'wisvch-plugin'),
            'new_item' => __('New Event', 'wisvch-plugin'),
            'view_item' => __('View Event', 'wisvch-plugin'),
            'search_items' => __('Search Event', 'wisvch-plugin'),
            'not_found' => __('No events found', 'wisvch-plugin'),
            'not_found_in_trash' => __('No events in the trash', 'wisvch-plugin'),
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
            'has_archive' => false,
            'capability_type' => 'page',
            'rewrite' => [
                'slug' => 'activities/event',
                'with_front' => false,
                'feeds' => null,
                'pages' => false,
            ],
            'menu_position' => 40,
            'menu_icon' => 'dashicons-calendar',
            'show_in_rest' => true,
            'rest_base' => 'events',
            'rest_controller_class',
        ];

        $args = apply_filters('events_post_type_args', $args);

        register_post_type($this->post_type, $args);
    }

    /**
     * Register the custom post type.
     *
     * @link http://codex.wordpress.org/Function_Reference/register_post_type
     */
    protected function register_post_type_product()
    {
        $labels = [
            'name' => __('Products', 'wisvch-plugin'),
            'singular_name' => __('Product', 'wisvch-plugin'),
            'add_new' => __('Add Product', 'wisvch-plugin'),
            'add_new_item' => __('Add Product', 'wisvch-plugin'),
            'edit_item' => __('Edit Product', 'wisvch-plugin'),
            'new_item' => __('New Product', 'wisvch-plugin'),
            'view_item' => __('View Product', 'wisvch-plugin'),
            'search_items' => __('Search Product', 'wisvch-plugin'),
            'not_found' => __('No events found', 'wisvch-plugin'),
            'not_found_in_trash' => __('No events in the trash', 'wisvch-plugin'),
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
            'public' => false,
            'has_archive' => false,
            'rewrite' => false
        ];

        $args = apply_filters('events_post_type_args', $args);

        register_post_type('product', $args);
    }

    private function register_taxonomy_category()
    {
        $labels = [
            'name' => __('Event Categories', 'wisvch-plugin'),
            'singular_name' => __('Event Category', 'wisvch-plugin'),
            'menu_name' => __('Categories', 'wisvch-plugin'),
            'edit_item' => __('Edit Event Category', 'wisvch-plugin'),
            'update_item' => __('Update Event Category', 'wisvch-plugin'),
            'add_new_item' => __('Add New Event Category', 'wisvch-plugin'),
            'new_item_name' => __('New Event Category Name', 'wisvch-plugin'),
            'parent_item' => __('Parent Event Category', 'wisvch-plugin'),
            'parent_item_colon' => __('Parent Event Category:', 'wisvch-plugin'),
            'all_items' => __('All Categories', 'wisvch-plugin'),
            'search_items' => __('Search Event Categories', 'wisvch-plugin'),
            'popular_items' => __('Popular Event Categories', 'wisvch-plugin'),
            'separate_items_with_commas' => __('Separate categories with commas', 'wisvch-plugin'),
            'add_or_remove_items' => __('Add or remove event categories', 'wisvch-plugin'),
            'choose_from_most_used' => __('Choose from the most used categories', 'wisvch-plugin'),
            'not_found' => __('No event categories found.', 'wisvch-plugin'),
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'show_tagcloud' => true,
            'hierarchical' => true,
            'rewrite' => [
                'slug' => 'activities/event-category',
                'with_front' => false,
            ],
            'show_admin_column' => true,
            'query_var' => true,
        ];

        $args = apply_filters('event_post_type_category_args', $args);

        register_taxonomy($this->taxonomies[0], $this->post_type, $args);

        // Also register for companies
        register_taxonomy_for_object_type($this->taxonomies[0], "event");
    }
}
