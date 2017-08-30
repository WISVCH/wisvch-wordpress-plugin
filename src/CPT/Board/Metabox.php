<?php

namespace WISVCH\CPT\Board;

/**
 * Register metaboxes.
 *
 * @package WISVCH\CPT\Board;
 */
class Metabox
{
    public function init()
    {
        add_action('add_meta_boxes', [$this, 'meta_boxes']);
        add_action('save_post', [$this, 'save_meta_boxes'], 10, 2);
    }

    /**
     * Register the metaboxes to be used for the board post type
     */
    public function meta_boxes()
    {
        add_meta_box('board_details', 'Board Details', [$this, 'render_meta_boxes'], 'board', 'side', 'high');
    }

    /**
     * The HTML for the fields
     */
    function render_meta_boxes($post)
    {

        $meta = get_post_custom($post->ID);
        $year = ! isset($meta['_board_year'][0]) ? '' : $meta['_board_year'][0];

        wp_nonce_field(basename(__FILE__), 'board_fields');
        ?>

        <p>
            <b><label>Year</label></b><br>
            <input type="text" name="_board_year" class="widefat" placeholder="e.g. 2015 / 2016" value="<?php echo esc_attr($year); ?>">
        </p>


    <?php }

    /**
     * Save metaboxes
     */
    function save_meta_boxes($post_id)
    {

        global $post;

        // Verify nonce
        if (! isset($_POST['board_fields']) || ! wp_verify_nonce($_POST['board_fields'], basename(__FILE__))) {
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

        $meta['_board_year'] = (isset($_POST['_board_year']) ? wp_kses_post($_POST['_board_year']) : '');

        foreach ($meta as $key => $value) {
            update_post_meta($post->ID, $key, $value);
        }
    }
}
