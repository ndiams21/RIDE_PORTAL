// Initialize cache and selected row data
const tableDataCache = {};
let selectedRowData = null; // Store data of the selected row for update/delete

// Select DOM elements
const tabs = document.querySelectorAll('div#tabs button');
const addRecordForm = document.getElementById('addRecordForm');
const addRecordForms = document.querySelectorAll('#addRecordForm form');
let current_table = '#completed_research_projects';
let target_table = '';
var primary_key = '';
var update_record_form = document.getElementById('modify_record_form');
var modify_record_forms = document.querySelectorAll('#modify_record_form form');
var form_containers = document.querySelectorAll('.form-container');


// Function to fetch data for a specific table
async function fetchData(tableName) {
    const loadingDiv = document.getElementById(`${tableName}_loading`);
    const table = document.querySelector(`#${tableName} table`);
    loadingDiv.style.display = 'block';
    table.style.display = 'none';

    try {
        const response = await fetch(`recall_records.php?table=${encodeURIComponent(tableName)}`);
        
        if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('Fetch result:', result); // Debugging

        if (result.success) {
            if (result.data && result.data.length > 0) {
                tableDataCache[tableName] = result.data;
                populateTable(tableName, result.data);
                loadingDiv.style.display = 'none';
                table.style.display = 'table';
            } else {
                loadingDiv.textContent = 'No data available.';
                console.error('No data found:', result.message);
            }
        } else {
            loadingDiv.textContent = result.message;
            console.error('Server error:', result.message);
        }
    } catch (error) {
        loadingDiv.textContent = 'An error occurred while fetching data.';
        console.error('Error fetching data:', error);
    }
}

// Function to populate table body with data
function populateTable(tableName, data) {
    const tbody = document.getElementById(`${tableName}_body`);
    tbody.innerHTML = ''; // Clear existing rows

    //console.log(`Populating table: ${tableName} with data:`, data); // Debugging

    if (data.length === 0) {
        //console.log('No data to display.');
        return;
    }

    // Create a document fragment for better performance
    const fragment = document.createDocumentFragment();

    data.forEach(row => {
        const tr = document.createElement('tr');

        for (const key in row) {
            const td = document.createElement('td');
            td.textContent = row[key];
            tr.appendChild(td);
        }
        
        fragment.appendChild(tr);
    });

    tbody.appendChild(fragment);

    // Initialize DataTables with customized DOM structure
    $(`#${tableName} table`).DataTable({
        destroy: true,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        dom: '<"top"l>rt<"bottom"ip><"clear">', // Customize the position of elements
        // Additional DataTable settings...
    });

    // Add buttons to the DataTable controls
    addControlButtons(tableName);

    // Use event delegation for row clicks (with DataTables API)
    tbody.addEventListener('click', (event) => {
        const clickedRow = event.target.closest('tr');
        if (clickedRow) {
            // Remove highlight from any previously selected row
            const selectedRow = tbody.querySelector('.selected');
            if (selectedRow) {
                selectedRow.classList.remove('selected');
            }

            // Highlight the selected row
            clickedRow.classList.add('selected');

            // Use DataTables API to get the data for the selected row
            const table = $(`#${tableName} table`).DataTable();
            const rowIndex = table.row(clickedRow).index(); // Get the correct row index
            selectedRowData = table.row(rowIndex).data(); // Use DataTables to retrieve the correct row data
            
            console.log("Selected Row Data:", selectedRowData);

            // Set the primary key based on the current table
            if (current_table.slice(1) === 'completed_research_projects' || current_table.slice(1) === 'research_project_proposals') {
                primary_key = selectedRowData[1];
            } else if (current_table.slice(1) === 'published_articles') {
                primary_key = selectedRowData[1];
            } else if (current_table.slice(1) === 'registered_ip_rights') {
                primary_key = selectedRowData[1];
            }

            //console.log(primary_key);

            // Enable action buttons
            document.querySelectorAll(current_table + ' button:not(:first-child)').forEach(button => {
                button.removeAttribute('disabled');
                button.classList.remove('disabled_button');
                button.classList.add('enabled_button');
            });
        }
    });

}

