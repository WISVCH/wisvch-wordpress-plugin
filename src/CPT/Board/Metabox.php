<?php

namespace WISVCH\CPT\Board;

/**
 * Register metaboxes.
 *
 * @package WISVCH\CPT\Board;
 */
class Metabox
{
    private $_meta;

    function __construct()
    {
        add_action('add_meta_boxes', [$this, 'meta_boxes']);
        add_action('save_post', [$this, 'save_meta_boxes'], 10, 2);
    }

    /**
     * Register the metaboxes to be used for the board post type
     */
    function meta_boxes()
    {
        add_meta_box('board_details', 'Board Details', [$this, 'render_board_details'], 'board', 'side', 'high');
    }

    /**
     * Board details metabox.
     */
    function render_board_details($post)
    {

        $meta = $this->_getMeta($post);
        $year = $this->_getMetaEntry($meta, '_board_year') ?? '';

        wp_nonce_field(basename(__FILE__), 'board_details');
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

        // Verify nonces
        if (! isset($_POST['board_details']) || ! wp_verify_nonce($_POST['board_details'], basename(__FILE__))) {
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

    /**
     * Retrieve and store post meta.
     *
     * @param WP_Post $post Post object.
     * @return array Metadata.
     */
    private function _getMeta($post)
    {

        if (! isset($this->_meta)) {
            $this->_meta = get_post_custom($post->ID);
        }

        return $this->_meta;;
    }

    /**
     * Get entry from post meta.
     *
     * @param array $meta Metadata object.
     * @param string $name Entry key.
     * @param bool $single Whether to return all values for key (array) or just the first.
     * @return mixed Meta value on success, false on failure.
     */
    private function _getMetaEntry($meta, $name, $single = true)
    {

        if ($single === true && isset($meta[$name][0])) {
            return $meta[$name][0];
        }

        if ($single !== true && isset($meta[$name])) {
            return $meta[$name];
        }

        return false;
    }
}
