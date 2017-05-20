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

        // Setup SMTP (@todo: remove, is temporary, for testing purposes only)
        add_action('phpmailer_init', [__CLASS__, 'setUpSMTP']);
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
     * @TODO REMOVE
     */
    static function setUpSMTP(\PHPMailer $phpmailer)
    {

        if (defined('PHPMAILER_HOST')) {

            $phpmailer->Host = PHPMAILER_HOST;
            $phpmailer->Port = PHPMAILER_PORT; // could be different
            $phpmailer->Username = PHPMAILER_USER; // if required
            $phpmailer->Password = PHPMAILER_PWD; // if required
            $phpmailer->SMTPAuth = true; // if required
            $phpmailer->SMTPSecure = 'tls'; // enable if required, 'tls' is another possible value
            $phpmailer->setFrom('w3cie@ch.tudelft.nl', 'ch.tudelft.nl');

            $phpmailer->IsSMTP();
        }
    }
}
