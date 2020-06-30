<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    /**
     * Add a student in database.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function add(Student $student): Student
    {
        $this->getEntityManager()->persist($student);
        $this->getEntityManager()->flush();

        return $student;
    }

    /**
     * Update a field inside Student entity.
     *
     * @param string $studentId Id of student
     * @param string $field     Field name to update
     * @param string $value     Value to put inside
     *
     * @return mixed
     */
    public function updateField(string $studentId, string $field, string $value)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->update(Student::class, 's')
            ->set('s.'.$field, '(:value)')
            ->where('s.id = (:id)')
            ->setParameters(['id' => $studentId, 'value' => $value])
            ->getQuery()
            ->execute();
    }

    /**
     * Add a grade to a student.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addGrade(Student $student, float $value, string $subject): Student
    {
        $grade = new Grade();
        $grade->setValue($value);
        $grade->setSubject($subject);
        $grade->setStudent($student);

        $student->addGrade($grade);

        $this->getEntityManager()->persist($student);
        $this->getEntityManager()->flush();

        return $student;
    }

    /**
     * Remove a student.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeStudent(Student $student): Student
    {
        $this->getEntityManager()->remove($student);
        $this->getEntityManager()->flush();

        return $student;
    }
}
