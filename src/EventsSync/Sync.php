<?php

namespace WISVCH\EventsSync;

use function json_decode;
use function json_encode;
use WP_REST_Request;

/**
 * Add, update or delete CH Events.
 *
 * @package WISVCH\EventsSync
 */
class Sync
{

    /**
     * Initialize plugin.
     */
    function __construct()
    {
    }

    /**
     * Add or update an Event.
     *
     * @param WP_REST_Request $request
     *
     * @return bool True on success, false on error.
     */
    static function handle_webhook_call(WP_REST_Request $request)
    {
        $jsonBody = $request->get_json_params();
        $trigger = $jsonBody['trigger'];

        if ($trigger === "EVENT_CREATE") {
            self::create_event($jsonBody);
        } else if ($trigger === "EVENT_UPDATE") {
            print "update event";
        } else {
            throw new WISVCHException("");
        }

        // Fetch data
        // TODO: fetch data

        // Check if event exists (based on custom field _ch_events_id)
        // TODO: check if event exists

        // If not exists, add
        // TODO: add

        // else, update
        // TODO: update

        return true;
    }

    private static function create_event(array $parsedJsonBody)
    {
        $title = $parsedJsonBody['title'];
    }

    private static function update_event()
    {
    }

    private static function delete_event()
    {

    }

    /**
     * Delete an Event.
     *
     * @param $id CH Events eventID.
     * @return bool True on success, false on error.
     */
    function delete_single($id)
    {

        // Validate
        if (! is_numeric($id)) {
            return false;
        }

        $id = intval($id);

        // Check if event exists
        // TODO Check if event exists

        // Delete event
        // TODO Delete event

        return true;
    }
}
