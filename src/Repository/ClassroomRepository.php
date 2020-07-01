<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Repository;

use App\Entity\Classroom;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Classroom|null find($id, $lockMode = null, $lockVersion = null)
 * @method Classroom|null findOneBy(array $criteria, array $orderBy = null)
 * @method Classroom[]    findAll()
 * @method Classroom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassroomRepository extends ServiceEntityRepository
{
    /**
     * ClassroomRepository constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classroom::class);
    }

    /**
     * Create a new classroom in database.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function add(Classroom $classroom): Classroom
    {
        $this->getEntityManager()->persist($classroom);
        $this->getEntityManager()->flush();

        return $classroom;
    }

    /**
     * Add a student to a classroom.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addStudent(Classroom $classroom, Student $student): Classroom
    {
        $classroom->addStudent($student);

        $student->setClassroom($classroom);

        $this->getEntityManager()->persist($classroom);
        $this->getEntityManager()->flush();

        return $classroom;
    }
}
