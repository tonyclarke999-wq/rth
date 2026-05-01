<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\BugRepository;
use App\Repository\ProjectRepository;
use App\Repository\RequirementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'app_project_index', methods: ['GET'])]
    public function index(Request $request, ProjectRepository $projectRepository): Response
    {
        $searchQuery = $request->query->get('q');
        $showArchived = $request->query->getBoolean('show_archived', false);
        
        if ($searchQuery) {
            $projects = $projectRepository->findBySearchQuery($searchQuery, $showArchived);
        } else {
            $projects = $showArchived ? $projectRepository->findAll() : $projectRepository->findActiveProjects();
        }

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
            'search_query' => $searchQuery,
            'show_archived' => $showArchived,
        ]);
    }

    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            
            if ($name) {
                $project->setName($name);
                $project->setDescription($description);
                $entityManager->persist($project);
                $entityManager->flush();

                return $this->redirectToRoute('app_project_index');
            }
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show(
        Project $project, 
        Request $request, 
        RequirementRepository $requirementRepository,
        BugRepository $bugRepository
    ): Response
    {
        $reqSearchQuery = $request->query->get('req_q');
        $bugSearchQuery = $request->query->get('bug_q');
        
        if ($reqSearchQuery) {
            $requirements = $requirementRepository->findByProjectAndSearch($project, $reqSearchQuery);
        } else {
            $requirements = $project->getRequirements();
        }

        if ($bugSearchQuery) {
            $bugs = $bugRepository->findByProjectAndSearch($project, $bugSearchQuery);
        } else {
            $bugs = $project->getBugs();
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'requirements' => $requirements,
            'bugs' => $bugs,
            'req_search_query' => $reqSearchQuery,
            'bug_search_query' => $bugSearchQuery,
        ]);
    }

    #[Route('/{id}/archive', name: 'app_project_archive', methods: ['POST'])]
    public function archive(Project $project, EntityManagerInterface $entityManager): Response
    {
        $project->setIsArchived(true);
        $entityManager->flush();

        $this->addFlash('success', 'Project archived.');
        return $this->redirectToRoute('app_project_index');
    }

    #[Route('/{id}/unarchive', name: 'app_project_unarchive', methods: ['POST'])]
    public function unarchive(Project $project, EntityManagerInterface $entityManager): Response
    {
        $project->setIsArchived(false);
        $entityManager->flush();

        $this->addFlash('success', 'Project restored.');
        return $this->redirectToRoute('app_project_index');
    }
}
