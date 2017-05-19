<?php

namespace WISVCH\Portal;

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register WISVCH Portal shortcodes.
 */
class Shortcodes
{
    /**
     * Init shortcodes.
     */
    public static function init()
    {
        $shortcodes = [
            'wisvch_portal_overview' => __CLASS__.'::overview',
            'wisvch_portal_edit_profile' => __CLASS__.'::edit_profile',
            'wisvch_portal_logout' => __CLASS__.'::logout',
        ];

        foreach ($shortcodes as $shortcode => $function) {
            add_shortcode($shortcode, $function);
        }
    }

    /**
     * Shortcode wrapper.
     *
     * @param $function Shortcode callback function.
     * @param array $atts Shortcode attributes.
     * @param array $wrapper Shortcode wrapper settings.
     * @return string
     */
    public static function shortcode_wrapper(
        $function,
        $atts = [],
        $wrapper = [
            'class' => 'wisvch-portal',
            'before' => null,
            'after' => null,
        ]
    ) {
        ob_start();

        echo empty($wrapper['before']) ? '<div class="'.esc_attr($wrapper['class']).'">' : $wrapper['before'];
        call_user_func($function, $atts);
        echo empty($wrapper['after']) ? '</div>' : $wrapper['after'];

        return ob_get_clean();
    }

    /**
     * Portal overview shortcode.
     *
     * @return string
     */
    public static function overview()
    {
        return self::shortcode_wrapper([Shortcodes\Overview::class, 'output']);
    }

    /**
     * Edit profile page shortcode.
     *
     * @return string
     */
    public static function edit_profile()
    {
        return self::shortcode_wrapper([Shortcodes\Profile::class, 'output']);
    }

    /**
     * Logout page shortcode.
     *
     * @return string
     */
    public static function logout()
    {
        return self::shortcode_wrapper([Shortcodes\Logout::class, 'output']);
    }

    /**
     * Check if current user is logged in. If not, show login page.
     *
     * @TODO implement this
     */
    public static function check_auth()
    {

        if (is_user_logged_in()) {
            return true;
        }

        return false;
    }

    /**
     * Get shortcode template.
     *
     * @param string $template_name
     * @param array $args (default: array())
     */
    static function get_template($template_name, $args = [], $auth_required = false)
    {

        // Load login form if auth required and not logged in
        if ($auth_required && ! self::check_auth()) {

            // Set notice
            $notice = "<h5>Authorization required</h5><p>You need to log in to view this content.</p>";

            include(self::locate_template('login.php'));

            return;
        }

        if (! empty($args) && is_array($args)) {
            extract($args);
        }

        // Display notices (if any)
        Shortcodes::notices($args);

        // Include template
        include(self::locate_template($template_name));
    }

    /**
     * Locate a template and return the path for inclusion.
     *
     * Load order:
     *   theme  /  parts  /  portal  /  $template_name
     *   PLUGIN_DIR  /  templates  /  $template_name
     *
     * @param string $template_name
     * @return string
     */
    static function locate_template($template_name)
    {

        $template_path = 'parts/portal/';
        $default_path = plugin_dir_path(__FILE__).'/templates/';

        // Look within passed path within the theme - this is priority.
        $template = locate_template($template_path.$template_name);

        // Get default template
        if (! $template) {
            $template = $default_path.$template_name;
        }

        // Return what we found.
        return $template;
    }

    static function notices($args)
    {

        if (isset($args['notice'])) {
            $notice = $args['notice'];
            include(self::locate_template("notice.php"));
        }
    }
}
