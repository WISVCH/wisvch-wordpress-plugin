<?php

namespace WISVCH\CPT\Event;

/**
 * Event feed. Downloadable iCal file.
 * In WordPress while CH Events is in development.
 *
 * @package WISVCH\CPT\Event
 */
class Feed
{
    /**
     * Hook into WordPress.
     */
    public static function register_hooks()
    {

        // Add feed endpoint
        add_action('init', function () {
            add_feed('ical', [__CLASS__, 'generate_feed']);
        });
    }

    /**
     * Generate event feed.
     *
     * @global wpdb $wpdb The WordPress database class.
     */
    public static function generate_feed()
    {

        ob_start();

        header('Content-type: text/calendar');
        header('Content-Disposition: attachment; filename="ical.ics"');

        // iCal header
        echo "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//WISVCH Events//NONSGML Events//EN\nMETHOD:PUBLISH\n";
        echo "X-WR-CALNAME;VALUE=TEXT:W.I.S.V. 'Christiaan Huygens'\nCALSCALE:GREGORIAN\n";

        // Get events
        // @TODO: convert to raw SQL, incorporate meta query for performance
        $events = new \WP_Query([
            'post_type' => 'event',
            'posts_per_page' => -1,
            'meta_query' => [
                'event_clause' => [
                    'key' => '_event_end_date',
                    'type' => 'DATE',
                    'value' => date('Y-m-d H:i'),
                    'compare' => '>',
                ],
            ],
            'orderby' => 'event_clause',
        ]);

        // Event loop
        while ($events->have_posts()) {
            $events->the_post();

            // Get post meta

            $meta = get_post_custom();
            $description = ! isset($meta['_event_short_description']) || empty($meta['_event_short_description'][0]) ? false : $meta['_event_short_description'][0];
            $location = ! isset($meta['_event_location']) || empty($meta['_event_location'][0]) ? "Unknown" : $meta['_event_location'][0];

            // N.B. event end date HAS to be set, is INNER JOIN'd within the WP_Query above.
            $end = ! isset($meta['_event_end_date']) || empty($meta['_event_end_date'][0]) ? false : strtotime($meta['_event_end_date'][0]);

            if (false === $end) {
                continue;
            }

            // Start date == end date if no start date set.
            $start = ! isset($meta['_event_start_date']) || empty($meta['_event_start_date'][0]) ? $end : strtotime($meta['_event_start_date'][0]);

            // Event header
            echo "BEGIN:VEVENT\nUID:wisvch_event".get_the_ID()."\n";

            // Event dates
            echo "CREATED;TZID=Europe/Amsterdam:".get_the_time('Ymd\THis')."\nLAST-MODIFIED;TZID=Europe/Amsterdam:".get_the_modified_time('Ymd\THis')."\n";
            echo "DTSTART;TZID=Europe/Amsterdam:".date('Ymd\THis', $start)."\nDTEND;TZID=Europe/Amsterdam:".date('Ymd\THis', $end)."\n";

            // Event title
            echo "SUMMARY:".static::escape_string(the_title_attribute(['echo' => false]))."\n";

            // Event description
            if (false !== $description) {
                echo "DESCRIPTION:".static::escape_string($description)."\n";
                // @TODO add cost information
            }

            // Event location
            echo "LOCATION:".static::escape_string($location)."\n";

            // @TODO: add category information

            // Event permalink
            echo "URL;TYPE=URI:".get_permalink()."\n";

            // Event footer
            echo "END:VEVENT\n";
        }

        wp_reset_postdata();

        // iCal footer
        echo "END:VCALENDAR";

        $ical = ob_get_clean();

        echo $ical;
    }

    private static function escape_string($str)
    {
        return preg_replace('/([\,;])/', '\\\$1', $str);
    }
}
