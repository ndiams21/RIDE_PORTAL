<?php
// project_dashboard.php

// Define an array of allowed IP addresses for administrative access
$allowed_ips = ['::1', '192.168.1.50']; // replace with your allowed IPs

// Get the user's IP address
$user_ip = $_SERVER['REMOTE_ADDR'];

// Check if the user's IP is in the allowed list
if (in_array($user_ip, $allowed_ips)) {
    // User is authorized, proceed to show the dashboard
    // Include your dashboard code here
    // echo "Welcome to the admin dashboard!";
    // Your existing dashboard code goes here
} else {
    // User is not authorized, redirect to the visitor dashboard
    header('Location: visitor_dashboard.php'); // Redirect to the visitor dashboard
    exit(); // Stop execution after redirecting
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Portal Dashboard</title>
    <link rel="stylesheet" href="http://192.168.1.50/css/admin_dashboard.css">
    <link rel="icon" type="image/x-icon" href="http://192.168.1.50/uploads/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
</head>
<body>
    <div class="container">
        <header>
            <!-- <img src="http://192.168.1.50/uploads/ride.png" alt="Description of the image"> -->
            <h1>RESEARCH, INNOVATION, DEVELOPEMENT, AND EXTENSION DASHBOARD</h1>
            <!-- <img src="http://192.168.1.50/uploads/norsu.png" alt="Description of the image"> -->
        </header>
        <main>
            <div class="tabs" id="tabs">
                <button id="completed_research_projects_tab" onclick="switchTable('completed_research_projects', this)" class="active-tab">Completed Research Projects</button>
                <button id="research_project_proposals_tab" onclick="switchTable('research_project_proposals', this)">Research Project Proposals</button>
                <button id="published_articles_tab" onclick="switchTable('published_articles', this)">Published Articles</button>
                <button id="registered_ip_rights_tab" onclick="switchTable('registered_ip_rights', this)">Registered IPRs</button>
            </div>

            <!-- Completed Research Projects Table -->
             
            <div id="completed_research_projects" class="table-container active">
                <h3>Completed Research Projects</h3>
                <!-- Add this inside each table-container div -->
                <div class="search-container">
                    <input type="text" class="searchInput" placeholder="Search..." onkeyup="searchTable()">
                </div>

                <div id="completed_research_projects_loading">Loading...</div>
                <table style="display: none;">
                    <thead>
                        <tr>
                            <th onclick="sortTable(0, 'completed_research_projects')">Year</th>
                            <th onclick="sortTable(1, 'completed_research_projects')">Reference Number</th>
                            <th onclick="sortTable(2, 'completed_research_projects')">Research Project Title</th>
                            <th onclick="sortTable(3, 'completed_research_projects')">Lead Researcher</th>
                            <th onclick="sortTable(4, 'completed_research_projects')">Co-Researcher</th>
                            <th onclick="sortTable(5, 'completed_research_projects')">Budget</th>
                            <th onclick="sortTable(6, 'completed_research_projects')">Project Duration (months)</th>
                            <th onclick="sortTable(7, 'completed_research_projects')">Date Started</th>
                            <th onclick="sortTable(8, 'completed_research_projects')">Date Completed</th>
                            <th onclick="sortTable(9, 'completed_research_projects')">Outputs</th>
                            <th onclick="sortTable(10, 'completed_research_projects')">Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="completed_research_projects_body">
                        <!-- Data will be inserted here via JavaScript -->
                    </tbody>
                </table>
            </div>
             


            <!-- Research Project Proposals -->
            <div id="research_project_proposals" class="table-container" style="display: none;">
                <h3>Research Project Proposals</h3>
                <div class="search-container">
                    <input type="text" class="searchInput" placeholder="Search..." onkeyup="searchTable()">
                </div>
                <div id="research_project_proposals_loading">Loading...</div>
                <table style="display: none;">
                    <thead>
                        <tr>
                            <th onclick="sortTable(0, 'research_project_proposals')">Year</th>
                            <th onclick="sortTable(1, 'research_project_proposals')">Reference Number</th>
                            <th onclick="sortTable(2, 'research_project_proposals')">Research Project Title</th>
                            <th onclick="sortTable(3, 'research_project_proposals')">Lead Researcher</th>
                            <th onclick="sortTable(4, 'research_project_proposals')">Co-Researcher</th>
                            <th onclick="sortTable(5, 'research_project_proposals')">Budget</th>
                            <th onclick="sortTable(6, 'research_project_proposals')">Funding Agency</th>
                            <th onclick="sortTable(7, 'research_project_proposals')">Project Duration (months)</th>
                            <th onclick="sortTable(8, 'research_project_proposals')">Date MOA Signed</th>
                            <th onclick="sortTable(9, 'research_project_proposals')">Date Started</th>
                            <th onclick="sortTable(10, 'research_project_proposals')">Status</th>
                            <th onclick="sortTable(11, 'research_project_proposals')">Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="research_project_proposals_body">
                        <!-- Data will be inserted here via JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Published Articles -->
            <div id="published_articles" class="table-container" style="display: none;">
                <h3>Published Articles</h3>
                <div class="search-container">
                    <input type="text" class="searchInput" placeholder="Search..." onkeyup="searchTable()">
                </div>
                <div id="published_articles_loading">Loading...</div>
                <table style="display: none;">
                    <thead>
                        <tr>
                            <th onclick="sortTable(0, 'published_articles')">Year of Publication</th>
                            <th onclick="sortTable(1, 'published_articles')">Research Title</th>
                            <th onclick="sortTable(2, 'published_articles')">Authors and Affiliations</th>
                            <th onclick="sortTable(3, 'published_articles')">Budget</th>
                            <th onclick="sortTable(4, 'published_articles')">Funding Agency</th>
                            <th onclick="sortTable(5, 'published_articles')">Journal Name</th>
                            <th onclick="sortTable(6, 'published_articles')">Indexing Body</th>
                        </tr>
                    </thead>
                    <tbody id="published_articles_body">
                        <!-- Data will be inserted here via JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Registered IPRs -->
            <div id="registered_ip_rights" class="table-container" style="display: none;">
                <h3>Registered Intellectual Property Rights (IPRs)</h3>
                <div class="search-container">
                    <input type="text" class="searchInput" placeholder="Search..." onkeyup="searchTable()">
                </div>
                <div id="registered_ip_rights_loading">Loading...</div>
                <table style="display: none;">
                    <thead>
                        <tr>
                            <th onclick="sortTable(0, 'registered_ip_rights')">Year Granted</th>
                            <th onclick="sortTable(1, 'registered_ip_rights')">Application Number</th>
                            <th onclick="sortTable(2, 'registered_ip_rights')">Type of IP Right</th>
                            <th onclick="sortTable(3, 'registered_ip_rights')">Inventor and Affiliation</th>
                            <th onclick="sortTable(4, 'registered_ip_rights')">Funding Agency</th>
                            <th onclick="sortTable(5, 'registered_ip_rights')">Utilization Status</th>
                        </tr>
                    </thead>
                    <tbody id="registered_ip_rights_body">
                        <!-- Data will be inserted here via JavaScript -->
                    </tbody>
                </table>
            </div>

            <!---->
            <div id="addRecordForm" class="form-container" style="display: none;">
                <h3>Add New Record</h3> 
                <form id="addForm_1" class="add_form" style="display: none;">
                    <label for="year">Year:</label>
                    <input type="text" id="year" name="year" placeholder="20xx"><br>

                    <label for="reference_number">Reference Number:</label>
                    <input type="text" id="reference_number" name="reference_number" placeholder="RIDE Project #: 20xx-xx"><br>

                    <label for="research_project_title">Research Project Title:</label>
                    <input type="text" id="research_project_title" name="research_project_title" placeholder="Project xxx"><br>

                    <label for="lead_researcher">Lead Researcher:</label>
                    <input type="text" id="lead_researcher" name="lead_researcher" placeholder="Dr. John Doe"><br>

                    <label for="co_researcher">Co-Researcher:</label>
                    <input type="text" id="co_researcher" name="co_researcher" placeholder="Dr. Mary Jane"><br>

                    <label for="budget">Budget:</label>
                    <input type="text" id="budget" name="budget" placeholder="0.00" oninput="formatCurrency(this)" />
                    <!-- <input type="text" id="budget" name="budget"><br> -->

                    <label for="project_duration_months">Project Duration (months):</label>
                    <input type="text" id="project_duration_months" name="project_duration_months" placeholder="12 months"><br>

                    <label for="date_started">Date Started:</label>
                    <input type="date" id="date_started" name="date_started"><br>

                    <label for="date_completed">Date Completed:</label>
                    <input type="date" id="date_completed" name="date_completed"><br>

                    <label for="outputs">Outputs:</label>
                    <input id="outputs" name="outputs">

                    <label for="remarks">Remarks:</label>
                    <input id="remarks" name="remarks">

                    <button type="submit" id="submitButton">Submit</button>
                    <button type="button" id="cancelButton">Cancel</button>
                </form>
                <!---->                            
                <form id="addForm_2" class="add_form" style="display: none;">
                    <label for="year">Year:</label>
                    <input type="text" id="year" name="year" placeholder="20xx"><br>

                    <label for="reference_number">Reference Number:</label>
                    <input type="text" id="reference_number" name="reference_number" placeholder="RIDE Project #: 20xx-xx"><br>

                    <label for="research_project_title">Research Project Title:</label>
                    <input type="text" id="research_project_title" name="research_project_title" placeholder="Project xxx"><br>

                    <label for="lead_researcher">Lead Researcher:</label>
                    <input type="text" id="lead_researcher" name="lead_researcher" placeholder="Dr. John Doe"><br>

                    <label for="co_researcher">Co-Researcher:</label>
                    <input type="text" id="co_researcher" name="co_researcher" placeholder="Dr. Mary Jane"><br>

                    <label for="budget">Budget:</label>
                    <input type="text" id="budget" name="budget" placeholder="0.00" oninput="formatCurrency(this)" />
                    <!-- <input type="text" id="budget" name="budget"><br> -->

                    <label for="funding_agency">Funding Agency:</label>
                    <input type="text" id="funding_agency" name="funding_agency" placeholder="CORP Funding"><br>

                    <label for="project_duration_months">Project Duration (months):</label>
                    <input type="text" id="project_duration_months" name="project_duration_months" placeholder="12 months"><br>

                    <label for="date_moa_signed">Date MOA Signed:</label>
                    <input type="date" id="date_moa_signed" name="date_moa_signed"><br>

                    <label for="date_started">Date Started:</label>
                    <input type="date" id="date_started" name="date_started"><br>

                    <label for="status">Status:</label>
                    <input id="status" name="status">

                    <label for="remarks">Remarks:</label>
                    <input id="remarks" name="remarks">

                    <button type="submit" id="submitButton">Submit</button>
                    <button type="button" id="cancelButton">Cancel</button>
                </form>
                <!---->                            
                <form id="addForm_3" class="add_form" style="display: none;">
                    <label for="year_of_publication">Year of Publication:</label>
                    <input type="text" id="year_of_publication" name="year_of_publication" placeholder="20xx"><br>

                    <label for="research_title">Research Title:</label>
                    <input type="text" id="research_title" name="research_title" placeholder="Project XXX"><br>

                    <label for="authors_and_affiliations">Authors and Affiliations:</label>
                    <input type="text" id="authors_and_affiliations" name="authors_and_affiliations" placeholder="Dr. John Doe & Dr. Mary Jane"><br>

                    <label for="budget">Budget:</label>
                    <input type="text" id="budget" name="budget" placeholder="0.00" oninput="formatCurrency(this)" />
                    <!-- <input type="text" id="budget" name="budget"><br> -->

                    <label for="funding_agency">Funding Agency:</label>
                    <input type="text" id="funding_agency" name="funding_agency" placeholder="CORP Funding"><br>

                    <label for="journal_name">Journal Name:</label>
                    <input type="text" id="journal_name" name="journal_name"><br>

                    <label for="indexing_body">Indexing Body:</label>
                    <input type="text" id="indexing_body" name="indexing_body"><br>

                    <button type="submit" id="submitButton">Submit</button>
                    <button type="button" id="cancelButton">Cancel</button>
                </form>
                <!---->
                <form id="addForm_4" class="add_form" style="display: none;">
                    <label for="year_granted">Year Granted:</label>
                    <input type="text" id="year_granted" name="year_granted" placeholder="20xx"><br>

                    <label for="application_number">Application Number:</label>
                    <input type="text" id="application_number" name="application_number" placeholder="APPxxx"><br>
                    
                    <label for="type_of_ip_right">Type of IP right:</label>
                    <input type="text" id="type_of_ip_right" name="type_of_ip_right" placeholder="Patent"><br>
                    
                    <label for="inventor_and_affiliation">Inventor and Affiliation:</label>
                    <input type="text" id="inventor_and_affiliation" name="inventor_and_affiliation" placeholder="Dr. John Doe & Dr. Mary Jane"><br>
                    
                    <label for="funding_agency">Funding Agency:</label>
                    <input type="text" id="funding_agency" name="funding_agency" placeholder="CORP Funding"><br>

                    <label for="utilization_status">Utilization Status:</label>
                    <input type="text" id="utilization_status" name="utilization_status"><br>
        
                    <button type="submit" id="submitButton">Submit</button>
                    <button type="button" id="cancelButton">Cancel</button>
                </form> 
            </div>
            <!---->
            <div id="modify_record_form" class="form-container" style="display: none;">
                <h3>Modify Record</h3> 
                
                <form id="modify_form_1" class="modify_form" style="display: none;">
                    <label for="year">Year:</label>
                    <input type="text" id="year" name="year" placeholder="20xx"><br>

                    <label for="reference_number">Reference Number:</label>
                    <input type="text" id="reference_number" name="reference_number" placeholder="RIDE Project #: 20xx-xx"><br>

                    <label for="research_project_title">Research Project Title:</label>
                    <input type="text" id="research_project_title" name="research_project_title" placeholder="Project xxx"><br>

                    <label for="lead_researcher">Lead Researcher:</label>
                    <input type="text" id="lead_researcher" name="lead_researcher" placeholder="Dr. John Doe"><br>

                    <label for="co_researcher">Co-Researcher:</label>
                    <input type="text" id="co_researcher" name="co_researcher" placeholder="Dr. Mary Jane"><br>

                    <label for="budget">Budget:</label>
                    <input type="text" id="budget" name="budget" placeholder="0.00" oninput="formatCurrency(this)" />
                    <!-- <input type="text" id="budget" name="budget"><br> -->

                    <label for="project_duration_months">Project Duration (months):</label>
                    <input type="text" id="project_duration_months" name="project_duration_months" placeholder="12 months"><br>

                    <label for="date_started">Date Started:</label>
                    <input type="date" id="date_started" name="date_started"><br>

                    <label for="date_completed">Date Completed:</label>
                    <input type="date" id="date_completed" name="date_completed"><br>

                    <label for="outputs">Outputs:</label>
                    <input id="outputs" name="outputs">

                    <label for="remarks">Remarks:</label>
                    <input id="remarks" name="remarks">

                    <button type="submit" id="submitButton">Submit</button>
                    <button type="button" id="cancelButton">Cancel</button>
                </form>

                <form id="modify_form_2" class="modify_form" style="display: none;">
                    <label for="year">Year:</label>
                    <input type="text" id="year" name="year" placeholder="20xx"><br>

                    <label for="reference_number">Reference Number:</label>
                    <input type="text" id="reference_number" name="reference_number" placeholder="RIDE Project #: 20xx-xx"><br>

                    <label for="research_project_title">Research Project Title:</label>
                    <input type="text" id="research_project_title" name="research_project_title" placeholder="Project xxx"><br>

                    <label for="lead_researcher">Lead Researcher:</label>
                    <input type="text" id="lead_researcher" name="lead_researcher" placeholder="Dr. John Doe"><br>

                    <label for="co_researcher">Co-Researcher:</label>
                    <input type="text" id="co_researcher" name="co_researcher" placeholder="Dr. Mary Jane"><br>

                    <label for="budget">Budget:</label>
                    <input type="text" id="budget" name="budget" placeholder="0.00" oninput="formatCurrency(this)" />
                    <!-- <input type="text" id="budget" name="budget"><br> -->

                    <label for="funding_agency">Funding Agency:</label>
                    <input type="text" id="funding_agency" name="funding_agency" placeholder="CORP Funding"><br>

                    <label for="project_duration_months">Project Duration (months):</label>
                    <input type="text" id="project_duration_months" name="project_duration_months" placeholder="12 months"><br>

                    <label for="date_moa_signed">Date MOA Signed:</label>
                    <input type="date" id="date_moa_signed" name="date_moa_signed"><br>

                    <label for="date_started">Date Started:</label>
                    <input type="date" id="date_started" name="date_started"><br>

                    <label for="status">Status:</label>
                    <input id="status" name="status">

                    <label for="remarks">Remarks:</label>
                    <input id="remarks" name="remarks">

                    <button type="submit" id="submitButton">Submit</button>
                    <button type="button" id="cancelButton">Cancel</button>
                </form>
                <!---->                            
                <form id="modify_form_3" class="modify_form" style="display: none;">
                    <label for="year_of_publication">Year of Publication:</label>
                    <input type="text" id="year_of_publication" name="year_of_publication"><br>

                    <label for="research_title">Research Title:</label>
                    <input type="text" id="research_title" name="research_title" placeholder="Project XXX"><br>

                    <label for="authors_and_affiliations">Authors and Affiliations:</label>
                    <input type="text" id="authors_and_affiliations" name="authors_and_affiliations" placeholder="Dr. John Doe & Dr. Mary Jane"><br>

                    <label for="budget">Budget:</label>
                    <input type="text" id="budget" name="budget" placeholder="0.00" oninput="formatCurrency(this)" />
                    <!-- <input type="text" id="budget" name="budget"><br> -->

                    <label for="funding_agency">Funding Agency:</label>
                    <input type="text" id="funding_agency" name="funding_agency" placeholder="CORP Funding"><br>

                    <label for="journal_name">Journal Name:</label>
                    <input type="text" id="journal_name" name="journal_name"><br>

                    <label for="indexing_body">Indexing Body:</label>
                    <input type="text" id="indexing_body" name="indexing_body"><br>

                    <button type="submit" id="submitButton">Submit</button>
                    <button type="button" id="cancelButton">Cancel</button>
                </form>
                <!---->
                <form id="modify_form_4" class="modify_form" style="display: none;">
                    <label for="year_granted">Year Granted:</label>
                    <input type="text" id="year_granted" name="year_granted" placeholder="20xx"><br>

                    <label for="application_number">Application Number:</label>
                    <input type="text" id="application_number" name="application_number" placeholder="APPxxx"><br>
                    
                    <label for="type_of_ip_right">Type of IP right:</label>
                    <input type="text" id="type_of_ip_right" name="type_of_ip_right" placeholder="Patent"><br>
                    
                    <label for="inventor_and_affiliation">Inventor and Affiliation:</label>
                    <input type="text" id="inventor_and_affiliation" name="inventor_and_affiliation" placeholder="Dr. John Doe & Dr. Mary Jane"><br>
                    
                    <label for="funding_agency">Funding Agency:</label>
                    <input type="text" id="funding_agency" name="funding_agency" placeholder="CORP Funding"><br>

                    <label for="utilization_status">Utilization Status:</label>
                    <input type="text" id="utilization_status" name="utilization_status"><br>
        
                    <button type="submit" id="submitButton">Submit</button>
                    <button type="button" id="cancelButton">Cancel</button>
                </form> 
            </div>
        </main>
    </div>
    <!-- Include jQuery and DataTables library -->
    <script src="http://192.168.1.50/js/admin_dashboard.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
</body>
</html>
