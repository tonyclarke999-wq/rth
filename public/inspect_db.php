<?php
require dirname(__DIR__).'/vendor/autoload.php';
use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->bootEnv(dirname(__DIR__).'/.env');

// Override database name to 'rth'
$dbUrl = $_ENV['DATABASE_URL'];
$dbUrl = str_replace('/app?', '/rth?', $dbUrl);
$_ENV['DATABASE_URL'] = $dbUrl;

$kernel = new Kernel('dev', true);
$kernel->boot();
$conn = $kernel->getContainer()->get('doctrine')->getConnection();

try {
    echo "<h1>Database Inspection</h1>";
    $users = $conn->fetchAllAssociative("SELECT user_id, username, password FROM \"user\"");
    echo "<h2>Users</h2>";
    foreach ($users as $user) {
        echo "ID: " . $user['user_id'] . ", Name: " . $user['username'] . ", Pass: " . $user['password'] . "<br>";
    }
    
    $projects = $conn->fetchAllAssociative("SELECT project_id, project_name FROM project");
    echo "<h2>Projects</h2>";
    foreach ($projects as $project) {
        echo "ID: " . $project['project_id'] . ", Name: " . $project['project_name'] . "<br>";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
