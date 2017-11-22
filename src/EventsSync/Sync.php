<?php

namespace WISVCH\EventsSync;

use WP_REST_Request;

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
        self::auth_ch_events();

        $json_body = $request->get_json_params();
        $trigger = $json_body['trigger'];

        try {
            switch ($trigger) {
                case 'EVENT_CREATE_UPDATE':
                    self::determine_create_or_update_event($json_body);
                    break;
                case 'EVENT_DELETE':
                    self::delete_event($json_body);
                    break;
                case 'PRODUCT_CREATE_UPDATE':
                    self::determine_create_or_update_product($json_body);
                    break;
                case 'PRODUCT_DELETE':
                    self::delete_product($json_body);
                    break;
                default:
                    throw new WISVCHException(vsprintf(self::EXCEPTION_TRIGGER_NOT_EXISTS, [$trigger]));
            }
        } catch (WISVCHException $exception) {
            mail("w3cie@ch.tudelft.nl", "[ERROR] Events sync", $exception->getTraceAsString());

            return false;
        }

        return true;
    }

    /**
     * Function determine_create_or_update
     *
     * @param $json_body
     *
     * @return void
     */
    static function determine_create_or_update_event($json_body): void
    {
        if (self::should_create_event($json_body)) {
            self::create_event($json_body);
        } else {
            self::update_event($json_body);
        }
    }

    /**
     * Function assert_should_create_event
     *
     * @param array $product_array
     *
     * @return bool
     */
    static function should_create_event(array $product_array): bool
    {
        return is_null(self::get_event_by_events_key($product_array['key']));
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
        } elseif (count($posts) === 0) {
            return null;
        } else {
            return $posts[0];
        }
    }

    /**
     * Function create_event
     *
     * @param array $event_array
     *
     * @return void
     */
    static function create_event(array $event_array)
    {
        $post_args = self::generate_post_args_event($event_array);
        $post_id = wp_insert_post($post_args);
        self::set_event_categories($event_array, $post_id);

        add_post_meta($post_id, '_ch_events_key', $event_array['key']);
        add_post_meta($post_id, '_event_short_description', $event_array['short_description']);
        add_post_meta($post_id, '_event_location', $event_array['location']);
        add_post_meta($post_id, '_event_start_date', $event_array['event_start']);
        add_post_meta($post_id, '_event_end_date', $event_array['event_end']);

        $product_post_array = self::set_event_products($event_array);
        add_post_meta($post_id, '_event_product_post_array', $product_post_array);
    }

    /**
     * Function update_event
     *
     * @param array $event_array
     *
     * @return void
     */
    static function update_event(array $event_array)
    {
        $ch_events_key = $event_array['key'];
        $post = self::get_event_by_events_key($ch_events_key);
        $post_id = $post->ID;

        $post_args = self::generate_post_args_event($event_array);
        $post_args['ID'] = $post_id;
        wp_update_post($post_args);
        self::set_event_categories($event_array, $post_id);

        update_post_meta($post_id, '_event_short_description', $event_array['short_description']);
        update_post_meta($post_id, '_event_location', $event_array['location']);
        update_post_meta($post_id, '_event_start_date', $event_array['event_start']);
        update_post_meta($post_id, '_event_end_date', $event_array['event_end']);

        $product_post_array = self::set_event_products($event_array);
        update_post_meta($post_id, '_event_product_post_array', $product_post_array);
    }

    /**
     * Function delete_event
     *
     * @param array $event_array
     *
     * @return void
     */
    static function delete_event(array $event_array)
    {
        $ch_events_key = $event_array['key'];
        $post = self::get_event_by_events_key($ch_events_key);

        wp_delete_post($post->ID, true);
    }

    /**
     * Function generate_post_args
     *
     * @param array $event_array
     *
     * @return array
     */
    static function generate_post_args_event(array $event_array): array
    {
        $post_args = [
            'post_title'   => $event_array['title'],
            'post_type'    => 'event',
            'post_status'  => 'publish',
            'post_content' => $event_array['description'],
        ];

        return $post_args;
    }

    /**
     * Function determine_create_or_update
     *
     * @param $json_body
     *
     * @return int
     */
    static function determine_create_or_update_product($json_body): int
    {
        if (self::should_create_product($json_body)) {
            return self::create_product($json_body);
        } else {
            return self::update_product($json_body);
        }
    }

    /**
     * Function assert_should_create_event
     *
     * @param array $event_array
     *
     * @return bool
     */
    static function should_create_product(array $event_array): bool
    {
        return is_null(self::get_product_by_events_key($event_array['key']));
    }

    /**
     * Function create_product
     *
     * @param array $product_array
     *
     * @return int
     */
    static function create_product(array $product_array): int
    {
        $post_args = self::generate_product_post_args($product_array);
        $post_id = wp_insert_post($post_args);

        add_post_meta($post_id, '_ch_events_key', $product_array['key']);
        add_post_meta($post_id, '_product_cost', $product_array['price']);

        return $post_id;
    }

    /**
     * Function update_product
     *
     * @param array $product_array
     *
     * @return int
     */
    static function update_product(array $product_array): int
    {
        $ch_events_key = $product_array['key'];
        $post = self::get_product_by_events_key($ch_events_key);
        $post_id = $post->ID;

        $post_args = self::generate_product_post_args($product_array);
        $post_args['ID'] = $post_id;
        wp_update_post($post_args);
        update_post_meta($post_id, '_product_cost', $product_array['price']);

        return $post_id;
    }

    /**
     * Function delete_product
     *
     * @param array $product_array
     *
     * @return void
     */
    private static function delete_product(array $product_array): void
    {
        $ch_events_key = $product_array['key'];
        $post = self::get_product_by_events_key($ch_events_key);

        wp_delete_post($post->ID, true);
    }

    /**
     * Function get_event_by_events_key
     *
     * @param string $ch_product_key
     *
     * @return \WP_Post|null
     */
    static function get_product_by_events_key(string $ch_product_key)
    {
        $posts = get_posts([
            'meta_key'    => '_ch_events_key',
            'meta_value'  => $ch_product_key,
            'post_type'   => 'product',
            'post_status' => 'any',
        ]);

        if (count($posts) > 1) {
            throw new WISVCHException(self::EXCEPTION_MULTIPLE_SAME_EVENT_EXISTS);
        } elseif (count($posts) === 0) {
            return null;
        } else {
            return $posts[0];
        }
    }

    /**
     * Function generate_product_post_args
     *
     * @param array $product_array
     *
     * @return array
     */
    private static function generate_product_post_args(array $product_array): array
    {
        $post_args = [
            'post_title'   => $product_array['title'],
            'post_type'    => 'product',
            'post_status'  => 'publish',
            'post_content' => $product_array['description'],
        ];

        return $post_args;
    }

    /**
     * Function set_event_categories
     *
     * @param array $event_array
     * @param       $post_id
     *
     * @return void
     */
    private static function set_event_categories(array $event_array, $post_id): void
    {
        $term_array = [];
        foreach ($event_array['categories'] as $category) {
            $term_array[] = get_term_by('slug', strtolower($category), 'event_category')->term_id;
        }
        wp_set_post_terms($post_id, $term_array, 'event_category');
    }

    /**
     * Function set_event_products
     *
     * @param array $event_array
     *
     * @return array
     */
    private static function set_event_products(array $event_array): array
    {
        $product_post_array = [];
        foreach ($event_array['products'] as $product_array) {
            $product_post_array[] = self::determine_create_or_update_product($product_array);
        }

        return $product_post_array;
    }

    /**
     * Function auth_ch_events
     *
     * @return void
     */
    private static function auth_ch_events(): void
    {
        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        $user = wp_authenticate($username, $password);

        if (is_wp_error($user)) {
            throw new WISVCHException("Login attempt with username " . $username);
        } else {
            if (!user_can($user->ID, 'edit_pages')) {
                throw new WISVCHException("User " . $username . " has not enough rights to do this!");
            }
        }
    }
}
