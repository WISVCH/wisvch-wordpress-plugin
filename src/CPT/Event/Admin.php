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
        $pt = &$this->registration_handler->post_type;

        // Add thumbnail support for this post type
        add_theme_support('post-thumbnails', [$pt]);

        // Add columns
        add_filter('manage_edit-'.$pt.'_columns', [$this, 'add_image_column'], 10, 1);
        add_action('manage_'.$pt.'_posts_custom_column', [$this, 'display_image'], 10, 1);
        add_action('manage_edit-'.$pt.'_sortable_columns', [$this, 'columns_sortable'], 10, 1);

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

            th#title {
                width: 20%;
            }

            th#taxonomy-event_category {
                width: 15%;
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
    function add_image_column($columns)
    {
        $columns = array_slice($columns, 0, 1, true) + ['thumbnail' => ''] + array_slice($columns, 1, null, true);

        return array_slice($columns, 0, 3, true) + ['event_date' => 'Event Date'] + array_slice($columns, 3, null, true);;
    }

    /**
     * Custom column callback
     *
     * @param string $column Column ID.
     */
    function display_image($column)
    {
        switch ($column) {
            case 'thumbnail':
                $thumb_url = get_the_post_thumbnail_url(get_the_ID(), [50, 50]);
                echo $thumb_url ? '<div class="cpt-thumb" style="background-image:url('.esc_url($thumb_url).');"></div>' : '';
                break;
            case 'event_date':

                $start = get_post_meta(get_the_ID(), '_event_start_date', true);
                $end = get_post_meta(get_the_ID(), '_event_end_date', true);

                echo $start ? '<strong>Start:</strong> '.esc_html($start) : '';
                echo $start && $end && $start !== $end ? '<br><strong>End:</strong> '.esc_html($end) : '';
                break;
        }
    }

    /**
     * Define sortable custom columns.
     *
     * @param string $columns Columns.
     * @return Columns.
     */
    function columns_sortable($columns)
    {
        $columns['event_date'] = 'event_date';

        return $columns;
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
     */
    public function add_glance_counts()
    {
        \WISVCH\WISVCH_Plugin::dashboard_glancer()->add($this->registration_handler->post_type, ['publish', 'pending']);
    }
}
