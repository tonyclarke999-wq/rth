<?php

namespace App\Controller;

use App\Entity\Attachment;
use App\Entity\Bug;
use App\Entity\Project;
use App\Repository\RequirementRepository;
use App\Repository\TestCaseRepository;
use App\Entity\TestCase;
use App\Service\EmailNotificationService;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bug')]
class BugController extends AbstractController
{
    #[Route('/new/{project}', name: 'app_bug_new', methods: ['GET', 'POST'])]
    public function new(
        Project $project, 
        Request $request, 
        EntityManagerInterface $entityManager,
        RequirementRepository $requirementRepository,
        TestCaseRepository $testCaseRepository,
        FileUploader $fileUploader,
        EmailNotificationService $emailService
    ): Response
    {
        $bug = new Bug();
        $bug->setProject($project);
        $bug->setStatus('New');

        if ($request->isMethod('POST')) {
            $summary = $request->request->get('summary');
            $description = $request->request->get('description');
            $requirementId = $request->request->get('requirement');
            $testCaseId = $request->request->get('testCase');
            $severity = $request->request->get('severity', 'Medium');
            $priority = $request->request->get('priority', 'Medium');

            if ($summary) {
                $bug->setSummary($summary);
                $bug->setDescription($description);
                $bug->setSeverity($severity);
                $bug->setPriority($priority);
                
                if ($requirementId) {
                    $bug->setRequirement($requirementRepository->find($requirementId));
                }
                
                if ($testCaseId) {
                    $bug->setTestCase($testCaseRepository->find($testCaseId));
                }

                $attachmentFiles = $request->files->get('attachments');
                if ($attachmentFiles) {
                    foreach ($attachmentFiles as $uploadedFile) {
                        if ($uploadedFile) {
                            $fileSize = $uploadedFile->getSize();
                            $originalFilename = $uploadedFile->getClientOriginalName();
                            $mimeType = $uploadedFile->getClientMimeType();
                            
                            $newFilename = $fileUploader->upload($uploadedFile);
                            
                            $attachment = new Attachment();
                            $attachment->setFilename($newFilename);
                            $attachment->setOriginalFilename($originalFilename);
                            $attachment->setMimeType($mimeType);
                            $attachment->setFileSize($fileSize);
                            $attachment->setBug($bug);
                            
                            $entityManager->persist($attachment);
                        }
                    }
                }
                
                $entityManager->persist($bug);
                $entityManager->flush();

                // Send Email Notification
                try {
                    $emailService->sendBugReportNotification($bug);
                } catch (\Exception $e) {
                    // Log error or ignore in dev if mailhog is down
                }

                return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);
            }
        }

        return $this->render('bug/new.html.twig', [
            'bug' => $bug,
            'project' => $project,
            'requirements' => $project->getRequirements(),
            'test_cases' => $project->getTestCases(),
        ]);
    }

    #[Route('/{id}', name: 'app_bug_show', methods: ['GET'])]
    public function show(Bug $bug): Response
    {
        return $this->render('bug/show.html.twig', [
            'bug' => $bug,
        ]);
    }
}
