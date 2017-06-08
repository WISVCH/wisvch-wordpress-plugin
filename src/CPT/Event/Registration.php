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

    public $taxonomies = ['event-category'];

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
            'name' => __('Events', 'team-post-type'),
            'singular_name' => __('Event', 'team-post-type'),
            'add_new' => __('Add Event', 'team-post-type'),
            'add_new_item' => __('Add Event', 'team-post-type'),
            'edit_item' => __('Edit Event', 'team-post-type'),
            'new_item' => __('New Event', 'team-post-type'),
            'view_item' => __('View Event', 'team-post-type'),
            'search_items' => __('Search Event', 'team-post-type'),
            'not_found' => __('No events found', 'team-post-type'),
            'not_found_in_trash' => __('No events in the trash', 'team-post-type'),
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
            'rest_controller_class'
        ];

        $args = apply_filters('events_post_type_args', $args);

        register_post_type($this->post_type, $args);
    }

    private function register_taxonomy_category()
    {
        $labels = [
            'name' => __('Event Categories', 'team-post-type'),
            'singular_name' => __('Event Category', 'team-post-type'),
            'menu_name' => __('Categories', 'team-post-type'),
            'edit_item' => __('Edit Event Category', 'team-post-type'),
            'update_item' => __('Update Event Category', 'team-post-type'),
            'add_new_item' => __('Add New Event Category', 'team-post-type'),
            'new_item_name' => __('New Event Category Name', 'team-post-type'),
            'parent_item' => __('Parent Event Category', 'team-post-type'),
            'parent_item_colon' => __('Parent Event Category:', 'team-post-type'),
            'all_items' => __('All Categories', 'team-post-type'),
            'search_items' => __('Search Event Categories', 'team-post-type'),
            'popular_items' => __('Popular Event Categories', 'team-post-type'),
            'separate_items_with_commas' => __('Separate categories with commas', 'team-post-type'),
            'add_or_remove_items' => __('Add or remove event categories', 'team-post-type'),
            'choose_from_most_used' => __('Choose from the most used categories', 'team-post-type'),
            'not_found' => __('No event categories found.', 'team-post-type'),
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'show_tagcloud' => true,
            'hierarchical' => true,
            'rewrite' => ['slug' => 'event-category'],
            'show_admin_column' => true,
            'query_var' => true,
        ];

        $args = apply_filters('event_post_type_category_args', $args);

        register_taxonomy($this->taxonomies[0], $this->post_type, $args);

        // Also register for companies
        register_taxonomy_for_object_type($this->taxonomies[0], "event");
    }
}