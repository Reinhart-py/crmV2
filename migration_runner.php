<?php
if (!defined('BASEPATH')) {
    define('BASEPATH', __DIR__ . '/system/');
}

$db_file = __DIR__ . '/application/config/database.php';
if (!file_exists($db_file)) {
    die("Database configuration file not found at: $db_file\n");
}

require_once $db_file;
$cfg = isset($db['default']) ? $db['default'] : null;
if (!$cfg) {
    die("Default database configuration missing in database.php\n");
}

$port = !empty($cfg['port']) ? (int)$cfg['port'] : 3306;
mysqli_report(MYSQLI_REPORT_OFF);
$mysqli = @new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], '', $port);
if ($mysqli->connect_error) {
    $mysqli = @new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database'], $port);
    if ($mysqli->connect_error) {
        die("Database Connection Error (" . $mysqli->connect_errno . "): " . $mysqli->connect_error . "\n");
    }
} else {
    $db_name = $mysqli->real_escape_string($cfg['database']);
    $mysqli->query("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    if (!$mysqli->select_db($cfg['database'])) {
        die("Error selecting database '" . $cfg['database'] . "': " . $mysqli->error . "\n");
    }
}

$mysqli->query("SET NAMES utf8mb4");
$mysqli->query("SET SESSION innodb_strict_mode = 0");

