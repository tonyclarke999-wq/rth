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
    // Project table
    "ALTER TABLE project RENAME COLUMN id TO project_id",
    "ALTER TABLE project RENAME COLUMN name TO project_name",
    "ALTER TABLE project RENAME COLUMN created_at TO date_created",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS status VARCHAR(100) DEFAULT 'Stable'",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS deleted CHAR(1) DEFAULT 'N'",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS db_name VARCHAR(100)",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS req_upload_path VARCHAR(255)",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS test_upload_path VARCHAR(255)",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS test_run_upload_path VARCHAR(255)",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS test_plan_upload_path VARCHAR(255)",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS defect_upload_path VARCHAR(255)",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS use_files CHAR(1) DEFAULT 'N'",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS bug_url VARCHAR(255)",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS show_testcase CHAR(1) DEFAULT 'Y'",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS show_custom_1 CHAR(1) DEFAULT 'N'",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS show_custom_2 CHAR(1) DEFAULT 'N'",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS show_custom_3 CHAR(1) DEFAULT 'N'",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS show_custom_4 CHAR(1) DEFAULT 'N'",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS show_custom_5 CHAR(1) DEFAULT 'N'",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS show_custom_6 CHAR(1) DEFAULT 'N'",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS show_window CHAR(1) DEFAULT 'N'",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS show_object CHAR(1) DEFAULT 'N'",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS show_memory_stats CHAR(1) DEFAULT 'N'",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS show_priority CHAR(1) DEFAULT 'N'",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS show_test_input CHAR(1) DEFAULT 'N'",
    "ALTER TABLE project ADD COLUMN IF NOT EXISTS test_versions CHAR(1) DEFAULT 'N'",

    // User table
    "ALTER TABLE \"user\" RENAME COLUMN id TO user_id",
    "ALTER TABLE \"user\" ADD COLUMN IF NOT EXISTS phone VARCHAR(50)",
    "ALTER TABLE \"user\" ADD COLUMN IF NOT EXISTS user_admin CHAR(1) DEFAULT 'N'",
    "ALTER TABLE \"user\" ADD COLUMN IF NOT EXISTS deleted CHAR(1) DEFAULT 'N'",
    "ALTER TABLE \"user\" ADD COLUMN IF NOT EXISTS default_project VARCHAR(100)",

    // TestCase table
    "ALTER TABLE test_case RENAME COLUMN id TO testid",
    "ALTER TABLE test_case RENAME COLUMN name TO testsuitename",

    // TestSuite table
    "ALTER TABLE test_suite RENAME COLUMN id TO testset_id",
    "ALTER TABLE test_suite RENAME COLUMN name TO testset_name",

    // Requirement table
    "ALTER TABLE requirement RENAME COLUMN id TO reqid",
    "ALTER TABLE requirement RENAME COLUMN name TO reqname",

    // TestStep table
    "ALTER TABLE test_step RENAME COLUMN id TO teststepid",
    "ALTER TABLE test_step RENAME COLUMN step_number TO teststep_number",
    "ALTER TABLE test_step RENAME COLUMN test_inputs TO inputs"
];

foreach ($sqls as $sql) {
    try {
        if (strpos($sql, 'RENAME COLUMN id') !== false) {
            $tableName = 'project';
            if (strpos($sql, 'user') !== false) $tableName = 'user';
            if (strpos($sql, 'test_case') !== false) $tableName = 'test_case';
            if (strpos($sql, 'test_suite') !== false) $tableName = 'test_suite';
            if (strpos($sql, 'requirement') !== false) $tableName = 'requirement';
            if (strpos($sql, 'test_step') !== false) $tableName = 'test_step';
            
            echo "Checking columns for $tableName: ";
            $row = $conn->fetchAssociative("SELECT * FROM \"$tableName\" LIMIT 1");
            if ($row) {
                echo implode(', ', array_keys($row)) . "<br>";
            } else {
                echo "Table empty or not found<br>";
            }
        }
        
        echo "Executing: $sql ... ";
        $conn->executeStatement($sql);
        echo "OK<br>";
    } catch (\Exception $e) {
        echo "ERROR: " . $e->getMessage() . "<br>";
    }
}
