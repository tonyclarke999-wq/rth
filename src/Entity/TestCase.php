<?php

namespace App\Entity;

use App\Repository\TestCaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestCaseRepository::class)]
#[ORM\Table(name: 'testsuite')]
class TestCase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'testid')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'testCases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\ManyToOne(inversedBy: 'testCases')]
    private ?TestSuite $testSuite = null;

    #[ORM\Column(name: 'testsuitename', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'testCase', targetEntity: TestStep::class, orphanRemoval: true)]
    #[ORM\OrderBy(['stepNumber' => 'ASC'])]
    private Collection $testSteps;

    public function __construct()
    {
        $this->testSteps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getTestSuite(): ?TestSuite
    {
        return $this->testSuite;
    }

    public function setTestSuite(?TestSuite $testSuite): self
    {
        $this->testSuite = $testSuite;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, TestStep>
     */
    public function getTestSteps(): Collection
    {
        return $this->testSteps;
    }

    public function addTestStep(TestStep $testStep): static
    {
        if (!$this->testSteps->contains($testStep)) {
            $this->testSteps->add($testStep);
            $testStep->setTestCase($this);
        }

        return $this;
    }

    public function removeTestStep(TestStep $testStep): static
    {
        if ($this->testSteps->removeElement($testStep)) {
            // set the owning side to null (unless already changed)
            if ($testStep->getTestCase() === $this) {
                $testStep->setTestCase(null);
            }
        }

        return $this;
    }
}
