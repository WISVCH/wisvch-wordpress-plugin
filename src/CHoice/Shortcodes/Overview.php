<?php

namespace WISVCH\CHoice\Shortcodes;

use WISVCH\CHoice\Template;

/**
 * Portal overview page.
 *
 * @package WISVCH\Portal\Shortcodes
 */
class Overview extends Template
{
    const TEMPLATE_NAME = 'overview.php';

    /**
     * Prepare data for use in the template.
     *
     * @return array
     */
    static function getTemplateData()
    {

        $return_data = parent::getTemplateData();

        // Return data
        $return_data['greeting'] = self::_greeting();

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
