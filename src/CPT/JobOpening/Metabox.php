<?php

namespace WISVCH\CPT\JobOpening;

/**
 * Register metaboxes.
 *
 * @package WISVCH\JobOpening;
 */
class Metabox
{
    public function init()
    {
        add_action('add_meta_boxes', [$this, 'meta_boxes']);
        add_action('save_post', [$this, 'save_meta_boxes'], 10, 2);
    }

    /**
     * Register the metaboxes to be used for the team post type
     *
     * @since 0.1.0
     */
    public function meta_boxes()
    {
        add_meta_box('job_opening_details', 'Job Opening', [$this, 'render_meta_boxes'], 'job_opening', 'side', 'high');
    }

    /**
     * The HTML for the fields
     *
     * @since 0.1.0
     */
    function render_meta_boxes($post)
    {

        $meta = get_post_custom($post->ID);
        $cID = ! isset($meta['_company_id'][0]) ? '' : (int) $meta['_company_id'][0];
        $location = ! isset($meta['_company_location'][0]) ? '' : $meta['_company_location'][0];

        wp_nonce_field(basename(__FILE__), 'job_opening_fields'); ?>

        <p>
            <b><label for="_company_id">Company:</label></b><br>
            <?php
            $companies = get_posts([
                'post_type' => 'company',
                'orderby' => 'post_name',
                'order' => 'ASC'
            ]);

            if (count($companies) > 0) {
                ?>

                <select name="_company_id" id="_company_id" class="widefat">
                    <option value="-1">- None -</option>
                    <?php
                    foreach ($companies as $company) {
                        ?>
                        <option value="<?php echo intval($company->ID); ?>" <?php selected($company->ID, $cID); ?>><?php echo esc_html($company->post_title); ?></option>
                        <?php
                    }
                    ?>
                </select>

                <?php
            }
            ?>
        </p>

        <p>
            <b><label for="_company_location">Location:</label></b><br>
            <input type="text" name="_company_location" class="widefat" value="<?php echo esc_attr($location); ?>">
        </p>

    <?php }

    /**
     * Save metaboxes
     *
     * @since 0.1.0
     */
    function save_meta_boxes($post_id)
    {

        global $post;

        // Verify nonce
        if (! isset($_POST['job_opening_fields']) || ! wp_verify_nonce($_POST['job_opening_fields'], basename(__FILE__))) {
            return $post_id;
        }

        // Check Autosave
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || (defined('DOING_AJAX') && DOING_AJAX) || isset($_REQUEST['bulk_edit'])) {
            return $post_id;
        }

        // Don't save if only a revision
        if (isset($post->post_type) && $post->post_type == 'revision') {
            return $post_id;
        }

        // Check permissions
        if (! current_user_can('edit_post', $post->ID)) {
            return $post_id;
        }

        // Validate id (numeric && >0)
        $meta['_company_id'] = isset($_POST['_company_id']) && is_numeric($_POST['_company_id']) && $_POST['_company_id'] > 0 ? intval($_POST['_company_id']) : '';
        $meta['_company_location'] = (isset($_POST['_company_location']) ? sanitize_text_field($_POST['_company_location']) : '');

        foreach ($meta as $key => $value) {
            update_post_meta($post->ID, $key, $value);
        }
    }
}