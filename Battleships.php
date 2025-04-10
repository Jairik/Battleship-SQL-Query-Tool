<?php session_start(); ?>
<!-- Website showcasing the search, insert, and delete functionality of the Battleship database
Live website can be found here: https://lamp.salisbury.edu/~jmccauley4/index.php 
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

?>

<!-- Creating the SELECT section -->
 <h1>SELECT</h1>
 <br>
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
            echo "<p>⚠️ Could not load columns from table: $selectedTable</p>";
        }
    }
    
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
    print '<input type="submit" name="submit_btn" value="Query">';
    print '</form>';  // Ending the form
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $whereClause = $_POST["whereClause"];
        $selectedColumn = $_POST["column_name"];
        // Setting the query variable
        if(isset($selectedColumn)) {
            $sql_query = "SELECT " . $selectedColumn . " FROM " . $selectedTable;
            if(isset($whereClause)){
                $sql_query .= " WHERE ". $whereClause;
            }
        }
    }

?>

<!-- Creating the INSERT section -->
 <h1>INSERT</h1>
 <?php

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