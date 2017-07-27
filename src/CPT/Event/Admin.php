<?php

namespace WISVCH\CPT\Event;

/**
 * Register post types and taxonomies.
 *
 * @package WISVCH\CPT\Event;
 */
class Admin
{
    protected $registration_handler;

    public function __construct($registration_handler)
    {
        $this->registration_handler = $registration_handler;
    }

    public function init()
    {

        // Add thumbnail support for this post type
        add_theme_support('post-thumbnails', [$this->registration_handler->post_type]);

        // Add thumbnails to column view
        add_filter('manage_edit-'.$this->registration_handler->post_type.'_columns', [$this, 'add_image_column'], 10, 1);
        add_action('manage_'.$this->registration_handler->post_type.'_posts_custom_column', [$this, 'display_image'], 10, 1);

        // Show post counts in the dashboard
        add_action('right_now_content_table_end', [$this, 'add_rightnow_counts']);
        add_action('dashboard_glance_items', [$this, 'add_glance_counts']);
    }

    /**
     * Add columns to post type list screen.
     *
     * @link http://wptheming.com/2010/07/column-edit-pages/
     *
     * @param array $columns Existing columns.
     *
     * @return array Amended columns.
     */
    public function add_image_column($columns)
    {
        $column_thumbnail = ['thumbnail' => __('Image', 'team-post-type')];

        return array_slice($columns, 0, 2, true) + $column_thumbnail + array_slice($columns, 1, null, true);
    }

    /**
     * Custom column callback
     *
     * @global stdClass $post Post object.
     *
     * @param string $column Column ID.
     */
    public function display_image($column)
    {

        // global $post;
        switch ($column) {
            case 'thumbnail':
                // echo get_the_post_thumbnail( $post->ID, array(35, 35) );
                echo get_the_post_thumbnail(get_the_ID(), [35, 35]);
                break;
        }
    }

    /**
     * Build a series of option elements from an array.
     *
     * Also checks to see if one of the options is selected.
     *
     * @param  array $terms Array of term objects.
     * @param  string $current_tax_slug Slug of currently selected term.
     *
     * @return string Markup.
     */
    protected function build_term_options($terms, $current_tax_slug)
    {
        $options = '';
        foreach ($terms as $term) {
            $options .= sprintf('<option value="%s"%s />%s</option>', esc_attr($term->slug), selected($current_tax_slug, $term->slug), esc_html($term->name.'('.$term->count.')'));
        }

        return $options;
    }

    /**
     * Add counts to "At a Glance" dashboard widget in WP 3.8+
     *
     * @since 0.1.0
     */
    public function add_glance_counts()
    {
        \WISVCH\WISVCH_Plugin::dashboard_glancer()->add($this->registration_handler->post_type, ['publish', 'pending']);
    }
}