<?php

namespace WISVCH\Portal\Shortcodes;

use WISVCH\Portal\Member;
use WISVCH\Portal\Template;

/**
 * Portal edit profile page.
 *
 * @TODO class is a mess, refactor and find a good way to store and access user data.
 *
 * @package WISVCH\Portal\Shortcodes
 */
class Profile extends Template
{
    const TEMPLATE_NAME = 'edit-profile.php';

    /**
     * Prepare data for use in the template.
     *
     * @return array
     */
    static function getTemplateData()
    {

        $return_data = parent::getTemplateData();

        $return_data['ch_member'] = Member::_is_ch_member_exclusively();

        // Get CH Connect data
        $return_data['ch_connect'] = Member::get_user_claim();

        // Process form if POST request
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $return_data['notice'] = self::_processForm($return_data['ch_connect']);
        }

        return $return_data;
    }

    /**
     * Get variable from store, escape for form use.
     *
     * @param $key Variable name.
     * @param $store Variable store.
     * @param bool $echo Whether or not to print result. (default: true)
     * @return string|void Esaped result, empty if key not found.
     */
    static function ch($key, $store, $echo = true)
    {

        $var = self::_getArrayValueDeep($key, $store);
        $var = esc_attr($var);

        if ($echo) {
            echo $var;
        } else {
            return $var;
        }
    }

    /**
     * Find value in multidimensional array.
     *
     * @param string|array $key Single- or multidimensional key to look for.
     * @param $store Data store.
     * @return mixed|string Empty string if key not found, else value at $key in $store.
     */
    private static function _getArrayValueDeep($key, $store)
    {

        // Store has to be an array
        if (! is_array($store)) {
            return "";
        }

        // If key is array but only contains one element, extract
        if (is_array($key) && count($key) == 1) {
            $key = reset($key);
        }

        // Check for non-multidimensional key
        if (! is_array($key)) {

            if (array_key_exists($key, $store)) {
                return $store[$key];
            }

            return "";
        } else { // Check for multidimensional key

            $k = array_shift($key);

            if (array_key_exists($k, $store) && is_array($store[$k])) {
                return self::_getArrayValueDeep($key, $store[$k]);
            }

            return "";
        }
    }

    /**
     * Process edit user form.
     *
     * @param $ch_connect CH Connect data
     * @return string Notice with error or success message.
     */
    private static function _processForm($ch_connect)
    {

        // Check nonce
        if (false === wp_verify_nonce($_POST["_wpnonce"], 'wisvch_portal_edit-profile')) {
            return "<h5>Error</h5><p>Could not update profile information.</p>";
        }

        $inputs = [
            'user_name' => 'Full Name',
            'user_given_name' => 'Given Name',
            'user_gender' => 'Gender',
            'user_birthdate' => 'Birth Date',
            'user_email' => 'Email Address',
            'user_phone' => 'Phone',
            'user_street_address' => 'Address',
            'user_zip' => 'Postal Code',
            'user_city' => 'City',
            'user_country' => 'Country',
            'user_studentno' => 'Student Number',
            'user_netid' => 'NetID',
        ];

        $updates = [];

        // Store updates
        foreach ($inputs as $field_name => $field_title) {

            if (array_key_exists($field_name, $_POST)) {
                $updates[$field_name] = [$field_title, sanitize_text_field($_POST[$field_name])];
            }
        }

        // Send email with updates
        if (count($updates) > 0) {

            $email_contents = self::_buildEmail($updates, $ch_connect);

            // @TODO: replace svenh with w3cie
            $mail_sent = wp_mail(WP_DEBUG ? "svenh@ch.tudelft.nl" : "secretary@ch.tudelft.nl", "Verzoek wijziging ledenadministratie", $email_contents);

            if ($mail_sent) {
                return '<h5>Success</h5><p>Changes submitted successfully. The secretary will update your information as soon as possible.</p>';
            }
        } elseif (count($updates) === 0) {
            return "<h5>Warning</h5><p>There were no changes to submit.</p>";
        }

        return "<h5>Error</h5><p>Could not update profile information.</p>";
    }

    /**
     * Build email body for user data changes.
     *
     * @param $updates POST updates
     * @param $userdata CH Connect user claim
     * @return string
     */
    private static function _buildEmail($updates, $userdata)
    {

        $user = wp_get_current_user();

        $output = "Waarde secretaris,\n\n";
        $output .= "Er is via de website een verzoek om de ledenadministratie aan te passen ingediend.\n\n";
        $output .= "Datum: ".date_i18n('Y-m-d H:i:s')."\n";
        $output .= "Gebruiker: ".sanitize_text_field($user->user_login)." (".sanitize_text_field($userdata['sub']).")\n\n";
        $output .= "Wijzigingen:\n\n";

        $output .= "=====\n\n";

        foreach ($updates as $key => $update) {
            $output .= sanitize_text_field($update[0]).': '.sanitize_text_field($update[1])."\n";
        }

        $output .= "\n=====\n\n";

        $output .= "Groetjes,\nW3Cie";

        return $output;
    }
}
