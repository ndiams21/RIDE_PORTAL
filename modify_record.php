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
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $form_id = $_POST['form_id']; 
        $primary_key = $_POST['primary_key'] ?? null;
        
        if (!$primary_key) {
            echo json_encode(['success' => false, 'message' => 'Primary key is required.']);
            exit;
        }

        switch ($form_id) { 
            case 'modify_form_1':
                $sql = "UPDATE completed_research_projects 
                        SET year = :year,
                            research_project_title = :research_project_title, 
                            lead_researcher = :lead_researcher, 
                            co_researcher = :co_researcher, 
                            budget = :budget, 
                            project_duration_months = :project_duration_months, 
                            date_completed = :date_completed, 
                            date_started = :date_started, 
                            outputs = :outputs, 
                            remarks = :remarks 
                        WHERE reference_number = :primary_key";
                break;

            case 'modify_form_2':
                $sql = "UPDATE research_project_proposals 
                        SET year = :year,
                            research_project_title = :research_project_title, 
                            lead_researcher = :lead_researcher, 
                            co_researcher = :co_researcher, 
                            budget = :budget,
                            funding_agency = :funding_agency, 
                            project_duration_months = :project_duration_months, 
                            date_moa_signed = :date_moa_signed, 
                            date_started = :date_started, 
                            status = :status, 
                            remarks = :remarks 
                        WHERE reference_number = :primary_key";
                break;

            case 'modify_form_3':
                $sql = "UPDATE published_articles 
                        SET year_of_publication = :year_of_publication,
                            authors_and_affiliations = :authors_and_affiliations, 
                            budget = :budget,
                            funding_agency = :funding_agency,
                            journal_name = :journal_name, 
                            indexing_body = :indexing_body 
                        WHERE research_title = :primary_key";
                break;

            case 'modify_form_4':
                $sql = "UPDATE registered_ip_rights 
                        SET year_granted = :year_granted,
                            type_of_ip_right = :type_of_ip_right, 
                            inventor_and_affiliation = :inventor_and_affiliation,
                            funding_agency = :funding_agency,
                            utilization_status = :utilization_status 
                        WHERE application_number = :primary_key";
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Invalid form ID.']);
                exit;
        }

        $stmt = $pdo->prepare($sql);
        
        // Bind parameters based on form ID
        switch ($form_id) {
            case 'modify_form_1':
                $stmt->bindParam(':year', $_POST['year']);
                $stmt->bindParam(':research_project_title', $_POST['research_project_title']);
                $stmt->bindParam(':lead_researcher', $_POST['lead_researcher']);
                $stmt->bindParam(':co_researcher', $_POST['co_researcher']);
                $stmt->bindParam(':budget', $_POST['budget']);
                $stmt->bindParam(':project_duration_months', $_POST['project_duration_months']);
                $stmt->bindParam(':date_completed', $_POST['date_completed']);
                $stmt->bindParam(':date_started', $_POST['date_started']);
                $stmt->bindParam(':outputs', $_POST['outputs']);
                $stmt->bindParam(':remarks', $_POST['remarks']);
                break;

            case 'modify_form_2':
                $stmt->bindParam(':year', $_POST['year']);
                $stmt->bindParam(':research_project_title', $_POST['research_project_title']);
                $stmt->bindParam(':lead_researcher', $_POST['lead_researcher']);
                $stmt->bindParam(':co_researcher', $_POST['co_researcher']);
                $stmt->bindParam(':budget', $_POST['budget']);
                $stmt->bindParam(':funding_agency', $_POST['funding_agency']);
                $stmt->bindParam(':project_duration_months', $_POST['project_duration_months']);
                $stmt->bindParam(':date_moa_signed', $_POST['date_moa_signed']);
                $stmt->bindParam(':date_started', $_POST['date_started']);
                $stmt->bindParam(':status', $_POST['status']);
                $stmt->bindParam(':remarks', $_POST['remarks']);
                break;

            case 'modify_form_3':
                $stmt->bindParam(':year_of_publication', $_POST['year_of_publication']);
                $stmt->bindParam(':authors_and_affiliations', $_POST['authors_and_affiliations']);
                $stmt->bindParam(':budget', $_POST['budget']);
                $stmt->bindParam(':funding_agency', $_POST['funding_agency']);
                $stmt->bindParam(':journal_name', $_POST['journal_name']);
                $stmt->bindParam(':indexing_body', $_POST['indexing_body']);
                break;

            case 'modify_form_4':
                $stmt->bindParam(':year_granted', $_POST['year_granted']);
                $stmt->bindParam(':type_of_ip_right', $_POST['type_of_ip_right']);
                $stmt->bindParam(':inventor_and_affiliation', $_POST['inventor_and_affiliation']);
                $stmt->bindParam(':funding_agency', $_POST['funding_agency']);
                $stmt->bindParam(':utilization_status', $_POST['utilization_status']);
                break;
        }

        $stmt->bindParam(':primary_key', $primary_key);

        if ($stmt->execute()) {
            // Fetch the updated record after the update
            $updatedRecord = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'message' => 'Record updated successfully!', 'data' => $updatedRecord]);
        }
         else {
            echo json_encode(['success' => false, 'message' => 'Failed to update record.']);
        }
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
$pdo = null;

?>
