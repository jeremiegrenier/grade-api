<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Controller;

use App\Entity\Student;
use App\Helper\ValidHelper;
use App\Operation\StudentOperationProcessor;
use App\Repository\ClassroomRepository;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StudentController.
 *
 * @author jgrenier
 *
 * @version 1.0.0
 *
 * @Route("/students")
 */
class StudentController extends AbstractController
{
    /**
     * @Route("", methods={"POST"})
     */
    public function createStudent(
        Request $request,
        StudentRepository $studentRepository,
        ClassroomRepository $classroomRepository
    ): JsonResponse {
        $studentData = \json_decode($request->getContent(), true);

        if (
            !\is_array($studentData)
            || !\array_key_exists('firstname', $studentData)
            || !\array_key_exists('lastname', $studentData)
            || !\array_key_exists('birthdate', $studentData)
            || !ValidHelper::isValidDate($studentData['birthdate'])
            || !\array_key_exists('classroom', $studentData)
            || !\is_int($studentData['classroom'])
        ) {
            return new JsonResponse(
                ['message' => 'wrong content'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $classroom = $classroomRepository->find($studentData['classroom']);

        if (!$classroom) {
            return new JsonResponse(
                ['message' => 'classroom invalid'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        $student = new Student();
        $student->setFirstname($studentData['firstname']);
        $student->setLastname($studentData['lastname']);
        $student->setBirthdate(new \DateTime($studentData['birthdate']));

        $studentRepository->add($student);
        $classroomRepository->addStudent($classroom, $student);

        return new JsonResponse(['student' => $student->getId()], jsonResponse::HTTP_OK);
    }

    /**
     * @Route("/{studentId}", methods={"GET"})
     */
    public function getStudent(
        string $studentId,
        StudentRepository $studentRepository
    ): JsonResponse {
        $student = $studentRepository->find($studentId);

        if (!$student) {
            return new JsonResponse(['message' => 'student not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['student' => $student], jsonResponse::HTTP_OK);
    }

    /**
     * @Route("/{studentId}", methods={"PATCH"})
     */
    public function patchStudent(
        string $studentId,
        Request $request,
        StudentRepository $studentRepository,
        StudentOperationProcessor $studentOperationProcessor
    ): JsonResponse {
        $operations = \json_decode($request->getContent());

        if (!\is_array($operations)) {
            return new JsonResponse(
                ['message' => 'wrong content'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $student = $studentRepository->find($studentId);

        if (!$student) {
            return new JsonResponse(
                ['message' => 'student not found'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        try {
            $studentOperationProcessor->process($student, $operations);

            return new JsonResponse(['message' => 'ok']);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @Route("/{studentId}/grades", methods={"POST"})
     */
    public function addGradeToStudent(
        string $studentId,
        Request $request,
        StudentRepository $studentRepository
    ): JsonResponse {
        $grade = \json_decode($request->getContent(), true);

        if (
            !\is_array($grade)
            || !\array_key_exists('value', $grade)
            || !\is_int($grade['value'])
            || !\array_key_exists('subject', $grade)
        ) {
            return new JsonResponse(
                ['message' => 'wrong content'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $student = $studentRepository->find($studentId);

        if (!$student) {
            return new JsonResponse(
                ['message' => 'student not found'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        try {
            $studentRepository->addGrade($student, $grade['value'], $grade['subject']);

            return new JsonResponse(['message' => 'ok']);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @Route("/{studentId}", methods={"DELETE"})
     */
    public function deleteStudent(
        string $studentId,
        StudentRepository $studentRepository
    ): JsonResponse {
        $student = $studentRepository->find($studentId);

        if (!$student) {
            return new JsonResponse(
                ['message' => 'student not found'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        try {
            $studentRepository->removeStudent($student);

            return new JsonResponse(['message' => 'ok']);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }
}
