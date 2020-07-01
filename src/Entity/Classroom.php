<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Entity;

use App\Repository\ClassroomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @ORM\Entity(repositoryClass=ClassroomRepository::class)
 */
class Classroom implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @OneToMany(targetEntity="Student", mappedBy="classroom", cascade={"persist"})
     */
    private $students;

    /**
     * Classroom constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    /**
     * @codeCoverageIgnore
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function addStudent(Student $student): bool
    {
        if ($this->students->contains($student)) {
            return false;
        }
        $this->students->add($student);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'students' => \array_map(static function (Student $student) {
                return $student->jsonSerialize();
            }, $this->students->toArray()),
            'average' => $this->computeAverage(),
        ];
    }

    /**
     * Compute average of all notes of the classroom.
     */
    private function computeAverage(): ?float
    {
        if (0 === $this->students->count()) {
            return null;
        }

        $total = 0;
        $nbGrade = 0;

        /** @var Student $student */
        foreach ($this->students as $student) {
            /** @var Grade $grade */
            foreach ($student->getGrades() as $grade) {
                $total += $grade->getValue();
                ++$nbGrade;
            }
        }

        if (0 === $nbGrade) {
            return null;
        }

        return $total / $nbGrade;
    }
}
