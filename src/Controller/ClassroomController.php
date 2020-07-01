<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Controller;

use App\Entity\Classroom;
use App\Repository\ClassroomRepository;
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
    public function createClassroom(ClassroomRepository $classroomRepository): JsonResponse
    {
        $classroom = new Classroom();

        $classroomRepository->add($classroom);

        return new JsonResponse(['classroom' => $classroom->getId()], jsonResponse::HTTP_OK);
    }

    /**
     * @Route("/{classroomId}", methods={"GET"})
     */
    public function getClassroom(
        string $classroomId,
        ClassroomRepository $classroomRepository
    ): JsonResponse {
        $classroom = $classroomRepository->find($classroomId);

        if (!$classroom) {
            return new JsonResponse(['message' => 'classroom not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['classroom' => $classroom], jsonResponse::HTTP_OK);
    }
}
