<?php
// Fix 1: Ensure all users have a default_project set
// Fix 2: Add missing columns to project_user_assoc
// Fix 3: Ensure all users are associated with a project

try {
    $pdo = new PDO('pgsql:host=db;dbname=rth', 'app', 'app');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected OK\n\n";

    // Step 1: Show current user state
    echo "=== Current Users ===\n";
    $stmt = $pdo->query('SELECT user_id, username, default_project FROM "user" ORDER BY user_id');
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  user_id={$r['user_id']} username={$r['username']} default_project=" . var_export($r['default_project'], true) . "\n";
    }

    // Step 2: Show current project_user_assoc
    echo "\n=== Current project_user_assoc ===\n";
    $stmt2 = $pdo->query('SELECT * FROM project_user_assoc ORDER BY project_id, user_id');
    while ($r = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        echo "  " . json_encode($r) . "\n";
    }

    // Step 3: Show projects
    echo "\n=== Projects ===\n";
    $stmt3 = $pdo->query('SELECT project_id, project_name FROM project ORDER BY project_id');
    while ($r = $stmt3->fetch(PDO::FETCH_ASSOC)) {
        echo "  project_id={$r['project_id']} name={$r['project_name']}\n";
    }

    // Fix A: Set default_project for users that have NULL - assign to project_id=1
    echo "\n=== Fixing NULL default_project values ===\n";
    $affected = $pdo->exec("UPDATE \"user\" SET default_project = 1 WHERE default_project IS NULL");
    echo "  Updated $affected users with default_project = 1\n";

    // Fix B: Associate all users with project_id=1 if not already associated
    echo "\n=== Adding missing project_user_assoc rows ===\n";
    $users = $pdo->query('SELECT user_id FROM "user"');
    foreach ($users as $u) {
        $uid = $u['user_id'];
        $check = $pdo->query("SELECT COUNT(*) FROM project_user_assoc WHERE project_id = 1 AND user_id = $uid");
        if ($check->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO project_user_assoc (project_id, user_id, user_rights, delete_rights) VALUES (1, $uid, 1, 0)");
            echo "  Added user_id=$uid to project_id=1\n";
        } else {
            echo "  user_id=$uid already in project_id=1\n";
        }
    }

    // Fix C: Add missing columns to project_user_assoc
    echo "\n=== Adding missing columns to project_user_assoc ===\n";
    $columns = [
        'email_testset'     => "CHAR(1) DEFAULT 'N'",
        'email_discussion'  => "CHAR(1) DEFAULT 'N'",
        'email_new_bug'     => "CHAR(1) DEFAULT 'N'",
        'email_update_bug'  => "CHAR(1) DEFAULT 'N'",
        'email_assigned_bug'=> "CHAR(1) DEFAULT 'N'",
        'email_bugnote_bug' => "CHAR(1) DEFAULT 'N'",
        'email_status_bug'  => "CHAR(1) DEFAULT 'N'",
        'qa_tester'         => "CHAR(1) DEFAULT 'N'",
        'ba_owner'          => "CHAR(1) DEFAULT 'N'",
    ];
    foreach ($columns as $col => $type) {
        try {
            $pdo->exec("ALTER TABLE project_user_assoc ADD COLUMN IF NOT EXISTS $col $type");
            echo "  Added column: $col\n";
        } catch (Exception $e) {
            echo "  Skipped $col: " . $e->getMessage() . "\n";
        }
    }

    echo "\n=== Done! Verify ===\n";
    $stmt4 = $pdo->query('SELECT user_id, username, default_project FROM "user" ORDER BY user_id');
    while ($r = $stmt4->fetch(PDO::FETCH_ASSOC)) {
        echo "  user_id={$r['user_id']} username={$r['username']} default_project={$r['default_project']}\n";
    }

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
