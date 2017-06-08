<?php

namespace WISVCH\EventsSync;

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
     * @param $id CH Events eventID.
     * @return bool True on success, false on error.
     */
    function add_single($id)
    {

        // Validate
        if (! is_numeric($id)) {
            return false;
        }

        $id = intval($id);

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
