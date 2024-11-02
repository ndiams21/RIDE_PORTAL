<?php
// recall_records.php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

header('Content-Type: application/json');

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$host = 'localhost'; 
$dbname = 'portal'; 
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Define allowed tables and their respective columns
$allowedTables = [
    'completed_research_projects' => ['year', 'reference_number', 'research_project_title', 'lead_researcher', 'co_researcher', 'budget', 'project_duration_months', 'date_started', 'date_completed', 'outputs', 'remarks'],
    'research_project_proposals' => ['year', 'reference_number', 'research_project_title', 'lead_researcher', 'co_researcher', 'budget', 'funding_agency', 'project_duration_months', 'date_moa_signed', 'date_started', 'status', 'remarks'],
    'published_articles' => ['year_of_publication', 'research_title', 'authors_and_affiliations', 'budget', 'funding_agency', 'journal_name', 'indexing_body'],
    'registered_ip_rights' => ['year_granted', 'application_number', 'type_of_ip_right', 'inventor_and_affiliation', 'funding_agency', 'utilization_status']
];

// Get the table name from the GET parameters
$table = isset($_GET['table']) ? $_GET['table'] : '';

if (!array_key_exists($table, $allowedTables)) {
    echo json_encode(['success' => false, 'message' => 'Invalid table specified.']);
    exit;
}

try {
    // Fetch data
    $stmt = $pdo->prepare("SELECT * FROM `$table`");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    // Log the error to the server's error log
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error fetching data: ' . $e->getMessage()]);
}
?>
