<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Controller;

use App\Entity\Classroom;
use App\Repository\ClassroomRepository;
use Psr\Log\LoggerInterface;
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
     * @Route("", methods={"POST"})
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
     * @Route("/{classroomId}", methods={"GET"})
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
