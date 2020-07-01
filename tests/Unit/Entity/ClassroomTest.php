<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Tests\Unit\Entity;

use App\Entity\Classroom;
use App\Entity\Grade;
use App\Entity\Student;
use App\Tests\Unit\AbstractUnitTest;

/**
 * Class ClassroomTest.
 *
 * @author jgrenier
 *
 * @version 1.0.0
 *
 * @coversDefaultClass \App\Entity\Classroom
 */
class ClassroomTest extends AbstractUnitTest
{
    /**
     * @covers ::jsonSerialize
     */
    public function test_jsonSerialize_noStudent_shouldReturnArray(): void
    {
        $sut = new Classroom();

        $result = $sut->jsonSerialize();
        $this->assertIsArray($result);
        $this->assertEquals(
            [
                'id' => null,
                'students' => [],
                'average' => null,
            ],
            $result
        );
    }

    /**
     * @covers ::jsonSerialize
     */
    public function test_jsonSerialize_studentNoGrades_shouldReturnArray(): void
    {
        $sut = new Classroom();
        $date = new \DateTime();

        $student = new Student();
        $student->setFirstname('firstname');
        $student->setLastname('lastname');
        $student->setBirthdate($date);

        $sut->addStudent($student);

        $result = $sut->jsonSerialize();
        $this->assertIsArray($result);
        $this->assertEquals(
            [
                'id' => null,
                'students' => [
                    [
                        'id' => null,
                        'firstname' => 'firstname',
                        'lastname' => 'lastname',
                        'birthdate' => $date->format('Y-m-d'),
                        'grades' => [],
                        'average' => null,
                    ],
                ],
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
        $sut = new Classroom();

        $date = new \DateTime();

        $grade = new Grade();
        $grade->setSubject('subject');
        $grade->setValue(10.5);

        $student = new Student();
        $student->setFirstname('firstname');
        $student->setLastname('lastname');
        $student->setBirthdate($date);
        $student->addGrade($grade);

        $sut->addStudent($student);

        $result = $sut->jsonSerialize();
        $this->assertIsArray($result);
        $this->assertEquals(
            [
                'id' => null,
                'students' => [
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
                ],
                'average' => 10.5,
            ],
            $result
        );
    }

    /**
     * @covers ::addStudent
     */
    public function test_addStudent_studentNotPresent_shouldReturnTrue(): void
    {
        $sut = new Classroom();

        $this->assertEmpty($this->getProtectedProperty($sut, 'students'));

        $date = new \DateTime();

        $student = new Student();
        $student->setFirstname('firstname');
        $student->setLastname('lastname');
        $student->setBirthdate($date);

        $result = $sut->addStudent($student);

        $this->assertTrue($result);

        $this->assertNotEmpty($this->getProtectedProperty($sut, 'students'));
        $this->assertContains($student, $this->getProtectedProperty($sut, 'students'));
    }

    /**
     * @covers ::addStudent
     */
    public function test_addStudent_studentAlreadyPresent_shouldReturnTrue(): void
    {
        $sut = new Classroom();
        $date = new \DateTime();

        $student = new Student();
        $student->setFirstname('firstname');
        $student->setLastname('lastname');
        $student->setBirthdate($date);

        $sut->addStudent($student);

        $this->assertNotEmpty($this->getProtectedProperty($sut, 'students'));
        $this->assertContains($student, $this->getProtectedProperty($sut, 'students'));
        $this->assertCount(1, $this->getProtectedProperty($sut, 'students'));

        $result = $sut->addStudent($student);

        $this->assertFalse($result);

        $this->assertNotEmpty($this->getProtectedProperty($sut, 'students'));
        $this->assertContains($student, $this->getProtectedProperty($sut, 'students'));
        $this->assertCount(1, $this->getProtectedProperty($sut, 'students'));
    }

    /**
     * @covers ::computeAverage
     */
    public function test_computeAverage_noStudent_shouldReturnNull(): void
    {
        $sut = new Classroom();

        $result = $this->callProtectedMethod($sut, 'computeAverage', []);

        $this->assertNull($result);
    }

    /**
     * @covers ::computeAverage
     */
    public function test_computeAverage_studentWithoutGrade_shouldReturnNull(): void
    {
        $sut = new Classroom();

        $student = new Student();

        $sut->addStudent($student);

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
        $sut = new Classroom();

        $student = new Student();

        foreach ($grades as $gradeValue) {
            $grade = new Grade();
            $grade->setSubject('subject');
            $grade->setValue($gradeValue);

            $student->addGrade($grade);
        }

        $sut->addStudent($student);

        $result = $this->callProtectedMethod($sut, 'computeAverage', []);

        $this->assertNotNull($result);
        $this->assertEquals($expectedAverage, $result);
    }
}
