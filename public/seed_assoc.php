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

$sqls = [
    "DELETE FROM project_user_assoc WHERE user_id = 1",
    "INSERT INTO project_user_assoc (project_id, user_id, user_rights, ba_owner, qa_tester) VALUES (1, 1, 100, 'Y', 'Y')",
    "INSERT INTO project_user_assoc (project_id, user_id, user_rights, ba_owner, qa_tester) VALUES (2, 1, 100, 'Y', 'Y')"
];

foreach ($sqls as $sql) {
    try {
        echo "Executing: $sql ... ";
        $conn->executeStatement($sql);
        echo "OK<br>";
    } catch (\Exception $e) {
        echo "ERROR: " . $e->getMessage() . "<br>";
    }
}
