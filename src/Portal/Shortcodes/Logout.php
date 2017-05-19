<?php

namespace WISVCH\Portal\Shortcodes;

use WISVCH\Portal\Shortcodes;

/**
 * Portal edit profile page.
 *
 * @package WISVCH\Portal\Shortcodes
 */
class Logout
{
    /**
     * Render template.
     */
    static function output()
    {

        // Include template if redirect fails
        Shortcodes::get_template('logout.php');
    }
}