// Function to append a new record to the DataTable
function appendNewRecord(tableName, newRecord) {
    // Get the DataTable instance
    const dataTable = $(`#${tableName} table`).DataTable();

    // Map newRecord properties based on the tableName
    const rowDataMap = {
        'completed_research_projects': [
            newRecord.year,
            newRecord.reference_number,
            newRecord.research_project_title,
            newRecord.lead_researcher,
            newRecord.co_researcher,
            newRecord.budget,
            newRecord.project_duration_months,
            newRecord.date_started,
            newRecord.date_completed,
            newRecord.outputs,
            newRecord.remarks
        ],
        'research_project_proposals': [
            newRecord.year,
            newRecord.reference_number,
            newRecord.research_project_title,
            newRecord.lead_researcher,
            newRecord.co_researcher,
            newRecord.budget,
            newRecord.funding_agency,
            newRecord.project_duration_months,
            newRecord.date_moa_signed,
            newRecord.date_started,
            newRecord.status,
            newRecord.remarks
        ],
        'published_articles': [
            newRecord.year_of_publication,
            newRecord.research_title,
            newRecord.authors_and_affiliations,
            newRecord.budget,
            newRecord.funding_agency,
            newRecord.journal_name,
            newRecord.indexing_body
        ],
        'registered_ip_rights': [
            newRecord.year_granted,
            newRecord.application_number,
            newRecord.type_of_ip_right,
            newRecord.inventor_and_affiliation,
            newRecord.funding_agency,
            newRecord.utilization_status
        ]
    };

    const rowData = rowDataMap[tableName];

    if (!rowData) {
        console.error('Unknown table:', tableName);
        return;
    }
    // Add the new row to the DataTable
    dataTable.row.add(rowData).draw(false); // 'false' to keep the current paging
}

// Function to add control buttons below the DataTable
function addControlButtons(tableName) {
    //console.log(current_table);

    const table = $(`#${tableName} table`).DataTable();

    // Remove existing button container to prevent duplicates
    $(table.table().container()).find('.button-container').remove();

    const addButton = $('<button id="addRecordBtn" class="enabled_button">Add Record</button>').click(add_record);
    // Create update and delete buttons
    const updateButton = $('<button id="updateRecordBtn" class="disabled_button" disabled>Update Record</button>').click(update_record);
    const deleteButton = $('<button id="deleteRecordBtn" class="disabled_button" disabled>Delete Record</button>').click(deleteSelectedRecord);

    // Create a div to contain the buttons
    const buttonContainer = $('<div class="button-container"></div>').append(addButton, updateButton, deleteButton);

    // Append the button container to the DataTable's bottom container
    const bottomContainer = $(table.table().container()).find('.bottom');
    bottomContainer.append(buttonContainer);
}

// Function to switch between tables
function switchTable(tableName, button) {
    // Clear primary_key
    primary_key = '';
    // Clear the search input of the currently active table
    if (current_table) {
        const currentTableContainer = document.querySelector(current_table);
        if (currentTableContainer) {
            const searchInput = currentTableContainer.querySelector('.searchInput');
            if (searchInput) {
                searchInput.value = ''; // Clear the search input
                // Optionally, reset the table rows to be visible
                const activeTable = currentTableContainer.querySelector("table");
                if (activeTable) {
                    const rows = activeTable.getElementsByTagName("tr");
                    for (let i = 1; i < rows.length; i++) { // Start at 1 to skip the header
                        rows[i].style.display = "";
                    }
                }
            }
        }
    }

    // Hide all table containers
    document.querySelectorAll('.table-container').forEach(table => {
        table.classList.remove('active');
        table.style.display = 'none';
    });

    // Remove 'active-tab' from all buttons
    document.querySelectorAll('.tabs button').forEach(btn => {
        btn.classList.remove('active-tab');
    });

    // Show the selected table container and mark the button as active
    const selectedTable = document.getElementById(tableName);
    selectedTable.style.display = 'block';
    selectedTable.classList.add('active');
    button.classList.add('active-tab');

    // Update the current_table variable
    current_table = `#${tableName}`;

    // Hide form containers initially
    document.querySelectorAll('.form-container').forEach(form => {
        form.style.display = 'none';
    });
    document.querySelectorAll('.add_form').forEach(form => {
        form.style.display = 'none';
    });

    // Reset buttons to disabled state
    document.querySelectorAll(current_table + ' button:not(:first-child)').forEach(button => {
        button.setAttribute('disabled', 'disabled');  // Disable button
        button.classList.remove('enabled_button');
        button.classList.add('disabled_button');
    });

    // Fetch the data for the selected table if not already cached
    if (!tableDataCache[tableName]) {
        fetchData(tableName);
    }
}

