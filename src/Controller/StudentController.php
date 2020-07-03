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
use Nelmio\ApiDocBundle\Annotation\Model;
use Psr\Log\LoggerInterface;
use Swagger\Annotations as SWG;
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
     * Create a student.
     *
     * @Route("", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the id of the student created",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="student", type="string")
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Content is invalid",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="message", type="string", example="wrong content")
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Classroom doesn't exist",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="message", type="string", example="classroom invalid")
     *     )
     * )
     *
     * @SWG\Parameter(
     *     name="",
     *     in="body",
     *     description="student",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="firstname", type="string"),
     *         @SWG\Property(property="lastname", type="string"),
     *         @SWG\Property(property="birthdate", type="string", format="date"),
     *         @SWG\Property(property="classroom", type="integer"),
     *     )
     * )
     *
     * @SWG\Tag(name="students")
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
     * Get details of a student.
     *
     * @Route("/{studentId}", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the detail of the student",
     *     @Model(type=Student::class)
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Student not found",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="message", type="string", example="student not found")
     *     )
     * )
     *
     * @SWG\Tag(name="students")
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
     * Update a student.
     *
     * @Route("/{studentId}", methods={"PATCH"})
     * @SWG\Response(
     *     response=200,
     *     description="Student is updated",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="message", type="string", example="ok")
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Content is invalid",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="message", type="string", example="wrong content")
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Student doesn't exist",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="message", type="string", example="student not found")
     *     )
     * )
     *
     * @SWG\Parameter(
     *     name="",
     *     in="body",
     *     description="List of operations to update the student",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="op", type="string", example="replace"),
     *              @SWG\Property(property="value", type="string", example="firstname"),
     *              @SWG\Property(property="field", type="string", example="newValue"),
     *         )
     *     )
     * )
     *
     * @SWG\Tag(name="students")
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
     * Add a grade to a student.
     *
     * @Route("/{studentId}/grades", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Grade is added",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="message", type="string", example="ok")
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Content is invalid",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="message", type="string", example="wrong content")
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Student doesn't exist",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="message", type="string", example="student not found")
     *     )
     * )
     *
     * @SWG\Parameter(
     *     name="",
     *     in="body",
     *     description="The grade for the student",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="value", type="number", format="float"),
     *         @SWG\Property(property="subject", type="string"),
     *     )
     * )
     *
     * @SWG\Tag(name="students")
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
            || (!\is_float($grade['value']) && !is_int($grade['value']))
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
     * Delete a student.
     *
     * @Route("/{studentId}", methods={"DELETE"})
     * @SWG\Response(
     *     response=200,
     *     description="Student removed",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="message", type="string", example="ok")
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Student doesn't exist",
     *     @SWG\Schema(type="object",
     *         @SWG\Property(property="message", type="string", example="student not found")
     *     )
     * )
     *
     * @SWG\Tag(name="students")
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
