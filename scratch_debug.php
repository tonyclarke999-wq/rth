<?php
require 'vendor/autoload.php';
use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use App\Repository\ProjectRepository;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$kernel = new Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();
$repo = $container->get('doctrine')->getRepository(App\Entity\Project::class);

$active = $repo->findActiveProjects();
echo "Active Projects:\n";
foreach ($active as $p) {
    echo "- " . $p->getName() . " (Archived: " . ($p->isArchived() ? 'Y' : 'N') . ")\n";
}
