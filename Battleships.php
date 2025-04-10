<?php session_start(); ?>
<!-- Website showcasing the search, insert, and delete functionality of the Battleship database
Live website can be found here: https://lamp.salisbury.edu/~jmccauley4/index.php 
Developer Note: My inconsistent use of camel case and snake case is awful, I am immensely sorry-->

<!DOCTYPE html>
<html lang="en">

<!-- Website title-->
<head>
    <title>Battleship Database</title>
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

<!-- Dropdown menu for table in database -->
<form method="POST">
    <label for="db_table">Select table:</label>
    <select id="db_table" name="db_table">
        <option value="Classes">Classes</option>
        <option value="Ships">Ships</option>
        <option value="Battles">Battles</option>
        <option value="Outcomes">Outcomes</option>
    </select>
</form>


<!-- Getting variables from dropdowns -->
<?php
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['db_table'])){
    $_SESSION['selectedTable'] = $_POST['db_table']; // Retreiving the table
    $sql_query = "SELECT * FROM $selectedTable";
    /* Retreive the column names of the chosen table from the database */
    $sql_col_query = "SHOW COLUMNS FROM" . $selectedTable;
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
}
?>

<!-- Creating dropdown menu for action
<form method="POST">
    <label for="Action">Choose an action:</label>
    <select id="Action" name="Action">
        <option value="SELECT">Select</option>
        <option value="INSERT">Insert</option>
        <option value="DELETE">Delete</option>
    </select>
    <input type="submit" value="Choose Action">
</form> -->

<!-- Defining PHP wrapper functions -->
<?php
// Wrapper function to add a dropdown with a specified array of columns
function generateDropdown($table_columns){
    $col_dropdown = '<form method="POST">';
    $col_dropdown .= '<label for="column_name"> WHERE: </label>';
    $col_dropdown .= '<select id="column_name" name="column_name">';
    // Add each column to the dropdown
    foreach ($table_columns as $col_name ){
        $col_dropdown .= '<option value="' . htmlspecialchars($col_name) . '">' . htmlspecialchars($col_name) .'</option>';
    }
    $col_dropdown .= '</select>';
    //$col_dropdown .= '<input type="submit" value="Submit Column">';
    $col_dropdown .= '</form>';
    return $col_dropdown;
}

// Wrapper function to add an input field with a POST request (parameter being variable name)
function generateInputField($inputName = 'whereClause'){
    $input_field = '<form method="POST">';
    $input_field .= '<input type="text" name="' . $inputName . '">';
    $input_field .= '</form>';
}

// Wrapper function that adds a submit button 
?>

<!-- Creating the SELECT section -->
 <h1>SELECT</h1>
 <br>
 <?php
    /* Creating a dropdown menu for the different columns */
    $dropdown_html = generateDropdown($table_columns);
    if (isset($dropdown_html)){
        print $dropdown_html;  // Print the dropdown
    }

    /* Generating an input box */
    generateInputField();

    /* Creating a submit button that retreives variables from the POST request */
    print('<input type="submit" name="submit_btn" value="Submit"');
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $whereClause = $_POST["whereClause"];
        $selectedColumn = $_POST["column_name"];
        // Setting the qeury variable
        if(isset($selectedColumn)) {
            $sql_query = "FROM " . $selectedTable . " SELECT " . $selectedColumn;
            if(isset($whereClause)){
                $sql_query .= " WHERE ". $whereClause;
            }
        }
    }

?>

<!-- Legacy code that may be reused -->
<?php /* 
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["Action"]) && isset($_SESSION['selectedTable'])) {
    $selectedAction = $_POST['Action']; // Retreiving the action
    $selectedTable = $_SESSION['selectedTable'];
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["column_name"])) {
            $selectedColumn[] = $_POST["column_name"];
            // Remove the selected column so it does not show up in following dropdowns
            $index = array_search($selectedColumn, $table_columns);
            if($index !== false){
                unset($table_columns[$index]); // Removes the chosen entry
                $table_columns = array_values($table_columns);  // Reindex the array
            }
            $col_count = count($table_columns);  // Get the number of columns to make additional fields
            // Add n additional more fields (for each column)
            while($col_count > 0){
                $dropdown_html = generateDropdown($table_columns);
                if (isset($dropdown_html)){
                    print $dropdown_html;
                }
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["column_name"])) {
                    $selectedColumn[] = $_POST["column_name"];
                    // Remove the selected column so it does not show up in following dropdowns
                    $index = array_search($selectedColumn, $table_columns);
                    if($index !== false){
                        unset($table_columns[$index]); // Removes the chosen entry
                        $table_columns = array_values($table_columns);  // Reindex the array
                    }
                    $col_count = $col_count - 1;  // Remove one 
                } 
                else {
                    break;
                }
            }
        }
    }
*/?>

<!-- Show the query results in a table-like format -->
<?php
    // Get the query result
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
        while($i < count($table_columns)){
            $tableOutput .= "<td>" . htmlspecialchars($row[$i]) . "</td>"; // Assign proper name to column
        }
        $tableOutput .= "</tr>";
        $tableOutput .= "</thead>";

        // Add in the rows of the table
        if(mysqli_num_rows($result) > 0){ 
            // Loop through all results of the query
            while($row = mysqli_fetch_assoc($result)){
                $tableOutput .= "<tr>";
                // Loop through all rows of the query
                $i = 0;
                while($i < count($table_columns)){
                    $tableOutput .= "<td>" . htmlspecialchars($row[$i]) . "</td>"; // Assign proper name to column
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
?>

</body>
</html>