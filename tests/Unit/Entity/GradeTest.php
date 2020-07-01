<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Tests\Unit\Entity;

use App\Entity\Grade;
use App\Tests\Unit\AbstractUnitTest;

/**
 * Class GradeTest.
 *
 * @author jgrenier
 *
 * @version 1.0.0
 *
 * @coversDefaultClass \App\Entity\Grade
 */
class GradeTest extends AbstractUnitTest
{
    /**
     * @covers ::jsonSerialize
     */
    public function test_jsonSerialize_shouldReturnArray(): void
    {
        $sut = new Grade();
        $sut->setValue(10.5);
        $sut->setSubject('subject');

        $result = $sut->jsonSerialize();
        $this->assertIsArray($result);
        $this->assertEquals(
            [
                'value' => 10.5,
                'subject' => 'subject',
            ],
            $result
        );
    }
}
