<?php

namespace WISVCH\EventsSync;

/**
 * CH Events synchronizer.
 *
 * @package WISVCH\EventsSync
 */
class Init
{
    /**
     * Initialize plugin.
     */
    function __construct()
    {

        // Register endpoints
        Routes::register_hooks();

        if (is_admin()) {

            // Load administration pages
            Admin::register_hooks();
        }
    }
}
