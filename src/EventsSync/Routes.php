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
        register_rest_route('events-sync/v1',
            '/single/',
            [
                'methods'  => 'POST',
                'callback' => [Sync::class, 'handle_webhook_call'],
                'permission_callback' => [__CLASS__, 'privileged_permission_callback'],
            ]);
    }

	static function privileged_permission_callback() {
		return current_user_can( 'edit_pages' );
	}
}
