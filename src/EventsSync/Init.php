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
    }
}
