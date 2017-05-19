<?php

namespace WISVCH\CPT\Board;

/**
 * Register post type.
 *
 * @package CPT\Board;
 */
class Registration
{
    public $post_type = 'board';

    public function init()
    {
        // Add the team post type
        add_action('init', [$this, 'register']);
    }

    /**
     * Initiate registration of post type.
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
            'name' => __('Boards', 'wisvch-plugin'),
            'singular_name' => __('Board', 'wisvch-plugin'),
            'add_new' => __('Add Board', 'wisvch-plugin'),
            'add_new_item' => __('Add New Board', 'wisvch-plugin'),
            'edit_item' => __('Edit Board', 'wisvch-plugin'),
            'new_item' => __('New Board', 'wisvch-plugin'),
            'view_item' => __('View Board', 'wisvch-plugin'),
            'search_items' => __('Search Boards', 'wisvch-plugin'),
            'not_found' => __('No boards found', 'wisvch-plugin'),
            'not_found_in_trash' => __('No boards in the trash', 'wisvch-plugin'),
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
                'slug' => 'association/boards',
                'with_front' => false,
                'feeds' => null,
                'pages' => false,
            ],
            'menu_position' => 40,
            'menu_icon' => 'dashicons-businessman',
        ];

        $args = apply_filters('board_post_type_args', $args);

        register_post_type($this->post_type, $args);
    }
}