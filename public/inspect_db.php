<?php
require dirname(__DIR__).'/vendor/autoload.php';
use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->bootEnv(dirname(__DIR__).'/.env');

$dbUrl = $_ENV['DATABASE_URL'];
if (strpos($dbUrl, '/rth') === false) {
    $dbUrl = str_replace('/app', '/rth', $dbUrl);
}

$kernel = new Kernel('dev', true);
$kernel->boot();
$conn = $kernel->getContainer()->get('doctrine')->getConnection();

try {
    echo "<h1>Table Inspection</h1>";
    
    $tables = ['project', 'project_user_assoc', 'release_tbl'];
    
    foreach ($tables as $table) {
        echo "<h2>Columns for $table</h2>";
        $columns = $conn->fetchAllAssociative("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = '$table'");
        foreach ($columns as $column) {
            echo $column['column_name'] . " (" . $column['data_type'] . ")<br>";
        }
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
