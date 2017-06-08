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
     * Initialize routes
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

        register_rest_route('events-sync/v1', '/single/(?P<id>\d+)', [
            'methods' => 'POST',
            'callback' => [Sync::class, 'add_single'],
        ]);

        register_rest_route('events-sync/v1', '/single/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [Sync::class, 'delete_single'],
        ]);
    }
}
