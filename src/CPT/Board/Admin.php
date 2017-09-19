<?php

namespace WISVCH\CPT\Board;

/**
 * Register post types and taxonomies.
 *
 * @package WISVCH\Board;
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
        add_filter('manage_edit-'.$pt.'_columns', [$this, 'add_custom_columns'], 10, 1);
        add_action('manage_'.$pt.'_posts_custom_column', [$this, 'custom_columns'], 10, 1);

        // Show post counts in the dashboard
        add_action('right_now_content_table_end', [$this, 'add_rightnow_counts']);
        add_action('dashboard_glance_items', [$this, 'add_glance_counts']);

        add_filter('default_content', [$this, 'prepopulate_content'], 10, 2);

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

            th#year {
                width: 10%;
            }

            th#taxonomy-job-type,
            th#taxonomy-job-study {
                width: 17.5%;
            }

            .cpt-thumb {
                width: 3.3em;
                height: 3.3em;
                background-position: center;
                background-size: cover;
                border-radius:50%;
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
    public function add_custom_columns($columns)
    {
        return array_slice($columns, 0, 1, true) + ['thumbnail' => ''] + array_slice($columns, 0, 2, true) + ['year' => 'Academic year'] + array_slice($columns, 1, null, true);
    }

    /**
     * Custom column callback
     *
     * @param string $column Column ID.
     */
    public function custom_columns($column)
    {

        switch ($column) {
            case 'thumbnail':
                $thumb_url = get_the_post_thumbnail_url(get_the_ID(), [50, 50]);
                echo $thumb_url ? '<div class="cpt-thumb" style="background-image:url('.esc_url($thumb_url).');"></div>' : '';
                break;
            case 'year':
                echo get_post_meta(get_the_ID(), '_board_year', true);
                break;
        }
    }

    /**
     * Add counts to "At a Glance" dashboard widget in WP 3.8+
     */
    public function add_glance_counts()
    {
        \WISVCH\WISVCH_Plugin::dashboard_glancer()->add($this->registration_handler->post_type, ['publish', 'pending']);
    }

    /**
     * Pre-popluates content field.
     *
     * @return string
     */
    public function prepopulate_content($post_content, $post)
    {

        // Verify post type
        if ($post->post_type !== $this->registration_handler->post_type) {
            return;
        }

        $content = '<table cellspacing="0" cellpadding="0">
<thead>
<tr>
<td colspan="2">From left to right</td>
</tr>
</thead>
<tbody>
<tr>
<td>Public Relations</td>
<td><strong>Name</strong></td>
</tr>
<tr>
<td>Applied Mathematics Education Affairs</td>
<td><strong>Name</strong></td>
</tr>
<tr>
<td>Secretary</td>
<td><strong>Name</strong></td>
</tr>
<tr>
<td>Chairman</td>
<td><strong>Name</strong></td>
</tr>
<tr>
<td>Treasurer</td>
<td><strong>Name</strong></td>
</tr>
<tr>
<td>Computer Science Education Affairs</td>
<td><strong>Name</strong></td>
</tr>
<tr>
<td>Career Affairs</td>
<td><strong>Name</strong></td>
</tr>
</tbody>
</table>';

        return $content;
    }
}
