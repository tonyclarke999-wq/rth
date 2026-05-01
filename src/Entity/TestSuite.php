<?php

namespace App\Entity;

use App\Repository\TestSuiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestSuiteRepository::class)]
class TestSuite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'testSuites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'testSuite', targetEntity: TestCase::class)]
    private Collection $testCases;

    public function __construct()
    {
        $this->testCases = new ArrayCollection();
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

    /**
     * @return Collection<int, TestCase>
     */
    public function getTestCases(): Collection
    {
        return $this->testCases;
    }

    public function addTestCase(TestCase $testCase): self
    {
        if (!$this->testCases->contains($testCase)) {
            $this->testCases->add($testCase);
            $testCase->setTestSuite($this);
        }

        return $this;
    }

    public function removeTestCase(TestCase $testCase): self
    {
        if ($this->testCases->removeElement($testCase)) {
            // set the owning side to null (unless already changed)
            if ($testCase->getTestSuite() === $this) {
                $testCase->setTestSuite(null);
            }
        }

        return $this;
    }
}
