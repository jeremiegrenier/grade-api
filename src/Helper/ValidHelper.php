<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Helper;

/**
 * ValidHelper.
 * Define check methods.
 *
 * @author jgrenier
 *
 * @version 1.0.0
 */
class ValidHelper
{
    public const DATE_MATCH = '(?:19|20)\d{2}-(?:0[1-9]|1[0-2])-(?:0[1-9]|[12]\d|3[01])';
    public const DATE_REGEX = '/^'.self::DATE_MATCH.'$/';

    /**
     * Return if date format is right.
     *
     * @param string|\DateTime $date
     */
    public static function isValidDate($date): bool
    {
        return !empty($date)
            && ((\is_string($date) && \preg_match(static::DATE_REGEX, (string) $date)) || ($date instanceof \DateTime));
    }
}
