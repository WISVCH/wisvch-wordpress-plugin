<?php

namespace WISVCH\CPT\Committee;

/**
 * Register post types and taxonomies.
 *
 * @package WISVCH\Committee;
 */
class Registration
{
    public $post_type = 'committee';

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
    }

    /**
     * Register the custom post type.
     *
     * @link http://codex.wordpress.org/Function_Reference/register_post_type
     */
    protected function register_post_type()
    {
        $labels = [
            'name' => 'Committees',
            'singular_name' => 'Committee',
            'add_new' => 'Add Committee',
            'add_new_item' => 'Add Committee',
            'edit_item' => 'Edit Committee',
            'new_item' => 'New Committee',
            'view_item' => 'View Committee',
            'search_items' => 'Search Committees',
            'not_found' => 'No committees found',
            'not_found_in_trash' => 'No committees in the trash',
        ];

        $supports = [
            'title',
            'editor',
            'thumbnail',
            'custom-fields',
            'revisions',
        ];

        $args = [
            'labels' => $labels,
            'supports' => $supports,
            'public' => true,
            'has_archive' => true,
            'capability_type' => 'page',
            'rewrite' => [
                'slug' => 'association/committees',
                'with_front' => false,
                'feeds' => null,
                'pages' => false,
            ],
            'menu_position' => 40,
            'menu_icon' => 'dashicons-groups',
        ];

        $args = apply_filters('committee_post_type_args', $args);

        register_post_type($this->post_type, $args);
    }
}
