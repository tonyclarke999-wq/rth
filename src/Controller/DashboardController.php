<?php

namespace App\Controller;

use App\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/project/{id}/dashboard', name: 'app_project_dashboard')]
    public function index(Project $project): Response
    {
        // Calculate Bug Statistics
        $bugStats = [
            'total' => count($project->getBugs()),
            'bySeverity' => [
                'Low' => 0,
                'Medium' => 0,
                'High' => 0,
                'Critical' => 0,
                'Blocker' => 0,
            ],
            'byStatus' => [],
        ];

        foreach ($project->getBugs() as $bug) {
            $severity = $bug->getSeverity();
            if (isset($bugStats['bySeverity'][$severity])) {
                $bugStats['bySeverity'][$severity]++;
            }
            
            $status = $bug->getStatus();
            if (!isset($bugStats['byStatus'][$status])) {
                $bugStats['byStatus'][$status] = 0;
            }
            $bugStats['byStatus'][$status]++;
        }

        // Calculate Test Statistics
        $testStats = [
            'total' => count($project->getTestCases()),
            'Passed' => 0,
            'Failed' => 0,
            'In Progress' => 0,
            'New' => 0,
        ];

        foreach ($project->getTestCases() as $test) {
            $status = $test->getStatus();
            if (isset($testStats[$status])) {
                $testStats[$status]++;
            }
        }

        return $this->render('dashboard/index.html.twig', [
            'project' => $project,
            'bugStats' => $bugStats,
            'bugSeverityLabels' => array_keys($bugStats['bySeverity']),
            'bugSeverityValues' => array_values($bugStats['bySeverity']),
            'testStats' => $testStats,
        ]);
    }
}
