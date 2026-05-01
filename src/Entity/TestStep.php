<?php

namespace App\Entity;

use App\Repository\TestStepRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestStepRepository::class)]
class TestStep
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'testSteps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TestCase $testCase = null;

    #[ORM\Column(type: 'integer')]
    private ?int $stepNumber = null;

    #[ORM\Column(type: 'text')]
    private ?string $action = null;

    #[ORM\Column(type: 'text')]
    private ?string $expectedResult = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $testInputs = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTestCase(): ?TestCase
    {
        return $this->testCase;
    }

    public function setTestCase(?TestCase $testCase): static
    {
        $this->testCase = $testCase;

        return $this;
    }

    public function getStepNumber(): ?int
    {
        return $this->stepNumber;
    }

    public function setStepNumber(int $stepNumber): static
    {
        $this->stepNumber = $stepNumber;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getExpectedResult(): ?string
    {
        return $this->expectedResult;
    }

    public function setExpectedResult(string $expectedResult): static
    {
        $this->expectedResult = $expectedResult;

        return $this;
    }

    public function getTestInputs(): ?string
    {
        return $this->testInputs;
    }

    public function setTestInputs(?string $testInputs): static
    {
        $this->testInputs = $testInputs;

        return $this;
    }
}
