<?php

namespace WISVCH\Portal;

/**
 * WISVCH Portal template.
 *
 * @package WISVCH\Portal
 */
abstract class Template
{
    const TEMPLATE_NAME = 'overview.php';

    /**
     * Render template.
     */
    static function output($auth_required = false)
    {
        Shortcodes::get_template(static::TEMPLATE_NAME, static::getTemplateData(), $auth_required);
    }

    /**
     * Prepare data for use in the template.
     *
     * @return array
     */
    static function getTemplateData()
    {

        $return_data = [];

        // Get WordPress user object if logged in
        $user = wp_get_current_user();
        if ($user->exists()) {
            $return_data['user'] = $user->data;
        }

        return $return_data;
    }
}
