<?php
// Sample delete_record.php
header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection settings
$host = 'localhost'; // or your server's IP address
$db_name = 'portal'; // Replace with your database name
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

// Database connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Get the input data
$input = json_decode(file_get_contents('php://input'), true);
$primary_key = $input['primary_key'];
$table = $input['table'];

// Prepare the DELETE statement based on the table
switch ($table) {
    case 'completed_research_projects':
        $sql = "DELETE FROM completed_research_projects WHERE reference_number = ?";
        break;
    case 'research_project_proposals':
        $sql = "DELETE FROM research_project_proposals WHERE reference_number = ?";
        break;
    case 'published_articles':
        $sql = "DELETE FROM published_articles WHERE research_title = ?";
        break;
    case 'registered_ip_rights':
        $sql = "DELETE FROM registered_ip_rights WHERE application_number = ?";
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Unknown table']);
        exit;
}

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $primary_key);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Record deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete record.']);
}

$stmt->close();
$conn->close();
?>
