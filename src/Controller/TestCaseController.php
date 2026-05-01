<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\TestCase;
use App\Repository\TestSuiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/test-case')]
class TestCaseController extends AbstractController
{
    #[Route('/new/{project}', name: 'app_test_case_new', methods: ['GET', 'POST'])]
    public function new(Project $project, Request $request, EntityManagerInterface $entityManager, TestSuiteRepository $testSuiteRepository): Response
    {
        $testCase = new TestCase();
        $testCase->setProject($project);
        $testCase->setStatus('New');

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $testSuiteId = $request->request->get('testSuite');
            $status = $request->request->get('status', 'New');

            if ($name) {
                $testCase->setName($name);
                $testCase->setDescription($description);
                $testCase->setStatus($status);
                
                if ($testSuiteId) {
                    $testCase->setTestSuite($testSuiteRepository->find($testSuiteId));
                }
                
                $entityManager->persist($testCase);
                $entityManager->flush();

                return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);
            }
        }

        return $this->render('test_case/new.html.twig', [
            'test_case' => $testCase,
            'project' => $project,
            'test_suites' => $project->getTestSuites(),
        ]);
    }
}
