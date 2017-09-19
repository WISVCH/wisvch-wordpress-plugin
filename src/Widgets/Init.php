<?php

namespace WISVCH\Widgets;

/**
 * Initialize custom widgets.
 *
 * @package WISVCH\Widgets
 */
class Init
{
    /**
     * Initialize widgets.
     */
    function __construct()
    {
        add_action('admin_enqueue_scripts', [__CLASS__, 'register_scripts']);
        add_action('widgets_init', [__CLASS__, 'register_widgets']);
    }

    /**
     * Register widgets with WordPress.
     */
    static function register_widgets()
    {

        register_widget(Banner::class);
    }

    static function register_scripts()
    {
        wp_register_script('w3cie-banner-widget', plugins_url('/assets/widgets/banner-widget.js', WISVCH_ASSET_BASE), ['media-widgets'], false, true);
    }
}
