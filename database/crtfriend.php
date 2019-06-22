<?php
include 'databasecon.php';

$sql = "CREATE TABLE friend (
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
user VARCHAR(32) NOT NULL,
friend_with VARCHAR(32) NOT NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "Table follow created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
mysqli_close($conn);
?>