<?php
include 'databasecon.php';

$sql = "CREATE TABLE admin_login (
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
user_name varchar(32) NOT NULL,
user_pass varchar(32) NOT NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "Table admin_login created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
mysqli_close($conn);
?>