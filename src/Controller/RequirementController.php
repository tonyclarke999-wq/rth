<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Requirement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/requirement')]
class RequirementController extends AbstractController
{
    #[Route('/new/{project}', name: 'app_requirement_new', methods: ['GET', 'POST'])]
    public function new(Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        $requirement = new Requirement();
        $requirement->setProject($project);
        $requirement->setStatus('New');

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $content = $request->request->get('content');
            $status = $request->request->get('status', 'New');

            if ($name) {
                $requirement->setName($name);
                $requirement->setContent($content);
                $requirement->setStatus($status);
                
                $entityManager->persist($requirement);
                $entityManager->flush();

                return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);
            }
        }

        return $this->render('requirement/new.html.twig', [
            'requirement' => $requirement,
            'project' => $project,
        ]);
    }

    #[Route('/{id}', name: 'app_requirement_show', methods: ['GET'])]
    public function show(Requirement $requirement): Response
    {
        return $this->render('requirement/show.html.twig', [
            'requirement' => $requirement,
        ]);
    }
}
