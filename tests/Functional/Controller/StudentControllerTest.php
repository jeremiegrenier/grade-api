<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Tests\Functional\AppBundle\AbstractFunctionalTest;

/**
 * Class StudentControllerTest.
 *
 * @author jgrenier
 *
 * @version 1.0.0
 *
 * @coversDefaultClass \App\Controller\StudentController
 */
class StudentControllerTest extends AbstractFunctionalTest
{
    /**
     * Data provider for test_createStudent_invalidBody_shouldReturnBadRequest.
     */
    public function provider_test_createStudent_invalidBody_shouldReturnBadRequest(): array
    {
        return [
            [
              null,
            ],
            [
              [
                  'firstname' => 'firstname',
              ],
            ],
                [
              [
                  'lastname' => 'lastname',
              ],
            ],
            [
              [
                  'birthdate' => '2020-07-01',
              ],
            ],
            [
              [
                  'classroom' => 'classroom',
              ],
            ],
            [
              [
                  'firstname' => 'firstname',
                  'lastname' => 'lastname',
                  'birthdate' => '2020-07-01',
              ],
            ],
            [
              [
                  'firstname' => 'firstname',
                  'lastname' => 'lastname',
              ],
            ],
            [
              [
                  'firstname' => 'firstname',
                  'birthdate' => '2020-07-01',
                  'classroom' => 'classroom',
              ],
            ],
            [
              [
                  'firstname' => 'firstname',
                  'classroom' => 'classroom',
              ],
            ],
            [
              [
                  'firstname' => 'firstname',
                  'classroom' => 'classroom',
                  'birthdate' => '2020-07-01',
                  'classroom' => 'notAnInt',
              ],
            ],
            [
              [
                  'firstname' => 'firstname',
                  'classroom' => 'classroom',
                  'birthdate' => 'invalidBirthDate',
                  'classroom' => 1,
              ],
            ],
        ];
    }

    /**
     * @covers ::createStudent
     *
     * @dataProvider provider_test_createStudent_invalidBody_shouldReturnBadRequest
     *
     * @param ?array $content
     */
    public function test_createStudent_invalidBody_shouldReturnBadRequest(?array $content): void
    {
        $client = $this->createJsonRequest(static::POST, '/students', null !== $content ? \json_encode($content) : null);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();

        $this->assertJson($content);

        $content = \json_decode($content, true);

        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('wrong content', $content['message']);
    }

    /**
     * @covers ::createStudent
     */
    public function test_createStudent_validBody_classroomDoNotExist_shouldReturnBadRequest(): void
    {
        $content = [
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'birthdate' => '2020-07-01',
            'classroom' => 9999999,
        ];

        $client = $this->createJsonRequest(static::POST, '/students', \json_encode($content));

        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);

