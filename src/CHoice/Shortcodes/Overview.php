<?php

namespace WISVCH\CHoice\Shortcodes;

use WISVCH\CHoice\Template;

/**
 * Portal overview page.
 *
 * @package WISVCH\Portal\Shortcodes
 */
class Overview extends Template
{
    const TEMPLATE_NAME = 'overview.php';

    /**
     * Prepare data for use in the template.
     *
     * @return array
     */
    static function getTemplateData()
    {

        $return_data = parent::getTemplateData();

        return $return_data;
    }
}
