<?php

namespace WISVCH\Widgets;

/**
 * Banner widget.
 */
class Banner extends \WP_Widget_Media
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('w3cie_banner', 'Banner', [
            'description' => __('Displays an banner.'),
            'mime_type' => 'image',
        ]);

        $this->l10n = array_merge($this->l10n, [
            'no_media_selected' => __('No image selected'),
            'add_media' => _x('Add Image', 'label for button in the image widget'),
            'replace_media' => _x('Replace Image', 'label for button in the image widget; should preferably not be longer than ~13 characters long'),
            'edit_media' => _x('Edit Image', 'label for button in the image widget; should preferably not be longer than ~13 characters long'),
            'missing_attachment' => sprintf(/* translators: placeholder is URL to media library */
                __('We can&#8217;t find that image. Check your <a href="%s">media library</a> and make sure it wasn&#8217;t deleted.'), esc_url(admin_url('upload.php'))),
            /* translators: %d is widget count */
            'media_library_state_multi' => _n_noop('Image Widget (%d)', 'Image Widget (%d)'),
            'media_library_state_single' => __('Image Widget'),
        ]);
    }

    /**
     * Get schema for properties of a widget instance (item).
     *
     * @return array Schema for properties.
     */
    public function get_instance_schema()
    {
        return array_merge(parent::get_instance_schema(), [
            'size' => [
                'type' => 'string',
                'enum' => array_merge(['full', 'custom']),
                'default' => 'full',
                'description' => __('Size'),
            ],
            'width' => [ // Via 'customWidth', only when size=custom; otherwise via 'width'.
                'type' => 'integer',
                'minimum' => 837,
                'default' => 837,
                'description' => __('Width'),
            ],
            'height' => [ // Via 'customHeight', only when size=custom; otherwise via 'height'.
                'type' => 'integer',
                'minimum' => 170,
                'default' => 170,
                'description' => __('Height'),
            ],
            'link_type' => [
                'type' => 'string',
                'enum' => ['custom'],
                'default' => 'none',
                'media_prop' => 'link',
                'description' => __('Link To'),
                'should_preview_update' => false,
            ],
            'link_url' => [
                'type' => 'string',
                'default' => '',
                'format' => 'uri',
                'media_prop' => 'linkUrl',
                'description' => __('URL'),
                'should_preview_update' => false,
            ],
            'link_target_blank' => [
                'type' => 'boolean',
                'default' => true,
                'media_prop' => 'linkTargetBlank',
                'description' => __('Open link in a new tab'),
                'should_preview_update' => false,
            ],
        ]);
    }

    /**
     * Displays the widget on the front-end.
     *
     * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
     * @param array $instance Saved setting from the database.
     */
    public function widget($args, $instance)
    {
        $instance = wp_parse_args($instance, wp_list_pluck($this->get_instance_schema(), 'default'));

        // Short-circuit if no media is selected.
        if (! $this->has_content($instance)) {
            return;
        }

        echo $args['before_widget'];

        /**
         * Filters the media widget instance prior to rendering the media.
         */
        $instance = apply_filters("widget_{$this->id_base}_instance", $instance, $args, $this);

        $this->render_media($instance);

        echo $args['after_widget'];
    }

    /**
     * Render the media on the frontend.
     *
     * @param array $instance Widget instance props.
     * @return void
     */
    public function render_media($instance)
    {
        $instance = array_merge(wp_list_pluck($this->get_instance_schema(), 'default'), $instance);

        $attachment = null;

        // Check if attachment is set and has required image mime type
        if ($this->is_attachment_with_mime_type($instance['attachment_id'], $this->widget_options['mime_type'])) {
            $attachment = get_post($instance['attachment_id']);
        }

        if ($attachment) {

            $size = $instance['size'];
            if ('custom' === $size || ! in_array($size, array_merge(get_intermediate_image_sizes(), ['full']), true)) {
                $size = [$instance['width'], $instance['height']];
            }

            // Get image
            $image = wp_get_attachment_image_url($attachment->ID, $size);
        } else {

            if (! $instance['url']) {
                return;
            }

            $image = $instance['url'];
        }

        $url = '';
        if ('custom' === $instance['link_type'] && ! empty($instance['link_url'])) {
            $url = $instance['link_url'];
        }

        $image_style = $image ? sprintf('style="background-image:url(%1$s)"', esc_url($image)) : '';
        $class = 'banner-container';

        if ($url) {
            $html = sprintf('<a href="%1$s" class="%2$s" rel="%3$s" target="%4$s" %5$s>%6$s</a>', esc_url($url), esc_attr($class), esc_attr($instance['link_rel']), ! empty($instance['link_target_blank']) ? '_blank' : '', $image_style, esc_html($instance['title']));
        } else {
            $html = sprintf('<div class="%1$s" %2$s>%3$s</div>', esc_attr($class), $image_style, esc_html($instance['title']));
        }

        echo $html;
    }

    /**
     * Loads the required media files for the media manager and scripts for media widgets.
     */
    public function enqueue_admin_scripts()
    {
        parent::enqueue_admin_scripts();

        $handle = 'w3cie-banner-widget';
        wp_enqueue_script($handle);

        $exported_schema = [];
        foreach ($this->get_instance_schema() as $field => $field_schema) {
            $exported_schema[$field] = wp_array_slice_assoc($field_schema, ['type', 'default', 'enum', 'minimum', 'format', 'media_prop', 'should_preview_update']);
        }
        wp_add_inline_script($handle, sprintf('wp.mediaWidgets.modelConstructors[ %s ].prototype.schema = %s;', wp_json_encode($this->id_base), wp_json_encode($exported_schema)));

        wp_add_inline_script($handle, sprintf('
					wp.mediaWidgets.controlConstructors[ %1$s ].prototype.mime_type = %2$s;
					wp.mediaWidgets.controlConstructors[ %1$s ].prototype.l10n = _.extend( {}, wp.mediaWidgets.controlConstructors[ %1$s ].prototype.l10n, %3$s );
		', wp_json_encode($this->id_base), wp_json_encode($this->widget_options['mime_type']), wp_json_encode($this->l10n)));
    }

    /**
     * Render form template scripts.
     */
    public function render_control_template_scripts()
    {
        parent::render_control_template_scripts();

        ?>
        <script type="text/html" id="tmpl-wp-media-widget-image-preview">
            <#
                    var describedById = 'describedBy-' + String( Math.random() );
                    #>
                <# if ( data.error && 'missing_attachment' === data.error ) { #>
                    <div class="notice notice-error notice-alt notice-missing-attachment">
                        <p><?php echo $this->l10n['missing_attachment']; ?></p>
                    </div>
                    <# } else if ( data.error ) { #>
                        <div class="notice notice-error notice-alt">
                            <p><?php _e('Unable to preview media due to an unknown error.'); ?></p>
                        </div>
                        <# } else if ( data.url ) { #>
                            <img class="attachment-thumb" src="{{ data.url }}" draggable="false" alt="{{ data.alt }}"
                            <# if ( ! data.alt && data.currentFilename ) { #> aria-describedby="{{ describedById }}"
                                <# } #> />
                                    <# if ( ! data.alt && data.currentFilename ) { #>
                                        <p class="hidden" id="{{ describedById }}"><?php
                                            /* translators: placeholder is image filename */
                                            echo sprintf(__('Current image: %s'), '{{ data.currentFilename }}');
                                            ?></p>
                                        <# } #>
                                            <# } #>
        </script>
        <?php
    }
}
