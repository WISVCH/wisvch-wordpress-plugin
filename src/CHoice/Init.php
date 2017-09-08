<?php

namespace WISVCH\CHoice;

/**
 * W.I.S.V. 'Christiaan Huygens' members' portal.
 *
 * @package WISVCH\Portal
 */
class Init
{
    static $user_portal_url;

    /**
     * Initialize user portal.
     */
    function __construct()
    {
        // Set user portal URL
        self::$user_portal_url = site_url('portal');

        // Init User, Shortcode
        Member::register_hooks();
        Shortcodes::init();
    }
}
