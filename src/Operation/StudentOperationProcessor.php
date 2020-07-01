<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Operation;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * Class StudentOperationProcessor.
 *
 * @author jgrenier
 *
 * @version 1.0.0
 */
class StudentOperationProcessor
{
    /** @var StudentRepository */
    private $studentRepository;

    /** @var LoggerInterface */
    private $logger;

    /**
     * StudentOperationProcessor constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->studentRepository = $registry->getRepository(Student::class);
        $this->logger = $logger;
    }

    /**
     * Process a list of operations on a student.
     *
     * @throws OperationException
     */
    public function process(Student $student, array $operations): bool
    {
        foreach ($operations as $operation) {
            switch ($operation->op) {
                case OperationType::REPLACE_OPERATION:
                    $this->replace($student, $operation->field, $operation->value);
                    break;
                default:
                    throw new OperationException('Operation '.$operation->op.' does not exist');
            }
        }

        return true;
    }

    /**
     * Process a replace operation on a student.
     *
     * @param string|\DateTime $value
     *
     * @throws \Exception
     */
    private function replace(Student $student, string $field, $value)
    {
        $id = $student->getId();
        if (!\in_array($field, ['firstname', 'lastname', 'birthdate'], true)) {
            $this->logger->error(
                'Not allowed to update '.$field.' field',
                \compact('id', 'field', 'value')
            );
            throw new OperationException('Not allowed to update '.$field.' field');
        }

        $this->studentRepository->updateField($id, $field, $value);
    }
}
