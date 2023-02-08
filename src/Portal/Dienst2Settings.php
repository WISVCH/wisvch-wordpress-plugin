<?php

namespace WISVCH\Portal;

/**
 * Class Dienst2Settings is used to register the settings for the Dienst2 API.
 * @package WISVCH\Portal
 */
class Dienst2Settings
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'wisvch_options_page']);
        add_action('admin_init', [$this, 'wisvch_dienst2_settings_api_init']);
    }

    public function wisvch_options_page()
    {
        add_options_page(
            'WISVCH Settings',
            'WISVCH Settings',
            'manage_options',
            'wisvch',
            [$this, 'wisvch_options_page_html']
        );
    }

    public function wisvch_dienst2_settings_api_init()
    {
        add_settings_section(
            'wisvch_dienst2_settings_section',
            'Dienst2 API',
            [$this, 'wisvch_dienst2_settings_section_callback'],
            'wisvch'
        );

        add_settings_field(
            Dienst2::DIENST2_API_URL,
            'Dienst2 API URL',
            [$this, 'wisvch_dienst2_settings_field_callback'],
            'wisvch',
            'wisvch_dienst2_settings_section',
            [
                'label_for' => Dienst2::DIENST2_API_URL,
            ]
        );

        add_settings_field(
            Dienst2::DIENST2_API_TOKEN,
            'Dienst2 API Token',
            [$this, 'wisvch_dienst2_settings_field_callback'],
            'wisvch',
            'wisvch_dienst2_settings_section',
            [
                'label_for' => Dienst2::DIENST2_API_TOKEN,
            ]
        );

        register_setting('wisvch', Dienst2::DIENST2_API_URL);
        register_setting('wisvch', Dienst2::DIENST2_API_TOKEN);
    }

    /**
     * Callback for the settings section.
     */
    public function wisvch_dienst2_settings_section_callback()
    {
        echo '<p>Settings for the Dienst2 API.</p>';
    }

    /**
     * Callback for the settings field.
     *
     * @param array $args
     */
    public function wisvch_dienst2_settings_field_callback(array $args)
    {
        $option = get_option($args['label_for']);
        echo "<input name='{$args['label_for']}' id='{$args['label_for']}' type='text' value='{$option}' class='regular-text' />";
    }


    /**
     * Top level menu callback function
     */
    function wisvch_options_page_html()
    {
        // check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }

        // add error/update messages

        // check if the user have submitted the settings
        // WordPress will add the "settings-updated" $_GET parameter to the url
        if (isset($_GET['settings-updated'])) {
            // add settings saved message with the class of "updated"
            add_settings_error('wporg_messages', 'wporg_message', __('Settings Saved', 'wporg'), 'updated');
        }

        // show error/update messages
        settings_errors('wporg_messages');
?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                // output security fields for the registered setting "wporg"
                settings_fields('wisvch');
                // output setting sections and their fields
                // (sections are registered for "wporg", each field is registered to a specific section)
                do_settings_sections('wisvch');
                // output save settings button
                submit_button('Save Settings');
                ?>
            </form>
        </div>
<?php
    }
}
