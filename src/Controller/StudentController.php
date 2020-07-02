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
use Psr\Log\LoggerInterface;
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
        ClassroomRepository $classroomRepository,
        LoggerInterface $logger
    ): JsonResponse {
        $logger->info('Ask to create student');

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
            $logger->error(
                'Request content is invalid',
                [
                    'data' => $studentData,
                ]
            );

            return new JsonResponse(
                ['message' => 'wrong content'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $logger->info(
            'Data to create request are valid',
            $studentData
        );

        $classroom = $classroomRepository->find($studentData['classroom']);

        if (!$classroom) {
            $logger->info(
                'Classroom could not be found to create student',
                $studentData
            );

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

        $logger->info(
            'studentCreated',
            [
                'student' => $student,
            ]
        );

        return new JsonResponse(['student' => $student->getId()], jsonResponse::HTTP_OK);
    }

    /**
     * @Route("/{studentId}", methods={"GET"})
     */
    public function getStudent(
        string $studentId,
        StudentRepository $studentRepository,
        LoggerInterface $logger
    ): JsonResponse {
        $logger->info(
            'Ask to get detail of student',
            [
                'studentId' => $studentId,
            ]
        );

        $student = $studentRepository->find($studentId);

        if (!$student) {
            $logger->error(
                'Student not found',
                [
                    'studentId' => $studentId,
                ]
            );

            return new JsonResponse(['message' => 'student not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $logger->info(
            'Return detail of student',
            [
                'studentId' => $studentId,
                'student' => $student,
            ]
        );

        return new JsonResponse(['student' => $student], jsonResponse::HTTP_OK);
    }

    /**
     * @Route("/{studentId}", methods={"PATCH"})
     */
    public function patchStudent(
        string $studentId,
        Request $request,
        StudentRepository $studentRepository,
        StudentOperationProcessor $studentOperationProcessor,
        LoggerInterface $logger
    ): JsonResponse {
        $operations = \json_decode($request->getContent());

        $logger->info(
            'Ask to update student',
            [
                'operations' => $operations,
                'studentId' => $studentId,
            ]
        );

        if (!\is_array($operations)) {
            $logger->error(
                'Operations format are invalid',
                [
                    'operations' => $operations,
                    'studentId' => $studentId,
                ]
            );

            return new JsonResponse(
                ['message' => 'wrong content'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $student = $studentRepository->find($studentId);

        if (!$student) {
            $logger->error(
                'Student not found for update',
                [
                    'operations' => $operations,
                    'studentId' => $studentId,
                ]
            );

            return new JsonResponse(
                ['message' => 'student not found'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        try {
            $studentOperationProcessor->process($student, $operations);

            $logger->info(
                'Student updated',
                [
                    'operations' => $operations,
                    'studentId' => $studentId,
                ]
            );

            return new JsonResponse(['message' => 'ok']);
        } catch (\Exception $e) {
            $logger->error(
                'Error when process operations on student',
                [
                    'errorMessage' => $e->getMessage(),
                    'operations' => $operations,
                    'studentId' => $studentId,
                ]
            );

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
        StudentRepository $studentRepository,
        LoggerInterface $logger
    ): JsonResponse {
        $grade = \json_decode($request->getContent(), true);

        $logger->info(
            'Ask to add grade to student',
            [
                'grade' => $grade,
                'studentId' => $studentId,
            ]
        );

        if (
            !\is_array($grade)
            || !\array_key_exists('value', $grade)
            || !\is_int($grade['value'])
            || !\array_key_exists('subject', $grade)
        ) {
            $logger->error(
                'Grade content invalid',
                [
                    'grade' => $grade,
                    'studentId' => $studentId,
                ]
            );

            return new JsonResponse(
                ['message' => 'wrong content'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $student = $studentRepository->find($studentId);

        if (!$student) {
            $logger->error(
                'Student not found for update',
                [
                    'grade' => $grade,
                    'studentId' => $studentId,
                ]
            );

            return new JsonResponse(
                ['message' => 'student not found'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        try {
            $studentRepository->addGrade($student, $grade['value'], $grade['subject']);

            $logger->info(
                'Grade added',
                [
                    'grade' => $grade,
                    'studentId' => $studentId,
                ]
            );

            return new JsonResponse(['message' => 'ok']);
        } catch (\Exception $e) {
            $logger->error(
                'Error when add grade on student',
                [
                    'errorMessage' => $e->getMessage(),
                    'grade' => $grade,
                    'studentId' => $studentId,
                ]
            );

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
        StudentRepository $studentRepository,
        LoggerInterface $logger
    ): JsonResponse {
        $student = $studentRepository->find($studentId);

        $logger->info(
            'Ask to delete a student',
            [
                'studentId' => $studentId,
            ]
        );

        if (!$student) {
            $logger->error(
                'Student not found to delete',
                [
                    'studentId' => $studentId,
                ]
            );

            return new JsonResponse(
                ['message' => 'student not found'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        try {
            $studentRepository->removeStudent($student);

            $logger->info(
                'Student deleted',
                [
                    'studentId' => $studentId,
                ]
            );

            return new JsonResponse(['message' => 'ok']);
        } catch (\Exception $e) {
            $logger->error(
                'Error when try to delete student',
                [
                    'errorMessage' => $e->getMessage(),
                    'studentId' => $studentId,
                ]
            );

            return new JsonResponse(
                ['error' => $e->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }
}
