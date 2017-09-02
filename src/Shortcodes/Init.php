<?php

namespace WISVCH\Shortcodes;

/**
 * Initialize custom shortcodes.
 *
 * @package WISVCH\Shortcodes
 */
class Init
{
    /**
     * Initialize shortcodes.
     */
    function __construct()
    {

        $shortcodes = [
            'wisv_attachment' => [Attachment::class, 'do_shortcode'],
        ];

        foreach ($shortcodes as $shortcode => $function) {
            add_shortcode($shortcode, $function);
        }
    }
}
