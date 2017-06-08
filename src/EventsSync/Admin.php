<?php

namespace WISVCH\EventsSync;

use WISVCH\WISVCH_Plugin;

/**
 * CH Events Sync administration page.
 *
 * @package WISVCH\EventsSync
 */
class Admin
{
    /**
     * Internal page slug for the settings page.
     */
    static $page_slug = 'edit.php?post_type=event';

    /**
     * Hook into WordPress.
     */
    static function register_hooks()
    {

        add_action('admin_init', [__CLASS__, 'init_settings']);
        add_action('admin_menu', [__CLASS__, 'admin_menu']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'admin_scripts']);
    }

    /**
     * Load scripts.
     *
     * @param $hook
     */
    static function admin_scripts($hook)
    {

        if ($hook !== 'event_page_events-sync') {
            return;
        }

        wp_enqueue_script('events-sync-js', plugins_url('assets/EventsSync/events-sync.js', WISVCH_ASSET_BASE), ['jquery']);
    }

    /**
     * Set-up custom Events Sync settings group.
     */
    static function init_settings()
    {
        register_setting('wisvch-events-sync', 'wisvch_events_sync', [__CLASS__, 'validate_input']);

        // Add settings section to admin page
        add_settings_section('wisvch_events_sync-connect', 'Connection Settings', '__return_false', 'wisvch-events-sync');

        // Get stored data
        $data = get_option('wisvch_events_sync');

        // Settings field: CH Events URL
        add_settings_field('wisvch_events_sync-ch_connect_url', 'CH Events URL', [__CLASS__, 'cb_ch_events_url'], 'wisvch-events-sync', 'wisvch_events_sync-connect', [
            'label_for' => 'wisvch_events_sync-ch_connect_url',
            'value' => $data['wisvch_events_sync-ch_connect_url'] ?? '',
        ]);

        // Settings field: CH Events Sync Interval
        add_settings_field('wisvch_events_sync-interval', 'Full sync interval', [__CLASS__, 'cb_interval'], 'wisvch-events-sync', 'wisvch_events_sync-connect', [
            'label_for' => 'wisvch_events_sync-interval',
            'value' => $data['wisvch_events_sync-interval'] ?? '1440',
        ]);
    }

    /**
     * Add settings page to menu.
     */
    static function admin_menu()
    {

        add_submenu_page(static::$page_slug, 'Events Synchronizer', 'Sync', 'manage_options', 'events-sync', [__CLASS__, 'settings_page']);
    }

    /**
     * Render settings page.
     */
    static function settings_page()
    {
        ?>

        <div class="wrap" id="eventsSync">

            <h1>Events Synchronization</h1>
            <?php settings_errors(); ?>

            <hr>

            <form method="post" action="options.php">

                <?php
                // Create hidden fields
                settings_fields('wisvch-events-sync');

                // Display settings sections
                do_settings_sections('wisvch-events-sync');

                // Render submit button
                submit_button();
                ?>

            </form>

            <hr>

        </div>

        <?php
    }

    /**
     * Callback function for setting: CH Events URL.
     *
     * @param $args
     */
    static function cb_ch_events_url($args)
    {
        ?>
        <input type="text"
               class="regular-text"
               id="wisvch_events_sync-ch_connect_url"
               name="wisvch_events_sync[wisvch_events_sync-ch_connect_url]"
               value="<?php echo esc_url($args['value']); ?>">

        <button type="button" id="checkConnection" class="button button-secondary"><span class="label">Test</span><span class="spinner" style="display:none;"></span></button>
        <?php
    }

    /**
     * Callback function for setting: Sync Interval.
     *
     * @param $args
     */
    static function cb_interval($args)
    {
        ?>
        <input type="text"
               id="wisvch_events_sync-interval"
               name="wisvch_events_sync[wisvch_events_sync-interval]"
               size="3"
               value="<?php echo intval($args['value']); ?>"> minutes
        <?php
    }

    /**
     * Validate settings inputs.
     *
     * @param $input
     * @return mixed|void
     */
    static function validate_input($input)
    {

        $output = [];

        // Iterate over inputs
        foreach ($input as $key => $value) {

            // Check if not empty
            if (isset($input[$key])) {

                switch ($key) {
                    case 'wisvch_events_sync-ch_connect_url':
                        $output[$key] = esc_url_raw($input[$key], ['http', 'https']);
                        break;
                    default:
                        $output[$key] = sanitize_text_field($input[$key]);
                }
            }
        }

        return $output;
    }
}
