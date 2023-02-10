<?php

namespace WISVCH\Portal\Shortcodes;

use WISVCH\Portal\Member;
use WISVCH\Portal\Shortcodes;
use WISVCH\Portal\Template;
use WISVCH\Portal\Dienst2;

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

        $return_data['ch_member'] = Member::_is_ch_member();

        // Get CH Connect data
        $return_data['ch_connect'] = Member::get_user_claim();

        // Process form if POST request and user is CH member
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post' && $return_data['ch_member']) {
            $return_data['notice'] = self::_processForm($return_data['ch_connect']);
        }

        if ($return_data['ch_member']) {
            // Get user data from Dienst2
            $dienst2 = new Dienst2();
            $return_data['ch_dienst2'] = $dienst2->getPerson();
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
        if (!is_array($store)) {
            return "";
        }

        // If key is array but only contains one element, extract
        if (is_array($key) && count($key) == 1) {
            $key = reset($key);
        }

        // Check for non-multidimensional key
        if (!is_array($key)) {

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

        // Check if logged in
        if (!Shortcodes::check_auth()) {
            return "<h5>Error</h5><p>You are not logged in.</p>";
        }
        $dienst2 = new Dienst2();
        $profile = $dienst2->getPerson();
        $allowedFields = Dienst2::DIENST2_ALLOWED_FIELDS;
        $checkboxFields = Dienst2::DIENST2_CHECKBOX_FIELDS;

        // Check which fields are updated
        $updates = [];

        foreach ($allowedFields as $field) {
            $userField = 'user_' . $field;
            // Always pass true or false for checkboxes
            if (in_array($field, $checkboxFields)) {
                if (array_key_exists($userField, $_POST)) {
                    $updates[$field] = true;
                } else {
                    $updates[$field] = false;
                }
            } else if (array_key_exists($userField, $_POST)) {
                // Check if field is changed
                if ($profile[$field] != $_POST[$userField]) {
                    $updates[$field] = sanitize_text_field($_POST[$userField]);
                }
            }
        }

        // Send email with updates
        if (count($updates) > 0) {
            try {
                $dienst2->updatePerson($updates);
            } catch (\Exception $e) {
                return "<h5>Error</h5><p>Could not update profile information.</p>";
            }

            $email_contents = self::_buildEmail($updates, $ch_connect);
            $mail_sent = wp_mail("secretary@ch.tudelft.nl", "[Website] Wijziging ledenadministratie", $email_contents);
            if ($mail_sent) {
                return '<h5>Success</h5><p>Changes submitted successfully. The secretary will review your changes and contact you if necessary.</p>';
            }

            return "<h5>Error</h5><p>Changes submitted successfully.</p>";
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

        $output = "Beste secretaris,\n\n";
        $output .= "Er is een nieuwe adreswijziging of gegevenswijziging via de website ingediend.\n\n";
        $output .= "Datum: " . date_i18n('Y-m-d H:i') . "\n";
        $output .= "Gebruiker: " . sanitize_text_field($user->user_login) . " (ID: " . sanitize_text_field($userdata['sub']) . ")\n\n";
        $output .= "Wijzigingen:\n";
        $output .= "-----------\n\n";

        foreach ($updates as $key => $update) {
            $output .= ucfirst($key) . ": " . sanitize_text_field($update) . "\n";
        }

        return $output;
    }
}
