<?php

namespace WISVCH\CPT\Committee;

/**
 * Register post types and taxonomies.
 *
 * @package WISVCH\Committee;
 */
class Admin
{
    protected $registration_handler;

    public function __construct($registration_handler)
    {
        $this->registration_handler = $registration_handler;
        $pt = &$this->registration_handler->post_type;

        // Add thumbnail support for this post type
        add_theme_support('post-thumbnails', [$pt]);

        // Add thumbnails to column view
        add_filter('manage_edit-'.$pt.'_columns', [$this, 'add_image_column'], 10, 1);
        add_action('manage_'.$pt.'_posts_custom_column', [$this, 'display_image'], 10, 1);

        // Show post counts in the dashboard
        add_action('right_now_content_table_end', [$this, 'add_rightnow_counts']);
        add_action('dashboard_glance_items', [$this, 'add_glance_counts']);

        // Add admin CSS
        add_action('admin_print_styles-edit.php', [$this, 'custom_css']);
    }

    /**
     * Add some custom css.
     */
    function custom_css()
    {
        if (get_post_type() !== $this->registration_handler->post_type) {
            return;
        } ?>
        <style>
            th#thumbnail {
                width: 4em;
            }

            .cpt-thumb {
                width: 3.3em;
                height: 3.3em;
                border-radius: 50%;
                background-position: center;
                background-size: cover;
                margin: 0 auto;
            }
        </style>
    <?php }

    /**
     * Add columns to post type list screen.
     *
     * @param array $columns Existing columns.
     * @return array Amended columns.
     */
    public function add_image_column($columns)
    {
        return array_slice($columns, 0, 1, true) + ['thumbnail' => ''] + array_slice($columns, 1, null, true);
    }

    /**
     * Custom column callback
     *
     * @param string $column Column ID.
     */
    public function display_image($column)
    {
        if ($column === 'thumbnail') {
            $thumb_url = get_the_post_thumbnail_url(get_the_ID(), [50, 50]);
            echo $thumb_url ? '<div class="cpt-thumb" style="background-image:url('.esc_url($thumb_url).');"></div>' : '';
        }
    }

    /**
     * Add counts to "At a Glance" dashboard widget in WP 3.8+
     */
    public function add_glance_counts()
    {
        \WISVCH\WISVCH_Plugin::dashboard_glancer()->add($this->registration_handler->post_type, ['publish', 'pending']);
    }
}
