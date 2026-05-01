<?php

namespace App\Command;

use App\Entity\Project;
use App\Entity\Requirement;
use App\Entity\TestSuite;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:import-legacy',
    description: 'Import initial data from legacy rth.sql',
)]
class ImportLegacyDataCommand extends Command
{
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    private $projectMap = [];

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $sqlPath = '/var/www/html/legacy/sql/rth.sql';

        if (!file_exists($sqlPath)) {
            $io->error('Legacy SQL file not found at: ' . $sqlPath);
            return Command::FAILURE;
        }

        $io->title('Starting Legacy Data Import');

        $sqlContent = file_get_contents($sqlPath);
        
        // 1. Import Users
        $this->importUsers($sqlContent, $io);

        // 2. Import Projects
        $this->importProjects($sqlContent, $io);

        // 3. Import Requirements
        $this->importRequirements($sqlContent, $io);

        // 4. Import Test Suites
        $this->importTestSuites($sqlContent, $io);

        $io->success('Import completed successfully!');

        return Command::SUCCESS;
    }

    private function importUsers(string $sql, SymfonyStyle $io)
    {
        $io->section('Importing Users');
        preg_match_all('/INSERT INTO user VALUES\((.*)\);/', $sql, $matches);

        foreach ($matches[1] as $valString) {
            $vals = $this->parseCsvLine($valString);
            
            $username = trim($vals[2], '"');
            $firstName = trim($vals[3], '"') ?: 'First';
            $lastName = trim($vals[4], '"') ?: 'Last';
            $email = trim($vals[7], '"') ?: $username . '@rth.local';
            
            $existing = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
            if ($existing) continue;

            $user = new User();
            $user->setUsername($username);
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setEmail($email);
            
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setRoles(['ROLE_USER']);
            
            if ($username === 'admin') {
                $user->setRoles(['ROLE_ADMIN']);
            }

            $this->entityManager->persist($user);
            $io->text('Added user: ' . $username);
        }
        $this->entityManager->flush();
    }

    private function importProjects(string $sql, SymfonyStyle $io)
    {
        $io->section('Importing Projects');
        preg_match_all('/INSERT INTO project VALUES\((.*)\);/', $sql, $matches);
        
        foreach ($matches[1] as $valString) {
            $vals = $this->parseCsvLine($valString);
            
            $legacyId = (int)$vals[14]; // Wait, ID is at index 0 in VALUES(1, ...)
            $legacyId = (int)$vals[0];
            $projectName = trim($vals[15], '"');
            $description = trim($vals[16], '"');

            $project = $this->entityManager->getRepository(Project::class)->findOneBy(['name' => $projectName]);
            
            if (!$project) {
                $project = new Project();
                $project->setName($projectName);
                $project->setDescription($description);
                $project->setCreatedAt(new \DateTime());
                $this->entityManager->persist($project);
                $io->text('Added project: ' . $projectName);
            }

            $this->projectMap[$legacyId] = $project;
        }
        $this->entityManager->flush();
    }

    private function importRequirements(string $sql, SymfonyStyle $io)
    {
        $io->section('Importing Requirements');
        preg_match_all('/INSERT INTO requirement VALUES\((.*)\);/', $sql, $matches);
        
        foreach ($matches[1] as $valString) {
            $vals = $this->parseCsvLine($valString);
            
            $legacyProjectId = (int)$vals[0];
            $reqName = trim($vals[2], '"');
            
            $project = $this->projectMap[$legacyProjectId] ?? null;
            if (!$project) continue;

            $existing = $this->entityManager->getRepository(Requirement::class)->findOneBy([
                'name' => $reqName,
                'project' => $project
            ]);
            if ($existing) continue;

            $requirement = new Requirement();
            $requirement->setProject($project);
            $requirement->setName($reqName);
            $requirement->setStatus('New');
            $requirement->setContent('Legacy Imported Requirement');

            $this->entityManager->persist($requirement);
        }
        $this->entityManager->flush();
        $io->text('Requirements imported.');
    }

    private function importTestSuites(string $sql, SymfonyStyle $io)
    {
        $io->section('Importing Test Suites');
        preg_match_all('/INSERT INTO testset VALUES\((.*)\);/', $sql, $matches);
        
        foreach ($matches[1] as $valString) {
            $vals = $this->parseCsvLine($valString);
            
            $legacyProjectId = (int)$vals[0];
            $suiteName = trim($vals[6], '"');
            $description = trim($vals[10], '"');

            $project = $this->projectMap[$legacyProjectId] ?? null;
            if (!$project) continue;

            $existing = $this->entityManager->getRepository(TestSuite::class)->findOneBy([
                'name' => $suiteName,
                'project' => $project
            ]);
            if ($existing) continue;

            $suite = new TestSuite();
            $suite->setProject($project);
            $suite->setName($suiteName);
            $suite->setDescription($description);

            $this->entityManager->persist($suite);
        }
        $this->entityManager->flush();
        $io->text('Test Suites imported.');
    }

    private function parseCsvLine($line)
    {
        return str_getcsv($line, ',', '"');
    }
}
