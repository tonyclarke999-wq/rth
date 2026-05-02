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
    #[ORM\Column(name: 'project_id')]
    private ?int $id = null;

    #[ORM\Column(name: 'project_name', length: 100)]
    private ?string $name = null;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'date_created', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'deleted', length: 1)]
    private ?string $deleted = 'N';

    #[ORM\Column(name: 'status', length: 100, nullable: true)]
    private ?string $status = 'Stable';

    #[ORM\Column(name: 'db_name', length: 100, nullable: true)]
    private ?string $dbName = null;

    #[ORM\Column(name: 'req_upload_path', length: 255, nullable: true)]
    private ?string $reqUploadPath = null;

    #[ORM\Column(name: 'test_upload_path', length: 255, nullable: true)]
    private ?string $testUploadPath = null;

    #[ORM\Column(name: 'test_run_upload_path', length: 255, nullable: true)]
    private ?string $testRunUploadPath = null;

    #[ORM\Column(name: 'test_plan_upload_path', length: 255, nullable: true)]
    private ?string $testPlanUploadPath = null;

    #[ORM\Column(name: 'defect_upload_path', length: 255, nullable: true)]
    private ?string $defectUploadPath = null;

    #[ORM\Column(name: 'use_files', length: 1, nullable: true)]
    private ?string $useFiles = 'N';

    #[ORM\Column(name: 'bug_url', length: 255, nullable: true)]
    private ?string $bugUrl = null;

    #[ORM\Column(name: 'show_testcase', length: 1, nullable: true)]
    private ?string $showTestCase = 'Y';

    #[ORM\Column(name: 'show_custom_1', length: 1, nullable: true)]
    private ?string $showCustom1 = 'N';

    #[ORM\Column(name: 'show_custom_2', length: 1, nullable: true)]
    private ?string $showCustom2 = 'N';

    #[ORM\Column(name: 'show_custom_3', length: 1, nullable: true)]
    private ?string $showCustom3 = 'N';

    #[ORM\Column(name: 'show_custom_4', length: 1, nullable: true)]
    private ?string $showCustom4 = 'N';

    #[ORM\Column(name: 'show_custom_5', length: 1, nullable: true)]
    private ?string $showCustom5 = 'N';

    #[ORM\Column(name: 'show_custom_6', length: 1, nullable: true)]
    private ?string $showCustom6 = 'N';

    #[ORM\Column(name: 'show_window', length: 1, nullable: true)]
    private ?string $showWindow = 'N';

    #[ORM\Column(name: 'show_object', length: 1, nullable: true)]
    private ?string $showObject = 'N';

    #[ORM\Column(name: 'show_memory_stats', length: 1, nullable: true)]
    private ?string $showMemoryStats = 'N';

    #[ORM\Column(name: 'show_priority', length: 1, nullable: true)]
    private ?string $showPriority = 'N';

    #[ORM\Column(name: 'show_test_input', length: 1, nullable: true)]
    private ?string $showTestInput = 'N';

    #[ORM\Column(name: 'test_versions', length: 1, nullable: true)]
    private ?string $testVersions = 'N';

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Requirement::class, orphanRemoval: true)]
    private Collection $requirements;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: TestSuite::class, orphanRemoval: true)]
    private Collection $testSuites;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: TestCase::class, orphanRemoval: true)]
    private Collection $testCases;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Bug::class, orphanRemoval: true)]
    private Collection $bugs;

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
