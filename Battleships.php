<?php session_start(); ?>
<!-- Website showcasing the search, insert, and delete functionality of the Battleship database
Live website can be found here: https://lamp.salisbury.edu/~jmccauley4/dev.php 
Developer Note: My inconsistent use of camel case and snake case is awful, I am immensely sorry-->

<!DOCTYPE html>
<html lang="en">

<!-- Website title-->
<head>
    <title>JJ's Battleships Database</title>
</head>
<body>

<!-- Establishing a connection to the database -->
<?php
if($connection = @mysqli_connect('localhost', 'jmccauley4', 'jmccauley4', 'jmccauley4DB')){
    print '<p>Successfully connected to MySQL.</p>';
}
else {
    print '<p>Connection to MySQL failed.</p>';
}
?>

<!-- Dropdown menu for table in database (table should be remembered on refresh -->
<form method="POST">
    <label for="db_table">Select table:</label>
    <select id="db_table" name="db_table">
        <option value="Classes" <?= ($_SESSION['selectedTable'] ?? '') == 'Classes' ? 'selected' : '' ?>>Classes</option>
        <option value="Ships" <?= ($_SESSION['selectedTable'] ?? '') == 'Ships' ? 'selected' : '' ?>>Ships</option>
        <option value="Battles" <?= ($_SESSION['selectedTable'] ?? '') == 'Battles' ? 'selected' : '' ?>>Battles</option>
        <option value="Outcomes" <?= ($_SESSION['selectedTable'] ?? '') == 'Outcomes' ? 'selected' : '' ?>>Outcomes</option>
    </select>
    <input type="Submit" value="Load Table">
</form>


<!-- Getting table name from dropdown -->
<?php
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['db_table'])){
    $_SESSION['selectedTable'] = $_POST['db_table']; // Retrieving the table
    $selectedTable = $_SESSION['selectedTable'];
    $sql_query = "SELECT * FROM " . $selectedTable;
    /* Retrieve the column names of the chosen table from the database */
    $selectedTable = $_SESSION['selectedTable'];
    $sql_col_query = "SHOW COLUMNS FROM " . $selectedTable;
    $result_col_query = mysqli_query($connection, $sql_col_query);
    $table_columns = [];
    if(mysqli_num_rows($result_col_query) > 0){ 
        while($row = mysqli_fetch_assoc($result_col_query)){  // Looping through all results of the query
            $table_columns[] = htmlspecialchars($row['Field']);
        }
    }
    else{  // No results found from the query (should never happen)
        print '<p> Please choose a valid table from the database </p>';
    }
    $selectedTable = $_SESSION['selectedTable'];
}
?>

<!-- Defining PHP wrapper functions -->
<?php
// Wrapper function to add a dropdown with a specified array of columns
function generateDropdown($table_columns){
    //$col_dropdown = '<form method="POST">';
    $col_dropdown = '<label for="column_name"> WHERE: </label>';
    $col_dropdown .= '<select id="column_name" name="column_name">';
    // Add each column to the dropdown
    foreach ($table_columns as $col_name ){
        $col_dropdown .= '<option value="' . htmlspecialchars($col_name) . '">' . htmlspecialchars($col_name) .'</option>';
    }
    $col_dropdown .= '</select>';
    //$col_dropdown .= '<input type="submit" value="Submit Column">';
    //$col_dropdown .= '</form>';
    return $col_dropdown;
}

// Wrapper function to print an input field with a POST request (parameter being variable name)
function printInputField($inputName = 'whereClause', $hint=''){
    //$input_field = '<form method="POST">';
    $input_field = '<input type="text" name="' . $inputName . '"  placeholder="' . $hint . '">';
    //$input_field .= '</form>';
    print $input_field;
}

// Converts the user input to the correct datatype (string or number)
function formatValue($val){
    if(is_numeric($val)){
        return $val + 0;  // Forcing numeric conversion
    }
    else{
        return "'" . addslashes((string)$val) ."'";
    }
}
?>

<?php
/* Ensuring that the table is correctly loaded*/
$selectedTable = $_SESSION['selectedTable'] ?? null;
$table_columns = [];
if ($selectedTable !== null) {
    $sql_col_query = "SHOW COLUMNS FROM " . $selectedTable;
    $result_col_query = mysqli_query($connection, $sql_col_query);

    if ($result_col_query && mysqli_num_rows($result_col_query) > 0) {
        while ($row = mysqli_fetch_assoc($result_col_query)) {
            $table_columns[] = htmlspecialchars($row['Field']);
        }
    } else {
        print "<p>⚠️ Could not load columns from table: $selectedTable</p>";
    }
}
?>

