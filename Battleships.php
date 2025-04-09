<!-- Website showcasing the search, insert, and delete functionality of the Battleship database
Live website can be found here: https://lamp.salisbury.edu/~jmccauley4/index.php -->

<!DOCTYPE html>
<html lang="en">

<!-- Website title-->
<head>
    <title>Battleship Database</title>
</head>
<body>

<!-- Establishing a connection to the database -->
<?php
if($connection = @mysqli_connect(hostname: 'localhost', username: 'jmccauley4', password: 'jmccauley4', database: 'jmccauley4DB')){
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
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $selectedTable = $_POST['db_table']; // Retreiving the table
}
?>

<!-- Creating dropdown menu for action -->
<form method="POST">
    <label for="Action">Choose an action:</label>
    <select id="Action" name="Action">
        <option value="SELECT">Select</option>
        <option value="INSERT">Insert</option>
        <option value="DELETE">Delete</option>
    </select>
</form>

<!-- Defining PHP function to print a dropdown using a given array -->
<?php
function generateDropdown($table_columns){
    $col_dropdown = '<form method="POST">';
    $col_dropdown .= '<label for="column_name"> WHERE: </label>';
    $col_dropdown .= '<select id="column_name" name="column_name">';
    // Add each column to the dropdown
    foreach ($table_columns as $col_name ){
        $col_dropdown .= '<option value="' . htmlspecialchars($col_name) . '">' . htmlspecialchars($col_name) .'</option>';
    }
    $col_dropdown .= '</select>';
    return $col_dropdown;
} 
?>

<!-- Based on action, using PHP to update relevant fields -->
 <?php
if($_SERVER["REQUEST_METHOD"] == "POST" && isset( $_POST["Action"]) && isset($selectedTable)) {
    $selectedAction = $_POST['Action']; // Retreiving the action

    /* Retreive the column names of the chosen table from the database */
    $sql_col_query = "SHOW COLUMNS FROM $selectedTable";
    $result_col_query = mysqli_query( $connection, $sql_col_query);
    $table_columns = [];
    if(mysqli_num_rows($result) > 0){ 
        while($row = mysqli_fetch_assoc($result)){  // Looping through all results of the query
            $table_columns[] = htmlspecialchars($row['Field']);
        }
    }
    else{  // No results found from the query (should never happen)
        print '<p> Please choose a valid table from the database <p>';
    }

    /* Now that the action is retreived, can use the corresponding input fields*/
    if($selectedAction == "SELECT"){
        $dropdown_html = generateDropdown(table_columns: $table_columns);
        if (isset($dropdown_html)){
            print "<h3>Column: ";
            print $dropdown_html;
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["column_name"])) {
            $selectedColumn[] = $_POST["column_name"];
            // Remove the selected column so it does not show up in following dropdowns
            $index = array_search(needle: $selectedColumn, haystack: $table_columns);
            if($index !== false){
                unset($table_columns[$index]); // Removes the chosen entry
                $table_columns = array_values(array: $table_columns);  // Reindex the array
            }
            $col_count = count($table_columns);  // Get the number of columns to make additional fields
            // Add n additional more fields (for each column)
            while($col_count > 0){
                $dropdown_html = generateDropdown(table_columns: $table_columns);
                if (isset($dropdown_html)){
                    print "<h3>Column: ";
                    print $dropdown_html;
                }
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["col_name"])) {
                    $selectedColumn .= $_POST["col_name"];
                    // Remove the selected column so it does not show up in following dropdowns
                    $index = array_search(needle: $selectedColumn, haystack: $table_columns);
                    if($index !== false){
                        unset($table_columns[$index]); // Removes the chosen entry
                        $table_columns = array_values(array: $table_columns);  // Reindex the array
                    }
                    $col_count = $col_count - 1;  // Remove one 
                }
            }
        }
    }
}
 ?>

<!-- Once form is submitted, show the query results or new table -->
<?php

?>

</body>
</html>