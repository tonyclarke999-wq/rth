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
    // Rename tables
    "ALTER TABLE IF EXISTS test_case RENAME TO testsuite",
    "ALTER TABLE IF EXISTS test_suite RENAME TO testset",

    // Create association tables
    "CREATE TABLE IF NOT EXISTS project_user_assoc (
        project_user_assoc_id SERIAL PRIMARY KEY,
        project_id INT NOT NULL,
        user_id INT NOT NULL,
        ba_owner CHAR(1) DEFAULT 'N',
        qa_tester CHAR(1) DEFAULT 'N',
        delete_rights CHAR(1) DEFAULT 'N',
        email_testset CHAR(1) DEFAULT 'N',
        email_discussion CHAR(1) DEFAULT 'N',
        email_new_bug CHAR(1) DEFAULT 'N',
        email_update_bug CHAR(1) DEFAULT 'N',
        email_assigned_bug CHAR(1) DEFAULT 'N',
        email_bugnote_bug CHAR(1) DEFAULT 'N',
        email_status_bug CHAR(1) DEFAULT 'N',
        user_rights INT DEFAULT 0
    )",

    "CREATE TABLE IF NOT EXISTS testset_testsuite_assoc (
        testset_testsuite_associd SERIAL PRIMARY KEY,
        testsetid INT NOT NULL,
        testid INT NOT NULL,
        finished CHAR(1) DEFAULT 'N',
        logtimestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        root_cause VARCHAR(255),
        teststatus VARCHAR(50),
        assignedto VARCHAR(100),
        comments TEXT
    )",

    "CREATE TABLE IF NOT EXISTS testsuite_requirement_assoc (
        testsuite_requirement_associd SERIAL PRIMARY KEY,
        testid INT NOT NULL,
        reqid INT NOT NULL,
        percentcovered INT DEFAULT 100
    )",

    // Create release and build
    "CREATE TABLE IF NOT EXISTS release (
        releaseid SERIAL PRIMARY KEY,
        project_id INT NOT NULL,
        releasename VARCHAR(100),
        description TEXT,
        archive CHAR(1) DEFAULT 'N',
        datereceived DATE
    )",

    "CREATE TABLE IF NOT EXISTS build (
        \"BuildID\" SERIAL PRIMARY KEY,
        \"ReleaseID\" INT NOT NULL,
        \"BuildName\" VARCHAR(100),
        \"DateReceived\" DATE,
        \"Archive\" CHAR(1) DEFAULT 'N',
        description TEXT
    )"
];

foreach ($sqls as $sql) {
    try {
        echo "Executing: " . substr($sql, 0, 50) . "... ";
        $conn->executeStatement($sql);
        echo "OK<br>";
    } catch (\Exception $e) {
        echo "ERROR: " . $e->getMessage() . "<br>";
    }
}
