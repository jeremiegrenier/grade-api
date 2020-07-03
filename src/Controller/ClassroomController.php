<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Controller;

use App\Entity\Classroom;
use App\Repository\ClassroomRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Psr\Log\LoggerInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ClassroomController.
 *
 * @author jgrenier
 *
 * @version 1.0.0
 *
 * @Route("/classrooms")
 */
class ClassroomController extends AbstractController
{
    /**
     * Create a new classroom.
     *
     * @Route("", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the id of the classroom created",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="classroom", type="string")
     *     )
     * )
     *
     * @SWG\Tag(name="classrooms")
     */
    public function createClassroom(
        ClassroomRepository $classroomRepository,
        LoggerInterface $logger
    ): JsonResponse {
        $logger->info('Ask to create a new classroom');

        $classroom = new Classroom();

        $classroomRepository->add($classroom);

        $logger->info('Classroom created');

        return new JsonResponse(['classroom' => $classroom->getId()], jsonResponse::HTTP_OK);
    }

    /**
     * Get detail of a classroom.
     *
     * @Route("/{classroomId}", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the detail of the classroom",
     *     @Model(type=Classroom::class)
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Classroom not found",
     *     @SWG\Schema(type="object",
     *     @SWG\Property(property="message", type="string"))
     * )
     * )
     *
     * @SWG\Tag(name="classrooms")
     */
    public function getClassroom(
        string $classroomId,
        ClassroomRepository $classroomRepository,
        LoggerInterface $logger
    ): JsonResponse {
        $logger->info(
            'Ask to get details of a classroom',
            [
                'classroomId' => $classroomId,
            ]
        );

        $classroom = $classroomRepository->find($classroomId);

        if (!$classroom) {
            $logger->error(
                'Classroom not found',
                [
                    'classroomId' => $classroomId,
                ]
            );

            return new JsonResponse(['message' => 'classroom not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $logger->info(
            'Classroom found',
            [
                'classroomId' => $classroomId,
                'classroom' => $classroom,
            ]
        );

        return new JsonResponse(['classroom' => $classroom], jsonResponse::HTTP_OK);
    }
}
