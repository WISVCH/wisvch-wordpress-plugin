<?php

namespace WISVCH\EventsSync;

use WP_REST_Request;
use function vsprintf;
use function wp_delete_post;
use function wp_update_post;

/**
 * Add, update or delete CH Events.
 *
 * @package WISVCH\EventsSync
 */
class Sync
{

    const EXCEPTION_TRIGGER_NOT_EXISTS         = 'Handle for trigger %s does not exists';
    const EXCEPTION_EVENT_ALREADY_EXISTS       = 'Event already exists and should not be created again';
    const EXCEPTION_MULTIPLE_SAME_EVENT_EXISTS = 'Number of event posts count is above 1.';

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
        $json_body = $request->get_json_params();
        $trigger = $json_body['trigger'];

        try {
            switch ($trigger) {
                case 'EVENT_CREATE_UPDATE':
                    self::determine_create_or_update($json_body);
                    break;
                case 'EVENT_DELETE':
                    self::delete_event($json_body);
                    break;
                default:
                    throw new WISVCHException(vsprintf(self::EXCEPTION_TRIGGER_NOT_EXISTS, [$trigger]));
            }
        } catch (WISVCHException $exception) {
            // TODO: notify somebody that something is wrong with the

            return false;
        }

        return true;
    }

    /**
     * Function create_event
     *
     * @param array $parsed_json_body
     *
     * @return void
     */
    static function create_event(array $parsed_json_body)
    {
        $post_args = self::generate_post_args($parsed_json_body);
        $post_id = wp_insert_post($post_args);

        add_post_meta($post_id, '_ch_events_key', $parsed_json_body['key']);
        add_post_meta($post_id, '_event_short_description', $parsed_json_body['short_description']);
        add_post_meta($post_id, '_event_location', $parsed_json_body['location']);
        add_post_meta($post_id, '_event_start_date', $parsed_json_body['event_start']);
        add_post_meta($post_id, '_event_end_date', $parsed_json_body['event_end']);

        add_post_meta($post_id, '_event_cost', 0); // TODO: change to real value.

        // TODO: set event image.
    }

    /**
     * Function assert_should_create_event
     *
     * @param array $parsed_json_body
     *
     * @return bool
     */
    static function should_create_event(array $parsed_json_body): bool
    {
        return is_null(self::get_event_by_events_key($parsed_json_body['key']));
    }

    /**
     * Function get_event_by_events_key
     *
     * @param string $ch_events_key
     *
     * @return \WP_Post|null
     */
    static function get_event_by_events_key(string $ch_events_key)
    {
        $posts = get_posts([
            'meta_key'    => '_ch_events_key',
            'meta_value'  => $ch_events_key,
            'post_type'   => 'event',
            'post_status' => 'any',
        ]);

        if (count($posts) > 1) {
            throw new WISVCHException(self::EXCEPTION_MULTIPLE_SAME_EVENT_EXISTS);
        } else if (count($posts) === 0) {
            return null;
        } else {
            return $posts[0];
        }
    }

    /**
     * Function determine_create_or_update
     *
     * @param $json_body
     *
     * @return void
     */
    static function determine_create_or_update($json_body): void
    {
        if (self::should_create_event($json_body)) {
            self::create_event($json_body);
        } else {
            self::update_event($json_body);
        }
    }

    /**
     * Function update_event
     *
     * @param array $parsed_json_body
     *
     * @return void
     */
    static function update_event(array $parsed_json_body)
    {
        $ch_events_key = $parsed_json_body['key'];
        $post = self::get_event_by_events_key($ch_events_key);
        $post_id = $post->ID;

        $post_args = self::generate_post_args($parsed_json_body);
        $post_args['ID'] = $post_id;
        wp_update_post($post_args);

        update_post_meta($post_id, '_event_short_description', $parsed_json_body['short_description']);
        update_post_meta($post_id, '_event_location', $parsed_json_body['location']);
        update_post_meta($post_id, '_event_start_date', $parsed_json_body['event_start']);
        update_post_meta($post_id, '_event_end_date', $parsed_json_body['event_end']);

        update_post_meta($post_id, '_event_cost', 0); // TODO: change to real value.
    }

    /**
     * Function delete_event
     *
     * @param array $parsed_json_body
     *
     * @return void
     */
    static function delete_event(array $parsed_json_body)
    {
        $ch_events_key = $parsed_json_body['key'];
        $post = self::get_event_by_events_key($ch_events_key);

        wp_delete_post($post->ID, true);
    }

    /**
     * Function generate_post_args
     *
     * @param array $parsed_json_body
     *
     * @return array
     */
    static function generate_post_args(array $parsed_json_body): array
    {
        $post_args = [
            'post_title'   => $parsed_json_body['title'],
            'post_type'    => 'event',
            'post_status'  => 'publish',
            'post_content' => $parsed_json_body['description'],
        ];

        return $post_args;
    }
}
