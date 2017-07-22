<?php

namespace WISVCH\CPT\Event;

/**
 * Register metaboxes.
 *
 * @package WISVCH\CPT\Event;
 */
class Metabox
{
    public function init()
    {
        add_action('admin_enqueue_scripts', [$this, 'load_assets']);

        add_action('add_meta_boxes', [$this, 'meta_boxes']);
        add_action('save_post', [$this, 'save_meta_boxes'], 10, 2);
    }

    public function load_assets($hook)
    {

        if (in_array($hook, ['post.php', 'post-new.php'])) {
            $screen = get_current_screen();

            if (is_object($screen) && 'event' === $screen->post_type) {

                // Flatpickr assets
                wp_enqueue_script('flatpickr', plugins_url('/assets/vendor/flatpickr/dist/flatpickr.min.js', WISVCH_ASSET_BASE), [], null, true);
                wp_enqueue_style('flatpickr', plugins_url('/assets/vendor/flatpickr/dist/flatpickr.min.css', WISVCH_ASSET_BASE), [], null);

                // Custom Events metabox JS
                wp_enqueue_script('cpt-events-metabox', plugins_url('/assets/cpt/event/metabox.js', WISVCH_ASSET_BASE), ['jquery'], null, true);
            }
        }
    }

    /**
     * Register the metaboxes to be used for the team post type
     */
    public function meta_boxes()
    {
        add_meta_box('events_details', 'Events Details', [$this, 'render_meta_boxes'], 'event', 'normal', 'high');
    }

    /**
     * The HTML for the fields
     */
    function render_meta_boxes($post)
    {
        $meta = get_post_custom($post->ID);
        $short = ! isset($meta['_event_short_description'][0]) ? '' : $meta['_event_short_description'][0];
        $location = ! isset($meta['_event_location'][0]) ? '' : $meta['_event_location'][0];
        $start = ! isset($meta['_event_start_date'][0]) ? '' : $meta['_event_start_date'][0];
        $end = ! isset($meta['_event_end_date'][0]) ? '' : $meta['_event_end_date'][0];
        $cost = ! isset($meta['_event_cost'][0]) ? '' : $meta['_event_cost'][0];

        wp_nonce_field(basename(__FILE__), 'event_fields');

        ?>
        <p>
            <b><label for="_event_short_description">Short description</label></b>
            <input type="text" name="_event_short_description" class="widefat" id="_event_short_description"
                   value="<?=esc_attr($short);?>">
        </p>

        <p>
            <b><label for="_event_location">Location</label></b>
            <input type="text" name="_event_location" class="widefat" id="_event_location"
                   value="<?=esc_attr($location);?>">
        </p>

        <p class="flatpickr-hide">
            <b><label for="_event_start_date">Starting time</label></b>
            <input type="datetime-local" name="_event_start_date" class="widefat" id="_event_start_date"
                   value="<?=esc_attr($start);?>">
        </p>

        <p class="flatpickr-hide">
            <b><label for="_event_end_date">Ending time</label></b>
            <input type="datetime-local" name="_event_end_date" class="widefat" id="_event_end_date"
                   value="<?=esc_attr($end);?>">
        </p>

        <p id="_event_date_range_wrapper" style="display:none;">
            <b><label for="_event_end_date">Date &amp; time:</label></b>
            <input type="datetime-local" class="widefat" id="_event_date_range">
        </p>

        <p>
            <b><label for="_event_cost">Cost</label></b>
            <input type="text" name="_event_cost" class="widefat" id="_event_cost" value="<?=esc_attr($cost);?>">
        </p>

        <?php
    }

    /**
     * Save metaboxes.
     */
    function save_meta_boxes($post_id)
    {

        global $post;

        // Verify nonce
        if (! isset($_POST['event_fields']) || ! wp_verify_nonce($_POST['event_fields'], basename(__FILE__))) {
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

        $meta['_event_short_description'] = (isset($_POST['_event_short_description']) ? sanitize_text_field($_POST['_event_short_description']) : '');
        $meta['_event_location'] = (isset($_POST['_event_location']) ? sanitize_text_field($_POST['_event_location']) : '');
        $meta['_event_start_date'] = (isset($_POST['_event_start_date']) ? sanitize_text_field($_POST['_event_start_date']) : '');
        $meta['_event_end_date'] = (isset($_POST['_event_end_date']) ? sanitize_text_field($_POST['_event_end_date']) : '');
        $meta['_event_cost'] = (isset($_POST['_event_cost']) ? sanitize_text_field($_POST['_event_cost']) : '');

        foreach ($meta as $key => $value) {
            update_post_meta($post->ID, $key, $value);
        }
    }
}
