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
            'name' => __('Committees', 'team-post-type'),
            'singular_name' => __('Committee', 'team-post-type'),
            'add_new' => __('Add Committee', 'team-post-type'),
            'add_new_item' => __('Add Committee', 'team-post-type'),
            'edit_item' => __('Edit Committee', 'team-post-type'),
            'new_item' => __('New Committee', 'team-post-type'),
            'view_item' => __('View Committee', 'team-post-type'),
            'search_items' => __('Search Committees', 'team-post-type'),
            'not_found' => __('No committees found', 'team-post-type'),
            'not_found_in_trash' => __('No committees in the trash', 'team-post-type'),
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