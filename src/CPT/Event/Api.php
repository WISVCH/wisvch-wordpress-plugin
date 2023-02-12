<?php

namespace WISVCH\CPT\Event;

/**
 * Handle custom Event API requests.
 *
 * @package WISVCH\CPT\Event;
 */
class Api
{
    /**
     * @var array Custom meta keys belonging to Event.
     */
    private static $_meta_keys = [
        "_event_short_description",
        "_event_location",
        "_event_start_date",
        "_event_end_date",
        "_event_cost",
    ];

    /**
     * Hook into WordPress.
     */
    public static function register_hooks()
    {
        // Add fields
        add_action('rest_api_init', [__CLASS__, 'add_fields']);

        // Add routes
        add_action('rest_api_init', [__CLASS__, 'add_routes']);
    }

    /**
     * Add fields to Event API request.
     */
    static function add_fields()
    {

        // Add Event Meta
        register_rest_field('event', 'meta', [
            'get_callback' => [__CLASS__, 'get_meta'],
            'schema' => [
                'description' => __('Comment karma.'),
                'type' => 'integer',
            ],
        ]);
    }

    /**
     * Register additional Event API endpoints.
     */
    static function add_routes()
    {

        register_rest_route('wp/v2', '/events/fullcalendar', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_fullcalendar_data'],
			'permission_callback' => [__CLASS__, 'privileged_permission_callback'],
        ]);
    }

	public static function privileged_permission_callback() {
		return current_user_can( 'edit_pages' );
	}

    /**
     * Get Event metadata.
     * N.B.: this method does not check if a user is authorized to view the event (meta)!
     *
     * @param $event Event array
     * @return array
     */
    static function get_meta($event)
    {

        $id = is_numeric($event) ? $event : $event['id'];
        $post_custom = get_post_custom($id);

        $meta = [];

        foreach (self::$_meta_keys as $key) {
            if (array_key_exists($key, $post_custom)) {
                $meta[$key] = reset($post_custom[$key]);
            }
        }

        return $meta;
    }

    static function get_fullcalendar_data(\WP_REST_Request $request)
    {

        // Get calendar view interval
        $start = $request->get_param('start');
        $end = $request->get_param('end');

        // Validate dates
        if (empty($start) || empty($end)) {
            return new \WP_Error('wisvch_plugin_fullcalendar_no_dates', 'No start or end date specified.', ['status' => 500]);
        }

        // Get events within calendar interval
        $events_raw = new \WP_Query([
            'post_type' => 'event',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => '_event_start_date',
                    'value' => [$start, $end],
                    'type' => 'DATETIME',
                    'compare' => 'BETWEEN',
                ],
            ],
        ]);

        $events = [];

        // Get metadata for events
        while ($events_raw->have_posts()) {
            $e = $events_raw->next_post();

            // Get post meta
            $meta = self::get_meta($e->ID);

            // Get categories
            $categories = get_the_terms($e->ID, 'event_category');

            // Add event to result array
            $fullcalendar_arr = [
                'id' => $e->ID,
                'title' => $e->post_title,
                'start' => date_i18n('c', strtotime($meta['_event_start_date'])),
                'url' => get_permalink($e),
                'allday' => false,
                'categories' => is_array($categories) ? wp_list_pluck($categories, 'slug') : false,
            ];

            // Add primary category (if Yoast SEO enabled)
            if (class_exists('\WPSEO_Primary_Term')) {

                $wpseo_primary_term = new \WPSEO_Primary_Term('event_category', $e->ID);
                $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
                $term = get_term($wpseo_primary_term);

                if (! is_wp_error($term)) {
                    $fullcalendar_arr['primary_category'] = $term->slug;
                }
            }

            // Make event last all day if no end date is set.
            if (empty($meta['_event_end_date'])) {
                $fullcalendar_arr['allday'] = true;
            } else {
                $fullcalendar_arr['end'] = date_i18n('c', strtotime($meta['_event_end_date']));
            }

            $events[] = $fullcalendar_arr;
        }

        return $events;
    }
}