<!-- Creating the SELECT section -->
 <h1>SELECT</h1>
 <br>
 <?php    
    /* Creating a dropdown menu for the different columns */
    print '<form method="POST" action="">';  // Starting the form
    // Ensure that the table properly loads in
    print '<input type="hidden" name="db_table" value="' . htmlspecialchars($_SESSION['selectedTable'] ?? '') . '">';
    $dropdown_html = generateDropdown($table_columns);
    if(isset($dropdown_html)){
        print $dropdown_html;  // Print the dropdown
    }
    print '=';
    /* Creating a submit button that retrieves variables from the POST request */
    printInputField();
    print '<input type="submit" name="sSubmit" value="Query">';
    print '</form>';  // Ending the form
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $whereClause = $_POST["whereClause"];
        $selectedColumn = $_POST["column_name"];
        // Setting the query variable
        if(isset($selectedColumn)) {
            $sql_query = "SELECT " . $selectedColumn . " FROM " . $selectedTable;
            if(isset($whereClause) && $whereClause != "") {  //If the where Clause is filled out
                $sql_query .= " WHERE ". $whereClause;
            }
        }
    }

?>

<!-- Creating the INSERT section -->
 <h1>INSERT</h1>
 <?php
    /* Creating various columns for the different column attributes*/
    $i = 0;  // Creating index for naming inputboxes
    $inputName = '';
    $inputNames[] = '';
    $printedInputFields = '<form method="POST"';
    $printedInputFields .= '<input type="hidden" name="db_table" value="' . htmlspecialchars($_SESSION['selectedTable'] ?? '') . '">';
    // Looping through each column to create text (label) and input box
    foreach($table_columns as $col_name){
        $printedInputFields .= "<td><strong>" . htmlspecialchars($col_name) . "</strong></td>"; // Print attribute name
        $inputName = ((htmlspecialchars($col_name)) . $i);  // Generate unique name for input box
        $printedInputFields .= '<input type="text" name="' . $inputName . '"  placeholder="' . $hint . '">';  // Print input box
        $inputNames .= $inputName;  // Add name to list for POST retrieval later
        $i++;  // Incremement index for future naming purposes
        $printedInputFields .= "</br>";
    }
    // Creating submit button
    $printedInputFields .= '<input type="submit" name="iSubmit" value="Query">';
    $printedInputFields .= '</form><br>';

    // Print everything and whatnot
    print $printedInputFields;

    // Pulling all the values from the POST request for the query
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // Check all the attribute input boxes to ensure that 
        $isMissingFields = false;  // Flag to indicate if any fields are missing
        $validFields = 0;
        //NOTE: Loop through and check, changing flag if missing
        foreach($inputNames as $name){
            if(isset($name) && $name != '') {
                $validFields++;
            }
            else{  // Field is not valid, change flag and break
                $isMissingFields = true;
                break;
            }
        }
        // Setting the query variable
        if(count($inputNames) == $validFields && $isMissingFields == false) {  // Somewhat redundant check
            $sql_query = "SELECT " . $selectedColumn . " FROM " . $selectedTable;
            if(isset($whereClause) && $whereClause != "") {  //If the where Clause is filled out
                $sql_query .= " WHERE ". $whereClause;
            }
        }
    }
 ?>

 <!-- Creating the DELETE section -->
 <h1>DELETE</h1>
 <?php

 ?>

 <!-- Creating the Results section -->
<h1><strong><U>Query Results</U></strong></h1>
<!-- Show the query results in a table-like format -->
<?php
    // Get the query result
    if(isset($sql_query)){
        $result = mysqli_query($connection, $sql_query);
        if(!$result){
            print "❌ Query failed: " . mysqli_error($connection);
        }
        else{
            // Begin making the table
            $empty = false;
            $tableOutput = "<table border='2'>";
            $tableOutput .= "<thead>";
            $tableOutput .= "<tr>";
            // Add all the column names
            foreach($table_columns as $col_name){
                $tableOutput .= "<td><strong>" . htmlspecialchars($col_name) . "</strong></td>"; // Assign proper name to column
            }
            $tableOutput .= "</tr>";
            $tableOutput .= "</thead>";

            // Add in the rows of the table
            if(mysqli_num_rows($result) > 0){ 
                // Loop through all results of the query
                while($row = mysqli_fetch_assoc($result)){
                    $tableOutput .= "<tr>";
                    // Loop through all rows of the query
                    foreach($table_columns as $col_name){
                        $tableOutput .= "<td>" . htmlspecialchars($row[$col_name]) . "</td>"; // Assign proper name to column
                    }
                    $tableOutput .= "</tr>";
                }
            }
            else{  // No results found from the query (should never happen)
                print '<p> ⚠️ No results found. </p>';
                $empty = true;
            }
            $tableOutput .= "</table>";

            if($empty == false){
                print $tableOutput;  // Print the final table
            }
        }
    }
?>

</body>
</html>