<?php

namespace WISVCH\CPT\JobOpening;

/**
 * Register post types and taxonomies.
 *
 * @package WISVCH\JobOpening;
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
        add_filter('manage_edit-'.$pt.'_columns', [$this, 'add_extra_columns'], 10, 1);
        add_action('manage_'.$pt.'_posts_custom_column', [$this, 'extra_columns'], 10, 1);

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
            th#company {
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
                background-size: contain;
                background-repeat: no-repeat;
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
    public function add_extra_columns($columns)
    {
        return array_slice($columns, 0, 1, true) + ['company' => 'Company'] + array_slice($columns, 1, null, true);
    }

    /**
     * Custom column callback
     *
     * @param string $column Column ID.
     */
    public function extra_columns($column)
    {
        switch ($column) {
            case 'company':
                $company_id = get_post_meta(get_the_ID(), '_company_id', true);

                if ($company_id) {

                    // Check if filtered on company
                    if (get_query_var('company_filter') !== '') {
                        echo '<span class="row-title">'.get_the_title($company_id).'</span>';
                        echo '<div class="row-actions"><span class="trash"><a href="'.remove_query_arg('company_filter').'">Reset filter</a></span></div>';
                    } else {
                        echo '<a class="row-title" href="'.add_query_arg('company_filter', $company_id).'">'.get_the_title($company_id).'</a>';
                        echo '<div class="row-actions"><a href="'.add_query_arg('company_filter', $company_id).'">Show only for this company</a></div>';
                    }
                }

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
     */
    public function add_glance_counts()
    {
        \WISVCH\WISVCH_Plugin::dashboard_glancer()->add($this->registration_handler->post_type, ['publish', 'pending']);
    }
}
