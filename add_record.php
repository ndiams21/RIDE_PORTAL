<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$db_name = 'portal';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$allowedTables = [
    'completed_research_projects' => ['year', 'reference_number', 'research_project_title', 'lead_researcher', 'co_researcher', 'budget', 'project_duration_months', 'date_started', 'date_completed', 'outputs', 'remarks'],
    'research_project_proposals' => ['year', 'reference_number', 'research_project_title', 'lead_researcher', 'co_researcher', 'budget', 'funding_agency', 'project_duration_months', 'date_moa_signed', 'date_started', 'status', 'remarks'],
    'published_articles' => ['year_of_publication', 'research_title', 'authors_and_affiliations', 'budget', 'funding_agency', 'journal_name', 'indexing_body'],
    'registered_ip_rights' => ['year_granted', 'application_number', 'type_of_ip_right', 'inventor_and_affiliation', 'funding_agency', 'utilization_status']
];

$form_id = isset($_POST['form_id']) ? $_POST['form_id'] : '';

$form_mappings = [
    'addForm_1' => 'completed_research_projects',
    'addForm_2' => 'research_project_proposals',
    'addForm_3' => 'published_articles',
    'addForm_4' => 'registered_ip_rights'
];

if (!array_key_exists($form_id, $form_mappings)) {
    echo json_encode(['success' => false, 'message' => 'Invalid form ID.']);
    exit;
}

$table = $form_mappings[$form_id];
$columns = $allowedTables[$table];
$data = [];

foreach ($columns as $column) {
    $data[$column] = isset($_POST[$column]) ? trim($_POST[$column]) : null;

    // Check if column is budget and format it
    if ($column === 'budget' && $data[$column] !== null) {
        // Remove commas for safe database storage and add two decimal places
        $data[$column] = number_format(str_replace(',', '', $data[$column]), 2, '.', ',');
    }
}

$columns_sql = implode(", ", array_keys($data));
$placeholders = ":" . implode(", :", array_keys($data));
$sql = "INSERT INTO `$table` ($columns_sql) VALUES ($placeholders)";

try {
    $stmt = $pdo->prepare($sql);
    foreach ($data as $key => &$value) {
        $stmt->bindParam(":$key", $value);
    }
    
    if ($stmt->execute()) {
        $lastId = $pdo->lastInsertId();
        $newRecord = $data;
        $newRecord['id'] = $lastId;

        echo json_encode(['success' => true, 'message' => 'Record added successfully.', 'data' => $newRecord]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add record.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
