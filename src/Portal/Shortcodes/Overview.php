<?php

namespace WISVCH\Portal\Shortcodes;

use WISVCH\Portal\Shortcodes;

/**
 * Portal overview page.
 *
 * @package WISVCH\Portal\Shortcodes
 */
class Overview
{
    /**
     * Render template.
     */
    static function output()
    {

        Shortcodes::get_template('overview.php', self::getTemplateData(), true);
    }

    /**
     * Prepare data for use in the template.
     *
     * @return array
     */
    static function getTemplateData()
    {

        // Get WordPress user object
        $user = wp_get_current_user();

        // @TODO import OpenID data here somewhere (or load from db)

        // Return data
        $return_data = [
            'user' => $user->data,
            'meta' => get_user_meta($user->ID),
            'greeting' => self::_greeting(),
        ];

        return $return_data;
    }

    private static function _greeting()
    {

        $hour = date('G');

        if ($hour >= 0 && $hour < 6) {
            $greeting = 'Good night';
        } elseif ($hour < 12) {
            $greeting = 'Good afternoon';
        } elseif ($hour < 18) {
            $greeting = 'Good afternoon';
        } else {
            $greeting = 'Good evening';
        }

        return $greeting;
    }
}
