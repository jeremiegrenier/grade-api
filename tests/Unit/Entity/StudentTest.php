<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Tests\Unit\Entity;

use App\Entity\Grade;
use App\Entity\Student;
use App\Tests\Unit\AbstractUnitTest;

/**
 * Class StudentTest.
 *
 * @author jgrenier
 *
 * @version 1.0.0
 *
 * @coversDefaultClass \App\Entity\Student
 */
class StudentTest extends AbstractUnitTest
{
    /**
     * @covers ::jsonSerialize
     */
    public function test_jsonSerialize_noGrades_shouldReturnArray(): void
    {
        $date = new \DateTime();
        $sut = new Student();
        $sut->setFirstname('firstname');
        $sut->setLastname('lastname');
        $sut->setBirthdate($date);

        $result = $sut->jsonSerialize();
        $this->assertIsArray($result);
        $this->assertEquals(
            [
                'id' => null,
                'firstname' => 'firstname',
                'lastname' => 'lastname',
                'birthdate' => $date->format('Y-m-d'),
                'grades' => [],
                'average' => null,
            ],
            $result
        );
    }

    /**
     * @covers ::jsonSerialize
     */
    public function test_jsonSerialize_studentWithGrades_shouldReturnArray(): void
    {
        $date = new \DateTime();

        $grade = new Grade();
        $grade->setSubject('subject');
        $grade->setValue(10.5);

        $sut = new Student();
        $sut->setFirstname('firstname');
        $sut->setLastname('lastname');
        $sut->setBirthdate($date);
        $sut->addGrade($grade);

        $result = $sut->jsonSerialize();
        $this->assertIsArray($result);
        $this->assertEquals(
            [
                'id' => null,
                'firstname' => 'firstname',
                'lastname' => 'lastname',
                'birthdate' => $date->format('Y-m-d'),
                'grades' => [
                    [
                        'value' => 10.5,
                        'subject' => 'subject',
                    ],
                ],
                'average' => 10.5,
            ],
            $result
        );
    }

    /**
     * @covers ::computeAverage
     */
    public function test_computeAverage_studentWithoutGrade_shouldReturnNull(): void
    {
        $sut = new Student();

        $result = $this->callProtectedMethod($sut, 'computeAverage', []);

        $this->assertNull($result);
    }

    /**
     * Data provider for test_computeAverage_studentWithGrade_shouldReturnSpecificValues.
     */
    public function provider_test_computeAverage_studentWithGrade_shouldReturnSpecificValues(): array
    {
        return [
            [
              [10],
              10,
            ],
            [
              [10, 20],
              15,
            ],
            [
              [0, 20],
              10,
            ],
            [
              [10.50, 10],
              10.25,
            ],
        ];
    }

    /**
     * @covers ::computeAverage
     *
     * @dataProvider provider_test_computeAverage_studentWithGrade_shouldReturnSpecificValues
     */
    public function test_computeAverage_studentWithGrade_shouldReturnSpecificValues(array $grades, float $expectedAverage): void
    {
        $sut = new Student();

        foreach ($grades as $gradeValue) {
            $grade = new Grade();
            $grade->setSubject('subject');
            $grade->setValue($gradeValue);

            $sut->addGrade($grade);
        }

        $result = $this->callProtectedMethod($sut, 'computeAverage', []);

        $this->assertNotNull($result);
        $this->assertEquals($expectedAverage, $result);
    }
}
