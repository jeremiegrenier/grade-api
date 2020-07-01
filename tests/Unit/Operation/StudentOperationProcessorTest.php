<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Tests\Unit\Entity;

use App\Entity\Student;
use App\Operation\OperationException;
use App\Operation\StudentOperationProcessor;
use App\Repository\StudentRepository;
use App\Tests\Unit\AbstractUnitTest;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

/**
 * Class StudentOperationProcessorTest.
 *
 * @author jgrenier
 *
 * @version 1.0.0
 *
 * @coversDefaultClass \App\Operation\StudentOperationProcessor
 */
class StudentOperationProcessorTest extends AbstractUnitTest
{
    /** @var StudentOperationProcessor */
    private $sut;

    /** @var StudentRepository|MockObject */
    private $studentRepositoryMock;

    /** @var LoggerInterface|MockObject */
    private $loggerInterfaceMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->studentRepositoryMock = $this->createMock(StudentRepository::class);
        $managerRegistryMock = $this->createMock(ManagerRegistry::class);
        $managerRegistryMock->method('getRepository')
            ->with(Student::class)
            ->willReturn($this->studentRepositoryMock);

        $this->loggerInterfaceMock = $this->createMock(LoggerInterface::class);

        $this->sut = new StudentOperationProcessor($managerRegistryMock, $this->loggerInterfaceMock);
    }

    /**
     * @covers ::process
     */
    public function test_process_invalidOperations_shouldThrowException(): void
    {
        $operation = new \stdClass();
        $operation->op = 'invalid';
        $operations = [
            $operation,
        ];

        $student = new Student();

        $this->expectException(OperationException::class);
        $this->expectExceptionMessage('Operation invalid does not exist');

        $this->sut->process($student, $operations);
    }

    /**
     * @covers ::process
     * @covers ::replace
     */
    public function test_process_replaceOperations_fieldCanNotBeReplaced_shouldThrowException(): void
    {
        $operation = new \stdClass();
        $operation->op = 'replace';
        $operation->field = 'notAField';
        $operation->value = 'value';
        $operations = [
            $operation,
        ];

        $student = $this->createMock(Student::class);
        $student->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $this->expectException(OperationException::class);
        $this->expectExceptionMessage('Not allowed to update notAField field');

        $this->loggerInterfaceMock->expects($this->once())
            ->method('error')
            ->with(
                'Not allowed to update notAField field',
                ['id' => 1, 'field' => 'notAField', 'value' => 'value']
            );

        $this->sut->process($student, $operations);
    }

    /**
     * @covers ::process
     * @covers ::replace
     */
    public function test_process_replaceOperations_fieldCanBeReplaced_shouldUpdateFiledAndReturnTrue(): void
    {
        $operation = new \stdClass();
        $operation->op = 'replace';
        $operation->field = 'firstname';
        $operation->value = 'value';
        $operations = [
            $operation,
        ];

        $student = $this->createMock(Student::class);
        $student->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $this->loggerInterfaceMock->expects($this->never())
            ->method('error');

        $this->studentRepositoryMock->expects($this->once())
            ->method('updateField')
            ->with(1, 'firstname', 'value');

        $result = $this->sut->process($student, $operations);

        $this->assertTrue($result);
    }
}
