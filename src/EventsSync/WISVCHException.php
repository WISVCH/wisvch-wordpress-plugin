<?php
/**
 * The ${FILE_NAME} file.
 */

namespace WISVCH\EventsSync;

use RuntimeException;

class WISVCHException extends RuntimeException
{

    /**
     * WISVCHException constructor.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}