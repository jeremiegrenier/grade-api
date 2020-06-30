<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Entity;

use App\Repository\GradeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GradeRepository::class)
 */
class Grade implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(
     *     min=0,
     *     max=20,
     *     notInRangeMessage="A grade value must be between {{ min }} and {{ max }}",
     * )
     */
    private $value;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subject;

    /**
     * @ManyToOne(targetEntity="Student", inversedBy="grades", cascade={"persist"})
     * @JoinColumn(name="student_id", referencedColumnName="id")
     */
    private $student;

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
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setStudent(Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'value' => $this->getValue(),
            'subject' => $this->getSubject(),
        ];
    }
}
