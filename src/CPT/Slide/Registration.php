<?php

namespace WISVCH\CPT\Slide;

/**
 * Register post type.
 *
 * @package CPT\Slide;
 */
class Registration
{
    public $post_type = 'slide';

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
            'name' => __('Slides', 'wisvch-plugin'),
            'singular_name' => __('Slide', 'wisvch-plugin'),
            'add_new' => __('Add Slide', 'wisvch-plugin'),
            'add_new_item' => __('Add New Slide', 'wisvch-plugin'),
            'edit_item' => __('Edit Slide', 'wisvch-plugin'),
            'new_item' => __('New Slide', 'wisvch-plugin'),
            'view_item' => __('View Slide', 'wisvch-plugin'),
            'search_items' => __('Search Slides', 'wisvch-plugin'),
            'not_found' => __('No slides found', 'wisvch-plugin'),
            'not_found_in_trash' => __('No slides in the trash', 'wisvch-plugin'),
        ];

        $supports = [
            'title',
            'editor',
            'thumbnail',
            'custom-fields',
            'revisions',
            'page-attributes'
        ];

        $args = [
            'labels' => $labels,
            'supports' => $supports,
            'public' => false,
            'show_ui' => true,
            'capability_type' => 'post',
            'rewrite' => false, // Permalinks format
            'menu_position' => 7,
            'menu_icon' => 'dashicons-images-alt',
        ];

        $args = apply_filters('slide_post_type_args', $args);

        register_post_type($this->post_type, $args);
    }
}