$table_schema = "CREATE TABLE IF NOT EXISTS `schema_migrations` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `version` VARCHAR(50) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `batch` INT(11) NOT NULL DEFAULT 1,
    `executed_at` DATETIME NOT NULL,
    `execution_time_ms` INT(11) NOT NULL DEFAULT 0,
    `status` VARCHAR(20) NOT NULL DEFAULT 'SUCCESS',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_migration_version` (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$mysqli->query($table_schema);

$batch_res = $mysqli->query("SELECT MAX(batch) as current_batch FROM `schema_migrations`");
$current_batch = 0;
if ($batch_res && $row = $batch_res->fetch_assoc()) {
    $current_batch = (int)$row['current_batch'];
}
$next_batch = $current_batch + 1;

$applied = array();
$app_res = $mysqli->query("SELECT `version`, `name`, `batch`, `executed_at`, `status` FROM `schema_migrations` ORDER BY `version` ASC");
if ($app_res) {
    while ($r = $app_res->fetch_assoc()) {
        $applied[$r['version']] = $r;
    }
}

$migrations_dir = __DIR__ . '/application/migrations';
$all_files = array();
if (is_dir($migrations_dir)) {
    $scan = scandir($migrations_dir);
    foreach ($scan as $f) {
        if (preg_match('/^([0-9]{3,})_(.+)\.php$/', $f, $m)) {
            $all_files[$m[1]] = array(
                'file' => $f,
                'path' => $migrations_dir . '/' . $f,
                'version' => $m[1],
                'name' => $m[2],
                'class' => 'Migration_' . $m[1] . '_' . $m[2]
            );
        }
    }
}
ksort($all_files);

$action = 'migrate';
if (php_sapi_name() === 'cli') {
    global $argv;
    if (isset($argv[1])) {
        $action = strtolower(trim($argv[1]));
    }
} else {
    $expected_key = getenv('MIGRATION_KEY') ? getenv('MIGRATION_KEY') : 'crm7strokes_migrate';
    $provided_key = isset($_GET['key']) ? trim($_GET['key']) : '';
    if ($provided_key !== $expected_key) {
        header('Content-Type: application/json', true, 403);
        die(json_encode(array('status' => 'error', 'message' => 'Unauthorized. Please provide ?key=' . $expected_key . ' or execute via server CLI.')));
    }
    if (isset($_GET['action'])) {
        $action = strtolower(trim($_GET['action']));
    }
}

if ($action === 'status') {
    if (php_sapi_name() === 'cli') {
        echo "=== CRM Migration Status ===\n";
        echo sprintf("%-10s %-35s %-12s %-20s\n", "Version", "Name", "Status", "Executed At");
        echo str_repeat("-", 80) . "\n";
        foreach ($all_files as $ver => $info) {
            $st = isset($applied[$ver]) ? 'APPLIED' : 'PENDING';
            $dt = isset($applied[$ver]) ? $applied[$ver]['executed_at'] : '-';
            echo sprintf("%-10s %-35s %-12s %-20s\n", $ver, $info['name'], $st, $dt);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(array(
            'status' => 'success',
            'current_batch' => $current_batch,
            'migrations' => array_values(array_map(function($info) use ($applied) {
                $ver = $info['version'];
                return array(
                    'version' => $ver,
                    'name' => $info['name'],
                    'status' => isset($applied[$ver]) ? 'APPLIED' : 'PENDING',
                    'executed_at' => isset($applied[$ver]) ? $applied[$ver]['executed_at'] : null
                );
            }, $all_files))
        ), JSON_PRETTY_PRINT);
    }
    exit(0);
}

if ($action === 'rollback') {
    if ($current_batch <= 0) {
        $msg = "No batches found to rollback.\n";
        if (php_sapi_name() === 'cli') echo $msg; else echo json_encode(array('status' => 'info', 'message' => $msg));
        exit(0);
    }
    $target_res = $mysqli->query("SELECT `version`, `name` FROM `schema_migrations` WHERE `batch` = $current_batch ORDER BY `version` DESC");
    $rolled_back = array();
    if ($target_res) {
        while ($target = $target_res->fetch_assoc()) {
            $ver = $target['version'];
            if (isset($all_files[$ver])) {
                require_once $all_files[$ver]['path'];
                $cls = $all_files[$ver]['class'];
                if (class_exists($cls)) {
                    $inst = new $cls();
                    if (method_exists($inst, 'down')) {
                        $inst->down($mysqli);
                    }
                }
            }
            $mysqli->query("DELETE FROM `schema_migrations` WHERE `version` = '" . $mysqli->real_escape_string($ver) . "'");
            $rolled_back[] = $ver;
        }
    }
    $msg = "Rolled back batch $current_batch: " . implode(', ', $rolled_back) . "\n";
    if (php_sapi_name() === 'cli') echo $msg; else echo json_encode(array('status' => 'success', 'message' => $msg, 'rolled_back' => $rolled_back));
    exit(0);
}

$pending_count = 0;
$executed = array();
foreach ($all_files as $ver => $info) {
    if (!isset($applied[$ver])) {
        $pending_count++;
        require_once $info['path'];
        $cls = $info['class'];
        if (!class_exists($cls)) {
            $err = "Migration class '$cls' not found in file: " . $info['file'];
            if (php_sapi_name() === 'cli') die("Error: $err\n");
            else die(json_encode(array('status' => 'error', 'message' => $err)));
        }
        $inst = new $cls();
        $start_time = microtime(true);
        $ok = $inst->up($mysqli);
        $duration_ms = (int)round((microtime(true) - $start_time) * 1000);
        $now = date('Y-m-d H:i:s');
        $esc_name = $mysqli->real_escape_string($info['name']);
        $esc_ver = $mysqli->real_escape_string($ver);
        $q_rec = "INSERT INTO `schema_migrations` (`version`, `name`, `batch`, `executed_at`, `execution_time_ms`, `status`) VALUES ('$esc_ver', '$esc_name', $next_batch, '$now', $duration_ms, 'SUCCESS')";
        $mysqli->query($q_rec);
        $executed[] = array(
            'version' => $ver,
            'name' => $info['name'],
            'duration_ms' => $duration_ms
        );
        if (php_sapi_name() === 'cli') {
            echo "Migrated: " . $info['file'] . " (" . $duration_ms . " ms)\n";
        }
    }
}

if (php_sapi_name() === 'cli') {
    if ($pending_count === 0) {
        echo "Database is up to date. No pending migrations.\n";
    } else {
        echo "Successfully executed $pending_count migration(s) in batch $next_batch.\n";
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(array(
        'status' => 'success',
        'pending_count' => $pending_count,
        'batch' => ($pending_count > 0 ? $next_batch : $current_batch),
        'executed' => $executed
    ), JSON_PRETTY_PRINT);
}
