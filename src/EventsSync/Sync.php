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
     * Add new Event.
     *
     * @param $id CH Events ID.
     */
    function add($id)
    {

        if (! is_numeric($id)) {
            return false;
        }

        $id = intval($id);
    }

    /**
     * Update Event.
     *
     * @param $id CH Events ID.
     */
    function update($id)
    {

        if (! is_numeric($id)) {
            return false;
        }

        $id = intval($id);
    }
}
