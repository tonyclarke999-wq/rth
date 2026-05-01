<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\TestSuite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/test-suite')]
class TestSuiteController extends AbstractController
{
    #[Route('/new/{project}', name: 'app_test_suite_new', methods: ['GET', 'POST'])]
    public function new(Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        $testSuite = new TestSuite();
        $testSuite->setProject($project);

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');

            if ($name) {
                $testSuite->setName($name);
                $testSuite->setDescription($description);
                
                $entityManager->persist($testSuite);
                $entityManager->flush();

                return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);
            }
        }

        return $this->render('test_suite/new.html.twig', [
            'test_suite' => $testSuite,
            'project' => $project,
        ]);
    }
}
