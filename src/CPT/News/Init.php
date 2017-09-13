<?php

namespace WISVCH\CPT\News;

/**
 * Rebrand default post type to News.
 *
 * @package   WISVCH\News
 */
class Init
{
    /**
     * Hook into WordPress.
     */
    public function __construct()
    {

        add_action('admin_menu', [$this, 'change_post_label']);
        add_action('init', [$this, 'change_post_object']);
    }

    /**
     * Update admin menu labels.
     */
    public function change_post_label()
    {
        global $menu;
        global $submenu;

        if (array_key_exists(5, $menu)) {
            $menu[5][0] = 'News';
        }

        if (array_key_exists('edit.php', $submenu)) {
            $submenu['edit.php'][5][0] = 'News';
            $submenu['edit.php'][10][0] = 'Add News Item';
        }
    }

    /**
     * Update post type object labels.
     */
    public function change_post_object()
    {
        global $wp_post_types;
        $labels = &$wp_post_types['post']->labels;
        $labels->name = 'News';
        $labels->singular_name = 'News Item';
        $labels->add_new = 'Add';
        $labels->add_new_item = 'Add News Item';
        $labels->edit_item = 'Edit News Item';
        $labels->new_item = 'News';
        $labels->view_item = 'View News Item';
        $labels->search_items = 'Search News';
        $labels->not_found = 'No News found';
        $labels->not_found_in_trash = 'No news items found in Trash';
        $labels->all_items = 'All News';
        $labels->menu_name = 'News';
        $labels->name_admin_bar = 'News';
    }
}
