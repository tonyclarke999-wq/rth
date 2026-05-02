<?php
require dirname(__DIR__).'/vendor/autoload.php';
use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->bootEnv(dirname(__DIR__).'/.env');

// Override database name to 'rth' if needed
$dbUrl = $_ENV['DATABASE_URL'];
if (strpos($dbUrl, '/rth') === false) {
    $dbUrl = str_replace('/app', '/rth', $dbUrl);
}

$kernel = new Kernel('dev', true);
$kernel->boot();
$conn = $kernel->getContainer()->get('doctrine')->getConnection();

try {
    echo "Connected to database via Symfony Kernel.\n";

    $queries = [
        // Rename 'release' table to 'release_tbl' to avoid keyword issues and match legacy
        "ALTER TABLE IF EXISTS \"release\" RENAME TO release_tbl",
        
        // Ensure 'archive' column exists in 'project'
        "DO $$ 
         BEGIN 
            IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='project' AND column_name='archive') THEN
                ALTER TABLE project ADD COLUMN archive CHAR(1) DEFAULT 'N';
            END IF;
         END $$;",

        // Rename ID columns to match legacy/Symfony mappings
        "ALTER TABLE IF EXISTS project RENAME COLUMN id TO project_id",
        "ALTER TABLE IF EXISTS release_tbl RENAME COLUMN id TO release_id",
        "ALTER TABLE IF EXISTS build RENAME COLUMN id TO build_id",
        
        // Ensure 'is_archived' is removed if it was accidentally added instead of 'archive'
        "ALTER TABLE IF EXISTS project DROP COLUMN IF EXISTS is_archived",

        // Fix other potential naming issues
        "ALTER TABLE IF EXISTS testset RENAME COLUMN id TO testset_id",
        "ALTER TABLE IF EXISTS testsuite RENAME COLUMN id TO testid"
    ];

    foreach ($queries as $query) {
        try {
            $conn->executeStatement($query);
            echo "Executed: $query\n";
        } catch (Exception $e) {
            echo "Error executing query: $query\n";
            echo "Reason: " . $e->getMessage() . "\n";
        }
    }

    echo "Schema alignment complete.\n";

} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
