<?php

namespace WISVCH\EventsSync;

/**
 * CH Events API routes.
 *
 * @package WISVCH\EventsSync
 */
class Routes
{
    /**
     * Hook into Wordprss.
     */
    static function register_hooks()
    {

        // Add endpoints
        add_action('rest_api_init', [__CLASS__, 'add_endpoints']);
    }

    /**
     * Add custom WP REST API endpoints.
     */
    static function add_endpoints()
    {

        register_rest_route('events-sync/v1', '/add/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => ['my_awesome_func'],
        ]);
    }
}
