<?php

namespace WISVCH;

/**
 * @package   WISVCH
 * @author    W3Cie <w3cie@ch.tudelft.nl>
 *
 * @wordpress-plugin
 * Plugin Name:       WISVCH WordPress Enhancements
 * Description:       Adds custom post types, widgets and user portal.
 * Version:           1.0.0
 * Author:            W3Cie
 * Author URI:        W3Cie <w3cie@ch.tudelft.nl>
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

// Define base path
define('WISVCH_ASSET_BASE', __FILE__);

// Load autoloader
require "autoloader.php";

/**
 * Initializes WordPress plug-in.
 *
 * @package WISVCH
 */
class WISVCH_Plugin
{
    // Dashboard Glancer, adds post counts to dashboard
    static protected $dashboard_glancer;

    /**
     * Init subcomponents.
     */
    function __construct()
    {

        // Init Dashboard Glancer (to load admin css)
        WISVCH_Plugin::dashboard_glancer();

        // Init custom post types
        new CPT\Board\Init();
        new CPT\Committee\Init();
        new CPT\Company\Init();
        new CPT\Event\Init();
        new CPT\HonoraryMember\Init();
        new CPT\JobOpening\Init();
        new CPT\Slide\Init();

        // Update existing post types
        new CPT\News\Init();

        // Init Portal
        new Portal\Init();

        // Init Events Synchronizer
        new EventsSync\Init();

        // Init shortcodes
        new Shortcodes\Init();

        // Add separator to admin menu
        add_action('admin_init', [$this, 'add_admin_menu_separator']);
    }

    /**
     * Add separator to create a dedicated block in the admin menu.
     */
    function add_admin_menu_separator()
    {
        global $menu;
        $position = 39;

        // Do not overwrite existing menu items
        while (! empty($menu[$position])) {
            $position++;
        }

        $menu[$position] = [
            0 => '',                            // The text of the menu item
            1 => 'read',                        // Permission level required to view the item
            2 => 'separator'.$position,       // The ID of the menu item
            3 => '',                            // Empty by default.
            4 => 'wp-menu-separator'            // Custom class names for the menu item
        ];
        ksort($menu);
    }

    /**
     * Make Dashboard Glancer class statically available.
     *
     * @return \Gamajo_Dashboard_Glancer
     */
    static function dashboard_glancer()
    {

        if (isset(self::$dashboard_glancer)) {
            return self::$dashboard_glancer;
        } else {
            if (! class_exists(\Gamajo_Dashboard_Glancer::class)) {
                require plugin_dir_path(__FILE__).'lib/class-gamajo-dashboard-glancer.php';  // WP 3.8
            }

            return self::$dashboard_glancer = new \Gamajo_Dashboard_Glancer();
        }
    }

    /**
     * Code executed upon plugin activation.
     */
    static function activate()
    {

        // Create custom roles.
        if (! \WISVCH\Portal\User::register_role()) {
            wp_die("Could not register custom CH Member role.", "Fatal error", ['back_link' => true]);
        }

        // Update permalinks
        flush_rewrite_rules();
    }

    /**
     * Code executed upon plugin deactivation.
     */
    static function deactivate()
    {

        // Update permalinks
        flush_rewrite_rules();
        // Don't remove roles here, because existing members would be converted to another role.

    }
}

new WISVCH_Plugin();
