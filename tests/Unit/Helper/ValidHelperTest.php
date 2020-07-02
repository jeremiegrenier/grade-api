<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Tests\Unit\Helper;

use App\Helper\ValidHelper;
use App\Tests\Unit\AbstractUnitTest;

/**
 * Class ValidHelperTest.
 * Unit tests on ValidHelper Class.
 *
 * @author cnoyer
 *
 * @version 1.0.0
 *
 * @coversDefaultClass \App\Helper\ValidHelper
 */
class ValidHelperTest extends AbstractUnitTest
{
    public const DATE_TEST = '2017-10-29';

    /**
     * @covers ::isValidDate()
     */
    public function testIsValidDate_rightParam_shouldReturnTrue(): void
    {
        $stub = ValidHelper::isValidDate(self::DATE_TEST);
        $this->assertTrue($stub);
    }

    /**
     * @covers ::isValidDate()
     */
    public function testIsValidDate_wrongParam_shouldReturnFalse(): void
    {
        $stub = ValidHelper::isValidDate(null);
        $this->assertFalse($stub);

        $stub = ValidHelper::isValidDate('29-10-2017');
        $this->assertFalse($stub);
    }
}
