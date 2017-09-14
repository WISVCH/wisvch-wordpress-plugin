<?php

namespace WISVCH\CPT\Company;

/**
 * Register post types and taxonomies.
 *
 * @package WISVCH\Company;
 */
class Admin
{
    protected $registration_handler;

    function __construct($registration_handler)
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

        // Hijack trash post link to add warning screen
        add_action('admin_menu', [$this, 'confirm_trash_page']);
        add_filter('get_delete_post_link', [$this, 'confirm_trash_link'], 10, 3);
        is_admin() && add_action('wp_loaded', [$this, 'confirm_trashed_redirect']);
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
    function add_image_column($columns)
    {
        return array_slice($columns, 0, 1, true) + ['thumbnail' => ''] + array_slice($columns, 1, null, true);
    }

    /**
     * Custom column callback
     *
     * @param string $column Column ID.
     */
    function display_image($column)
    {
        if ($column === 'thumbnail') {
            $thumb_url = get_the_post_thumbnail_url(get_the_ID(), [50, 50]);
            echo $thumb_url ? '<div class="cpt-thumb" style="background-image:url('.esc_url($thumb_url).');"></div>' : '';
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
    function add_glance_counts()
    {
        \WISVCH\WISVCH_Plugin::dashboard_glancer()->add($this->registration_handler->post_type, ['publish', 'pending']);
    }

    /**
     * Add hidden admin page to confirm deletion of company.
     */
    function confirm_trash_page()
    {
        add_submenu_page(null, 'Confirm delete company', 'Confirm delete company', 'edit_pages', 'confirm_delete_company', [$this, 'confirm_trash_page_contents']);
    }

    /**
     * Contents of confirm deletion of company page.
     */
    function confirm_trash_page_contents()
    {

        $company_id = $_GET['post'];

        // Verify nonce
        if (empty($_GET['_delete_company_nonce']) || ! wp_verify_nonce($_GET['_delete_company_nonce'], '_delete_company_'.$company_id)) {
            wp_die('Error &minus; Bad request.', 'Error', [
                'response' => 400,
                'back_link' => true,
            ]);
            die;
        }

        // Verify query vars
        $company = get_post($company_id);

        if (! $company || $company->post_type !== 'company' || $company->post_status !== 'publish') {
            wp_die('Error &minus; Company does not exist.', 'Error', [
                'response' => 400,
                'back_link' => true,
            ]);
            die;
        }

        // Get job openings
        $job_openings = get_posts([
            'post_type' => 'job_opening',
            'posts_per_page' => -1,
            'meta_key' => '_company_id',
            'meta_value' => $company->ID,
        ]);

        $count = count($job_openings);

        ?>

        <div id="wrap">
            <h2>Confirm deletion of <b><?=esc_html($company->post_title);?></b></h2>
            <?php if ($count > 0) { ?>

                <p><strong>Warning!</strong> There <?=$count === 1 ? 'is still 1 active job opening' : 'are still '.$count.' active job openings'?> linked to this company.</p>

                <ul style="margin-left:2rem;list-style:disc;">
                    <?php foreach ($job_openings as $jo) { ?>
                        <li><a target="new" href="<?=get_edit_post_link($jo->ID)?>"><?=esc_html($jo->post_title)?></a></li>
                    <?php } ?>
                </ul>

                <p>Please go back and remove all remaining job openings before deleting this company.</p>

                <p>
                    <a href="<?=get_edit_post_link($company->ID)?>" class="button button-primary">&lsaquo; Go back</a>
                </p>

            <?php } else { ?>
                <p>There are no active job openings linked to this company.</p>
                <p>
                    <a href="<?=get_edit_post_link($company->ID)?>" class="button">Go back</a>
                    <a href="<?=get_delete_post_link($company->ID)?>" class="button button-primary">Delete <?=esc_html($company->post_title);?></a>
                </p>
            <?php } ?>
        </div>

        <?php

    }

    /**
     * Filters the post delete link.
     *
     * @param string $link The delete link.
     * @param int $post_id Post ID.
     * @return New trash URL.
     */
    function confirm_trash_link($url, $post_id, $force)
    {

        // Don't alter link when deleting company permanently
        if ($force === true) {
            return $url;
        }

        // Check if on delete company page
        $nonce_check = ! empty($_GET['_delete_company_nonce']) && wp_verify_nonce($_GET['_delete_company_nonce'], '_delete_company_'.$post_id);

        // Alter link for edit-company screen
        if ('company' === get_post_type($post_id) && ! $nonce_check) {
            $url = get_admin_url(null, 'admin.php?page=confirm_delete_company&post='.$post_id);
            $nonce_url = wp_nonce_url($url, '_delete_company_'.$post_id, '_delete_company_nonce');

            return esc_url($nonce_url);
        }

        return $url;
    }

    /**
     * Check if company successfully trashed, redirect to overview instead of confirm deletion page.
     */
    function confirm_trashed_redirect()
    {

        // Check if on delete company page
        $nonce_check = ! empty($_GET['_delete_company_nonce']) && wp_verify_nonce($_GET['_delete_company_nonce'], '_delete_company_'.$_GET['post']);

        if (isset($_GET['trashed']) && $_GET['trashed'] === '1' && $nonce_check) {
            wp_redirect(admin_url('edit.php?post_status=trash&post_type=company'));
            exit;
        }
    }
}