function add_form_submit(event){
    event.preventDefault();
    // console.log('hello world');

    const form = event.target;
    const formId = form.id;
    const formData = new FormData(form); // Use the form ID to get the specific form data

    switch(formId) {
        case 'addForm_1':
            target_table = 'completed_research_projects';
            break;
        case 'addForm_2':
            target_table = 'research_project_proposals';
            break;
        case 'addForm_3':
            target_table = 'published_articles';
            break;
        case 'addForm_4':
            target_table = 'registered_ip_rights';
            break;
        default:
            console.error('Unknown form ID:', formId);
            return;
    }

    formData.append('form_id', formId);
    
    fetch('add_record.php' , {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(data); // Check the structure of the returned data
            alert(data.message);
        
            // Hide the form and reset it
            addRecordForm.style.display = 'none';
            // form.reset();

            addRecordForms.forEach(function(form) {
                form.style.display = 'none';
                form.reset();
            });

            // Append the new record to the current DataTable
            appendNewRecord(target_table, data.data); // data.data should now contain the new record
        } else {
            console.error(data.message); // Handle error message
            alert(`Error: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An unexpected error occurred.');
    });
}

function update_form_submit(event){
    // event.preventDefault();
    console.log('bye world');
}

// Handler for form submission
// function handleFormSubmit(event) {
//     console.log(primary_key);
//     event.preventDefault(); // Prevent default form submission

//     var what_file = '';

//     const form = event.target;
//     const formId = form.id;
//     const formData = new FormData(form); // Use the form ID to get the specific form data

    
//     if(formId.includes('addForm')){
//         //console.log('add');
//         what_file = 'add_record.php';
//     }
//     else if(formId.includes('modify_form')){
//         //console.log('modify');
//         what_file = 'modify_record.php';
//         formData.append('primary_key', primary_key); // Corrected line
//     }

//     // //Determine target table based on form ID
//     switch(formId) {
//         case 'addForm_1':
//             target_table = 'completed_research_projects';
//             break;
//         case 'addForm_2':
//             target_table = 'research_project_proposals';
//             break;
//         case 'addForm_3':
//             target_table = 'published_articles';
//             break;
//         case 'addForm_4':
//             target_table = 'registered_ip_rights';
//             break;
//         //
//         case 'modify_form_1':
//             target_table = 'completed_research_projects';
//             break;
//         case 'modify_form_2':
//             target_table = 'research_project_proposals';
//             break;
//         case 'modify_form_3':
//             target_table = 'published_articles';
//             break;
//         case 'modify_form_4':
//             target_table = 'registered_ip_rights';
//             break;
//         default:
//             console.error('Unknown form ID:', formId);
//             return;
//     }
//     // Append the form_id to the form data
//     // formData.append('form_id', formId, 'primary_key', primary_key);

//     // Append the form_id and primary_key to the form data
//     // formData.append('form_id', formId);
    
//     fetch(what_file , {
//         method: 'POST',
//         body: formData
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             console.log(data); // Check the structure of the returned data
//             alert(data.message);
        
//             // Hide the form and reset it
//             // addRecordForm.style.display = 'none';
//             // form.reset();



//             modify_record_forms.forEach(function(form){
//                 form.style.display = 'none';
//             });
            
        
//             // Append the new record to the current DataTable
//             appendNewRecord(target_table, data.data); // data.data should now contain the new record
//         } else {
//             console.error(data.message); // Handle error message
//             alert(`Error: ${data.message}`);
//         }
//     })
//     .catch(error => {
//         console.error('Error:', error);
//         alert('An unexpected error occurred.');
//     });
// }

// Function to handle adding a new record
function add_record() {
    addRecordForm.style.display = 'block';
    let activeTab = null; // Initialize a variable to hold the active tab
    // Identify the active tab
    tabs.forEach(function(tab) {
        if (tab.classList.contains('active-tab')) {
            activeTab = tab; // If it has the active class, assign it to activeTab
        }
    });

    //console.log('Active Tab ID:', activeTab.id);
    // Hide all forms initially
    addRecordForms.forEach(function(form) {
        form.style.display = 'none';
    });

    // Display the relevant form based on the active tab
    switch(activeTab.id) {
        case 'completed_research_projects_tab':
            addRecordForms[0].style.display = 'block';
            break;
            
        case 'research_project_proposals_tab':
            addRecordForms[1].style.display = 'block';
            break;
        case 'published_articles_tab':
            addRecordForms[2].style.display = 'block';
            break;
        case 'registered_ip_rights_tab':
            addRecordForms[3].style.display = 'block';
            break;
        default:
            console.error('Unknown active tab:', activeTab.id);
    }
    // Add event listener to cancel buttons
    const add_form_cancel_btns = document.querySelectorAll('#addRecordForm .cancelButton');
    add_form_cancel_btns.forEach(function(btn) {
        btn.onclick = function() {
            // Hide all forms and the main form container
            addRecordForms.forEach(function(form) {
                form.style.display = 'none';
            });
            addRecordForm.style.display = 'none';
        };
    });
    // Attach submit event listeners if not already attached
    addRecordForms.forEach(function(form) {
        if (!form.dataset.listenerAttached) { // Prevent duplicate listeners
            form.addEventListener('submit', add_form_submit);
            form.dataset.listenerAttached = true; // Mark as attached
        }
    });
    scrollToElement();
}

function scrollToElement() {
    form_containers.forEach(function(form) {
        if (form.id === 'addRecordForm' || form.id === 'modify_record_form') {
            form.scrollIntoView({ behavior: "smooth" });
            form.focus();
            console.log(form.id);
        }
    });
}


// Function to update selected record
function update_record() {
    addRecordBtn.classList.remove('enabled_button');
    addRecordBtn.classList.add('disabled_button');
    addRecordBtn.setAttribute('disabled','true');
        
    modify_record_forms.forEach(function(form) {

        modify_record_forms.forEach(function(form) {
            form.style.display = 'none';   
        })

        if (!form.dataset.listenerAttached) { // Prevent duplicate listeners
            form.addEventListener('submit', update_form_submit);
            form.dataset.listenerAttached = true; // Mark as attached
        }
    });

    // const modify_record_form = document.getElementById('modify_record_form');
    modify_record_form.style.display = 'block';
    var what_modify_form = '';

    if (selectedRowData) {
        if(current_table.slice(1) === 'completed_research_projects'){
            form = document.getElementById('#modify_form_1');
            inputs = document.querySelectorAll('#modify_record_form #modify_form_1 input');
            textarea = document.querySelectorAll('#modify_record_form #modify_form_1 textarea');
            modify_record_forms[0].style.display = 'block';
            
            inputs[0].value = selectedRowData[0]; //year 
            inputs[1].value = selectedRowData[1]; //reference_number; //primary key
            inputs[2].value = selectedRowData[2]; //research_project_title;
            inputs[3].value = selectedRowData[3]; //lead_researcher;
            inputs[4].value = selectedRowData[4]; //co_researcher;
            inputs[5].value = selectedRowData[5]; //budget;
            inputs[6].value = selectedRowData[6]; //project_duration_months;
            inputs[7].value = selectedRowData[7]; //date_started;
            inputs[8].value = selectedRowData[8]; //date_completed;
            inputs[9].value = selectedRowData[9]; //date_completed;
            inputs[10].value = selectedRowData[10]; //date_completed;
            // textarea[0].value = selectedRowData[9]; //outputs;
            // textarea[1].value = selectedRowData[10]; //remarks;

            what_modify_form = modify_record_forms[0].id;
            primary_key = selectedRowData[1];
        }
        else if(current_table.slice(1) == 'research_project_proposals'){
            form = document.getElementById('#modify_form_2');
            inputs = document.querySelectorAll('#modify_record_form #modify_form_2 input');
            textarea = document.querySelectorAll('#modify_record_form #modify_form_2 textarea');
            modify_record_forms[1].style.display = 'block';
            
            inputs[0].value = selectedRowData[0]; //year 
            inputs[1].value = selectedRowData[1]; //reference_number; //primary key
            inputs[2].value = selectedRowData[2]; //research_project_title;
            inputs[3].value = selectedRowData[3]; //lead_researcher;
            inputs[4].value = selectedRowData[4]; //co_researcher;
            inputs[5].value = selectedRowData[5]; //budget;
            inputs[6].value = selectedRowData[6]; //funding_agency;
            inputs[7].value = selectedRowData[7]; //project_duration_months;
            inputs[8].value = selectedRowData[8]; //date_moa_signed;
            inputs[9].value = selectedRowData[9]; //date_started;
            inputs[10].value = selectedRowData[10];
            inputs[11].value = selectedRowData[11];
            // textarea[0].value = selectedRowData[10]; //status;
            // textarea[1].value = selectedRowData[11]; //remarks;
            what_modify_form = modify_record_forms[1].id;
            primary_key = selectedRowData[1];
        }
        else if(current_table.slice(1) == 'published_articles'){
            form = document.getElementById('#modify_form_3');
            inputs = document.querySelectorAll('#modify_record_form #modify_form_3 input');
            modify_record_forms[2].style.display = 'block';
            
            inputs[0].value = selectedRowData[0]; //year_of_publication;
            inputs[1].value = selectedRowData[1]; //research_title; //primary key
            inputs[2].value = selectedRowData[2]; //authors_and_affiliations;
            inputs[3].value = selectedRowData[3]; //budget;
            inputs[4].value = selectedRowData[4]; //funding_agency;
            inputs[5].value = selectedRowData[5]; //journal_name;
            inputs[6].value = selectedRowData[6]; //indexing_body;
            what_modify_form = modify_record_forms[2].id;
            primary_key = selectedRowData[1];
        }
        else if(current_table.slice(1) == 'registered_ip_rights'){
            form = document.getElementById('#modify_form_4');
            inputs = document.querySelectorAll('#modify_record_form #modify_form_4 input');
            modify_record_forms[3].style.display = 'block';
            
            inputs[0].value = selectedRowData[0];//year_granted;
            inputs[1].value = selectedRowData[1];//application_number; //primary key
            inputs[2].value = selectedRowData[2];//type_of_ip_right;
            inputs[3].value = selectedRowData[3];//inventor_and_affiliation;
            inputs[4].value = selectedRowData[4];//funding_agency;
            inputs[5].value = selectedRowData[5];//utilization_status;
            what_modify_form = modify_record_forms[3].id;
            primary_key = selectedRowData[1];
        }

    }
    scrollToElement();
}

// Function to delete selected record
function deleteSelectedRecord() {
    if (selectedRowData) {
        // Confirm deletion
        if (!confirm('Are you sure you want to delete this record?')) {
            return;
        }

        // Get the table name from current_table and the primary key for deletion
        const tableName = current_table.substring(1); // Remove '#' from the selector

        //console.log(current_table.slice(1));

        const recordId = primary_key; // Use the primary key for the deletion

        //console.log(primary_key);

        // Send a request to delete the record
        fetch('delete_record.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ primary_key: recordId, table: tableName }) // Pass primary_key and table
        })
        .then(response => response.json())
        .then(data => {
            if(data.success){
                // Remove row from the displayed table
                const dataTable = $(`#${tableName} table`).DataTable();
                dataTable.row('.selected').remove().draw(false);
                selectedRowData = null; // Clear selected row data

                // Disable action buttons
                document.getElementById('updateRecordBtn').disabled = true;
                document.getElementById('deleteRecordBtn').disabled = true;

                alert('Record deleted successfully.');
            } else {
                console.error(data.message);
                alert(`Error: ${data.message}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred while deleting the record.');
        });
    }
}

// Initial fetch for default table
document.addEventListener('DOMContentLoaded', () => {
    fetchData('completed_research_projects');
});

// Search function updated to use class-based search inputs
function searchTable() {
    // Find the currently active table-container
    const activeTableContainer = document.querySelector(".table-container.active");
    
    if (activeTableContainer) {
        // Find the search input within the active table-container
        const input = activeTableContainer.querySelector(".searchInput");
        const filter = input.value.toLowerCase();
        const activeTable = activeTableContainer.querySelector("table");
        
        // Get the rows of the active table
        const rows = activeTable.getElementsByTagName("tr");
        
        for (let i = 1; i < rows.length; i++) { // Start at 1 to skip the header row
            const cells = rows[i].getElementsByTagName("td");
            let rowContainsSearchTerm = false;
            
            // Check each cell for the search term
            for (let j = 0; j < cells.length; j++) {
                const cell = cells[j];
                if (cell && cell.textContent.toLowerCase().includes(filter)) {
                    rowContainsSearchTerm = true;
                    break; // Stop checking further cells in this row
                }
            }
            
            // Show or hide the row based on whether the search term was found
            rows[i].style.display = rowContainsSearchTerm ? "" : "none";
        }
    }
}

function formatCurrency(input) {
    // Remove non-digit characters except for decimal point
    let value = input.value.replace(/[^0-9.]/g, '');

    // Ensure only one decimal point is present
    const decimalIndex = value.indexOf('.');
    if (decimalIndex !== -1) {
        value = value.slice(0, decimalIndex + 3); // Limit to two decimals
    }

    // Parse the number and format with commas and two decimal places
    if (value) {
        let parts = value.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add comma separators
        input.value = parts.join('.'); // Reassemble the number
    }
}