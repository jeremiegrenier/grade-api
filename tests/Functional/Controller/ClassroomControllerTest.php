<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Tests\Functional\AppBundle\AbstractFunctionalTest;

/**
 * Class ClassroomControllerTest.
 *
 * @author jgrenier
 *
 * @version 1.0.0
 *
 * @coversDefaultClass \App\Controller\ClassroomController
 */
class ClassroomControllerTest extends AbstractFunctionalTest
{
    /**
     * @covers ::createClassroom
     * @covers ::getClassroom
     */
    public function test_createStudent_validBody_classroomDoNotExist_shouldReturnBadRequest(): void
    {
        $client = $this->createJsonRequest(static::POST, '/classrooms');

        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);

        $content = \json_decode($content, true);

        $this->assertArrayHasKey('classroom', $content);
        $classroomId = $content['classroom'];

        //check classroom is created
        $client = $this->createJsonRequest(static::GET, '/classrooms/'.$classroomId);
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $content = \json_decode($content, true);
        $this->assertArrayHasKey('classroom', $content);

        $this->assertArrayHasKey('id', $content['classroom']);
        $this->assertEquals($classroomId, $content['classroom']['id']);
        $this->assertArrayHasKey('students', $content['classroom']);
        $this->assertEmpty($content['classroom']['students']);
        $this->assertArrayHasKey('average', $content['classroom']);
        $this->assertNull($content['classroom']['average']);
    }
}
