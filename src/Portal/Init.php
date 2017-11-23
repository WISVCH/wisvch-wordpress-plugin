<?php

namespace WISVCH\Portal;

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
        //@TODO: make dynamic or create setting for this
        self::$user_portal_url = site_url('portal');

        // Init User, Shortcode
        Member::register_hooks();
        Shortcodes::init();

        // CH Connect Branding
        self::chConnectBranding();

        // Change e-mail sender
        self::setEmailSender();
    }

    /**
     * Get URL to user portal.
     *
     * @return string|void User portal URL
     */
    static function getUrl()
    {
        return self::$user_portal_url ? self::$user_portal_url : site_url("/");
    }

    /**
     * Replace OpenID with CH Connect throughout the website.
     */
    static function chConnectBranding()
    {

        // Change login button text
        add_filter("openid-connect-generic-login-button-text", function () {
            return "Login with CH Connect";
        });
    }

    /**
     * Change e-mail sender to sitename.
     */
    static function setEmailSender()
    {

        // Change login button text
        add_filter("wp_mail_from_name", function () {
            return get_bloginfo('name');
        });
    }
}