        $content = \json_decode($content, true);

        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('classroom invalid', $content['message']);
    }

    /**
     * @covers ::createStudent
     * @covers ::getStudent
     */
    public function test_createStudent_validBody_shouldReturnStudentIdAndCreateIt(): void
    {
        //create classroom
        $client = $this->createJsonRequest(static::POST, '/classrooms');
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);

        $this->assertArrayHasKey('classroom', $content);
        $classroomId = $content['classroom'];

        $content = [
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'birthdate' => '2020-07-01',
            'classroom' => $classroomId,
        ];

        $client = $this->createJsonRequest(static::POST, '/students', \json_encode($content));

        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);

        $content = \json_decode($content, true);

        $this->assertArrayHasKey('student', $content);

        $studentId = $content['student'];
        //check student is created
        $client = $this->createJsonRequest(static::GET, '/students/'.$studentId);
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('student', $content);

        $this->assertArrayHasKey('id', $content['student']);
        $this->assertEquals($studentId, $content['student']['id']);
        $this->assertArrayHasKey('firstname', $content['student']);
        $this->assertEquals('firstname', $content['student']['firstname']);
        $this->assertArrayHasKey('lastname', $content['student']);
        $this->assertEquals('lastname', $content['student']['lastname']);
        $this->assertArrayHasKey('birthdate', $content['student']);
        $this->assertEquals('2020-07-01', $content['student']['birthdate']);
        $this->assertArrayHasKey('grades', $content['student']);
        $this->assertEmpty($content['student']['grades']);
        $this->assertArrayHasKey('average', $content['student']);
        $this->assertNull($content['student']['average']);
    }

    /**
     * @covers ::getStudent
     */
    public function test_getStudent_studentNotExist_shouldReturnNotFound(): void
    {
        $client = $this->createJsonRequest(static::GET, '/students/999999999');
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('student not found', $content['message']);
    }

    /**
     * @covers ::patchStudent
     */
    public function test_patchStudent_noOperations_shouldReturnBadRequest(): void
    {
        $client = $this->createJsonRequest(static::PATCH, '/students/1', null);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('wrong content', $content['message']);
    }

    /**
     * @covers ::patchStudent
     */
    public function test_patchStudent_studentNotExist_shouldReturnNotFound(): void
    {
        $content = [
            [
                'op' => 'replace',
                'field' => 'firstname',
                'value' => 'newName',
            ],
        ];

        $client = $this->createJsonRequest(static::PATCH, '/students/1', \json_encode($content));
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('student not found', $content['message']);
    }

    /**
     * @covers ::patchStudent
     */
    public function test_patchStudent_operationInvalid_shouldReturnBadRequest(): void
    {
        $client = $this->createJsonRequest(static::POST, '/classrooms');
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);

        $this->assertArrayHasKey('classroom', $content);
        $classroomId = $content['classroom'];

        $content = [
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'birthdate' => '2020-07-01',
            'classroom' => $classroomId,
        ];

        $client = $this->createJsonRequest(static::POST, '/students', \json_encode($content));

        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);

        $content = \json_decode($content, true);

        $this->assertArrayHasKey('student', $content);

        $studentId = $content['student'];

        $content = [
            [
                'op' => 'attach',
                'field' => 'firstname',
                'value' => 'newName',
            ],
        ];

        $client = $this->createJsonRequest(static::PATCH, '/students/'.$studentId, \json_encode($content));
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Operation attach does not exist', $content['error']);
    }

    /**
     * @covers ::patchStudent
     */
    public function test_patchStudent_operationValid_shouldReturnOkAndUpdateStudent(): void
    {
        $client = $this->createJsonRequest(static::POST, '/classrooms');
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);

        $this->assertArrayHasKey('classroom', $content);
        $classroomId = $content['classroom'];

        $content = [
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'birthdate' => '2020-07-01',
            'classroom' => $classroomId,
        ];

        $client = $this->createJsonRequest(static::POST, '/students', \json_encode($content));

        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);

        $content = \json_decode($content, true);

        $this->assertArrayHasKey('student', $content);

        $studentId = $content['student'];

        $content = [
            [
                'op' => 'replace',
                'field' => 'firstname',
                'value' => 'newName',
            ],
        ];

        $client = $this->createJsonRequest(static::PATCH, '/students/'.$studentId, \json_encode($content));
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('ok', $content['message']);

        //check student is updated
        $client = $this->createJsonRequest(static::GET, '/students/'.$studentId);
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('student', $content);

        $this->assertArrayHasKey('id', $content['student']);
        $this->assertEquals($studentId, $content['student']['id']);
        $this->assertArrayHasKey('firstname', $content['student']);
        $this->assertEquals('newName', $content['student']['firstname']);
        $this->assertArrayHasKey('lastname', $content['student']);
        $this->assertEquals('lastname', $content['student']['lastname']);
        $this->assertArrayHasKey('birthdate', $content['student']);
        $this->assertEquals('2020-07-01', $content['student']['birthdate']);
        $this->assertArrayHasKey('grades', $content['student']);
        $this->assertEmpty($content['student']['grades']);
        $this->assertArrayHasKey('average', $content['student']);
        $this->assertNull($content['student']['average']);
    }

    /**
     * @covers ::addGradeToStudent
     */
    public function test_addGradeToStudent_invalidData_shouldReturnBadRequest(): void
    {
        $client = $this->createJsonRequest(static::POST, '/students/1/grades', null);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('wrong content', $content['message']);
    }

    /**
     * @covers ::addGradeToStudent
     */
    public function test_addGradeToStudent_studentNotExist_shouldReturnNotFound(): void
    {
        $content = [
            'value' => 10,
            'subject' => 'test',
        ];

        $client = $this->createJsonRequest(static::POST, '/students/1/grades', \json_encode($content));
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('student not found', $content['message']);
    }

    /**
     * @covers ::addGradeToStudent
     */
    public function test_addGradeToStudent_operationValid_shouldReturnOkAndUpdateStudent(): void
    {
        $client = $this->createJsonRequest(static::POST, '/classrooms');
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);

        $this->assertArrayHasKey('classroom', $content);
        $classroomId = $content['classroom'];

        $content = [
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'birthdate' => '2020-07-01',
            'classroom' => $classroomId,
        ];

        $client = $this->createJsonRequest(static::POST, '/students', \json_encode($content));

        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);

        $content = \json_decode($content, true);
        $this->assertArrayHasKey('student', $content);
        $studentId = $content['student'];

        //check student has no grades
        $client = $this->createJsonRequest(static::GET, '/students/'.$studentId);
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('student', $content);

        $this->assertArrayHasKey('id', $content['student']);
        $this->assertEquals($studentId, $content['student']['id']);
        $this->assertArrayHasKey('firstname', $content['student']);
        $this->assertEquals('firstname', $content['student']['firstname']);
        $this->assertArrayHasKey('lastname', $content['student']);
        $this->assertEquals('lastname', $content['student']['lastname']);
        $this->assertArrayHasKey('birthdate', $content['student']);
        $this->assertEquals('2020-07-01', $content['student']['birthdate']);
        $this->assertArrayHasKey('grades', $content['student']);
        $this->assertEmpty($content['student']['grades']);
        $this->assertArrayHasKey('average', $content['student']);
        $this->assertNull($content['student']['average']);

        $content = [
            'value' => 10,
            'subject' => 'test',
        ];

        $client = $this->createJsonRequest(static::POST, '/students/'.$studentId.'/grades', \json_encode($content));
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('ok', $content['message']);

        //check student is updated
        $client = $this->createJsonRequest(static::GET, '/students/'.$studentId);
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('student', $content);

        $this->assertArrayHasKey('id', $content['student']);
        $this->assertEquals($studentId, $content['student']['id']);
        $this->assertArrayHasKey('firstname', $content['student']);
        $this->assertEquals('firstname', $content['student']['firstname']);
        $this->assertArrayHasKey('lastname', $content['student']);
        $this->assertEquals('lastname', $content['student']['lastname']);
        $this->assertArrayHasKey('birthdate', $content['student']);
        $this->assertEquals('2020-07-01', $content['student']['birthdate']);
        $this->assertArrayHasKey('grades', $content['student']);
        $this->assertNotEmpty($content['student']['grades']);
        $this->assertCount(1, $content['student']['grades']);
        $this->assertArrayHasKey('average', $content['student']);
        $this->assertEquals(10, $content['student']['average']);
    }

    /**
     * @covers ::deleteStudent
     */
    public function test_deleteStudent_studentNotExist_shouldReturnNotFound(): void
    {
        $client = $this->createJsonRequest(static::DELETE, '/students/1');
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('student not found', $content['message']);
    }

    /**
     * @covers ::deleteStudent
     */
    public function test_deleteStudent_student_shouldReturnOkAndRemoveStudent(): void
    {
        $client = $this->createJsonRequest(static::POST, '/classrooms');
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);

        $this->assertArrayHasKey('classroom', $content);
        $classroomId = $content['classroom'];

        $content = [
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'birthdate' => '2020-07-01',
            'classroom' => $classroomId,
        ];

        $client = $this->createJsonRequest(static::POST, '/students', \json_encode($content));

        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);

        $content = \json_decode($content, true);
        $this->assertArrayHasKey('student', $content);
        $studentId = $content['student'];

        //check student exist
        $client = $this->createJsonRequest(static::GET, '/students/'.$studentId);
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('student', $content);

        $this->assertArrayHasKey('id', $content['student']);
        $this->assertEquals($studentId, $content['student']['id']);

        $client = $this->createJsonRequest(static::DELETE, '/students/'.$studentId);
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('ok', $content['message']);

        //check student not exist
        $client = $this->createJsonRequest(static::GET, '/students/'.$studentId);
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('student not found', $content['message']);
    }
}
