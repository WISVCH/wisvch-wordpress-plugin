<?php

namespace WISVCH\CPT\Company;

/**
 * Register metaboxes.
 *
 * @package WISVCH\Company;
 */
class Metabox
{
    public function init()
    {
        add_action('add_meta_boxes', [$this, 'meta_boxes']);
        add_action('save_post', [$this, 'save_meta_boxes'], 10, 2);
    }

    /**
     * Register the metaboxes to be used for the company post type
     */
    public function meta_boxes()
    {
        add_meta_box('company_details', 'Company Details', [$this, 'render_meta_boxes'], 'company', 'advanced', 'high');
    }

    /**
     * The HTML for the fields
     */
    function render_meta_boxes($post)
    {

        $meta = get_post_custom($post->ID);
        $address = ! isset($meta['_company_address'][0]) ? '' : $meta['_company_address'][0];
        $phone = ! isset($meta['_company_phone'][0]) ? '' : $meta['_company_phone'][0];
        $email = ! isset($meta['_company_email'][0]) ? '' : $meta['_company_email'][0];
        $website = ! isset($meta['_company_website'][0]) ? '' : $meta['_company_website'][0];
        $linkto = ! isset($meta['_link_to'][0]) ? '' : $meta['_link_to'][0];

        wp_nonce_field(basename(__FILE__), 'company_fields');

        // @TODO move inline css to separate file (or use default WP admin CSS)
        ?>

        <div style="width:50%; float:left; box-sizing:border-box;padding-right:1rem;">

            <p>
                <b><label>Address</label></b><br>
                <textarea name="_company_address" class="widefat" rows="3" style="resize:none;"><?php echo esc_textarea($address); ?></textarea>
            </p>

            <p>
                <b><label>Phone Number</label></b><br>
                <input type="text" name="_company_phone" class="widefat" value="<?php echo esc_attr($phone); ?>">
            </p>

        </div>
        <div style="width:50%; float:left;box-sizing:border-box;padding-left:1rem;">

            <p>
                <b><label>Email Address</label></b><br>
                <input type="text" name="_company_email" class="widefat" value="<?php echo esc_attr($email); ?>">
            </p>

            <p>
                <b><label>Website URL</label></b><br>
                <input type="text" name="_company_website" class="widefat" value="<?php echo esc_attr($website); ?>">
            </p>

            <p>
                <b><label>Link to...</label></b><br>
                <select name="_link_to" class="widefat">
                    <option value="url" <?php selected($linkto, "url"); ?>>Website</option>
                    <option value="post" <?php selected($linkto, "post"); ?>>Company Profile</option>
                </select>
            </p>
        </div>

        <br class="clear">


    <?php }

    /**
     * Save metaboxes
     */
    function save_meta_boxes($post_id)
    {

        global $post;

        // Verify nonce
        if (! isset($_POST['company_fields']) || ! wp_verify_nonce($_POST['company_fields'], basename(__FILE__))) {
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

        $meta['_company_address'] = (isset($_POST['_company_address']) ? wp_kses_post($_POST['_company_address']) : '');
        $meta['_company_phone'] = (isset($_POST['_company_phone']) ? sanitize_text_field($_POST['_company_phone']) : '');
        $meta['_company_email'] = (isset($_POST['_company_email']) ? sanitize_email($_POST['_company_email']) : '');
        $meta['_company_website'] = (isset($_POST['_company_website']) ? esc_url_raw($_POST['_company_website']) : '');

        // Default to "url" if value is invalid
        $meta['_link_to'] = ! in_array($_POST['_link_to'], ["post", "url"]) ? "url" : $_POST['_link_to'];

        foreach ($meta as $key => $value) {
            update_post_meta($post->ID, $key, $value);
        }
    }
}