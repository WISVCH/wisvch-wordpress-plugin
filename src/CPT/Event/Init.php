<?php

namespace WISVCH\CPT\Event;

/**
 * Registration of CPT and related taxonomies.
 *
 * @package WISVCH\CPT\Event
 */
class Init
{
    protected $registration_handler;

    /**
     * Initialize the plugin by setting localization and new site activation hooks.
     */
    public function __construct()
    {

        // Instantiate registration class, so we can add it as a dependency to main plugin class.
        $registration = new Registration;

        // Register callback that is fired when the plugin is activated.
        register_activation_hook(__FILE__, [__CLASS__, 'activate']);

        // Initialize registrations for post-activation requests.
        $registration->init();

        // Initialize metaboxes
        $metaboxes = new Metabox;
        $metaboxes->init();

        if (is_admin()) {
            $admin = new Admin($registration);
            $admin->init();
        }

        // Initialize API
        Api::register_hooks();
    }

    /**
     * Fired for each blog when the plugin is activated.
     */
    public function activate()
    {
        $this->registration_handler->register();
    }
}