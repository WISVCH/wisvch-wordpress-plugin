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

        // TODO Register endpoints
        //

        if (is_admin()) {

            // Load administration pages
            Admin::register_hooks();
        }
    }
}
