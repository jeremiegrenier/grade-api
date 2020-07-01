<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass=StudentRepository::class)
 */
class Student implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="date")
     */
    private $birthdate;

    /**
     * @OneToMany(targetEntity="Grade", mappedBy="student", cascade={"persist"})
     *
     * @var PersistentCollection
     */
    private $grades;

    /**
     * @ManyToOne(targetEntity="Classroom", inversedBy="students")
     * @JoinColumn(name="classroom_id", referencedColumnName="id")
     */
    private $classroom;

    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->grades = new ArrayCollection();
    }

    /**
     * @codeCoverageIgnore
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getBirthdate(): \DateTimeInterface
    {
        return $this->birthdate;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getGrades(): array
    {
        return $this->grades->toArray();
    }

    /**
     * @codeCoverageIgnore
     */
    public function addGrade(Grade $grade): bool
    {
        $this->grades->add($grade);

        return true;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getClassroom(): Classroom
    {
        return $this->classroom;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setClassroom(Classroom $classroom): self
    {
        $this->classroom = $classroom;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'firstname' => $this->getFirstname(),
            'lastname' => $this->getLastname(),
            'birthdate' => $this->getBirthdate()->format('Y-m-d'),
            'grades' => \array_map(static function (Grade $grade) {
                return $grade->jsonSerialize();
            }, $this->getGrades()),
            'average' => $this->computeAverage(),
        ];
    }

    /**
     * Compute average grade for user.
     */
    private function computeAverage(): ?float
    {
        if (0 === $this->grades->count()) {
            return null;
        }

        $total = \array_reduce($this->grades->toArray(), static function ($total, Grade $grade) {
            return $total + $grade->getValue();
        }, 0);

        return $total / \count($this->grades);
    }
}
