<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\Table(name: '`project`')]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Requirement::class, orphanRemoval: true)]
    private Collection $requirements;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: TestSuite::class, orphanRemoval: true)]
    private Collection $testSuites;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: TestCase::class, orphanRemoval: true)]
    private Collection $testCases;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Bug::class, orphanRemoval: true)]
    private Collection $bugs;

    #[ORM\Column]
    private ?bool $isArchived = null;

    public function __construct()
    {
        $this->requirements = new ArrayCollection();
        $this->testSuites = new ArrayCollection();
        $this->testCases = new ArrayCollection();
        $this->bugs = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Requirement>
     */
    public function getRequirements(): Collection
    {
        return $this->requirements;
    }

    public function addRequirement(Requirement $requirement): self
    {
        if (!$this->requirements->contains($requirement)) {
            $this->requirements->add($requirement);
            $requirement->setProject($this);
        }

        return $this;
    }

    public function removeRequirement(Requirement $requirement): self
    {
        if ($this->requirements->removeElement($requirement)) {
            // set the owning side to null (unless already changed)
            if ($requirement->getProject() === $this) {
                $requirement->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TestSuite>
     */
    public function getTestSuites(): Collection
    {
        return $this->testSuites;
    }

    public function addTestSuite(TestSuite $testSuite): self
    {
        if (!$this->testSuites->contains($testSuite)) {
            $this->testSuites->add($testSuite);
            $testSuite->setProject($this);
        }

        return $this;
    }

    public function removeTestSuite(TestSuite $testSuite): self
    {
        if ($this->testSuites->removeElement($testSuite)) {
            if ($testSuite->getProject() === $this) {
                $testSuite->setProject(null);
            }
        }

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
            $testCase->setProject($this);
        }

        return $this;
    }

    public function removeTestCase(TestCase $testCase): self
    {
        if ($this->testCases->removeElement($testCase)) {
            if ($testCase->getProject() === $this) {
                $testCase->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bug>
     */
    public function getBugs(): Collection
    {
        return $this->bugs;
    }

    public function addBug(Bug $bug): self
    {
        if (!$this->bugs->contains($bug)) {
            $this->bugs->add($bug);
            $bug->setProject($this);
        }

        return $this;
    }

    public function removeBug(Bug $bug): self
    {
        if ($this->bugs->removeElement($bug)) {
            if ($bug->getProject() === $this) {
                $bug->setProject(null);
            }
        }

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): static
    {
        $this->isArchived = $isArchived;

        return $this;
    }
}
