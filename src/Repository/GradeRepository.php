<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Repository;

use App\Entity\Grade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Grade|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grade|null findOneBy(array $criteria, array $orderBy = null)
 * @method Grade[]    findAll()
 * @method Grade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeRepository extends ServiceEntityRepository
{
    /**
     * GradeRepository constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grade::class);
    }
}
