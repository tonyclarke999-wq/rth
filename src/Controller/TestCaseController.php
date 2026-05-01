<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\TestCase;
use App\Entity\TestStep;
use App\Repository\TestStepRepository;
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

    #[Route('/{id}', name: 'app_test_case_show', methods: ['GET'])]
    public function show(TestCase $testCase): Response
    {
        return $this->render('test_case/show.html.twig', [
            'test_case' => $testCase,
        ]);
    }

    #[Route('/{id}/step/new', name: 'app_test_step_new', methods: ['POST'])]
    public function newStep(Request $request, TestCase $testCase, EntityManagerInterface $entityManager, TestStepRepository $testStepRepository): Response
    {
        $action = $request->request->get('action');
        $expectedResult = $request->request->get('expectedResult');
        $testInputs = $request->request->get('testInputs');
        $stepNumber = $request->request->get('stepNumber');

        if ($action && $expectedResult) {
            $testStep = new TestStep();
            $testStep->setTestCase($testCase);
            $testStep->setAction($action);
            $testStep->setExpectedResult($expectedResult);
            $testStep->setTestInputs($testInputs);

            if ($stepNumber) {
                $testStep->setStepNumber((int)$stepNumber);
            } else {
                $testStep->setStepNumber($testStepRepository->findNextStepNumber($testCase->getId()));
            }

            $entityManager->persist($testStep);
            $entityManager->flush();
            $this->addFlash('success', 'Test step added successfully.');
        } else {
            $this->addFlash('error', 'Action and Expected Result are required.');
        }

        return $this->redirectToRoute('app_test_case_show', ['id' => $testCase->getId()]);
    }

    #[Route('/step/{id}/delete', name: 'app_test_step_delete', methods: ['POST'])]
    public function deleteStep(Request $request, TestStep $testStep, EntityManagerInterface $entityManager): Response
    {
        $testCaseId = $testStep->getTestCase()->getId();
        
        if ($this->isCsrfTokenValid('delete'.$testStep->getId(), $request->request->get('_token'))) {
            $entityManager->remove($testStep);
            $entityManager->flush();
            $this->addFlash('success', 'Test step deleted.');
        }

        return $this->redirectToRoute('app_test_case_show', ['id' => $testCaseId]);
    }
}
