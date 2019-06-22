<?php
include 'databasecon.php';

$sql = "CREATE TABLE friend_request (
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
user_from VARCHAR(32) NOT NULL,
user_to VARCHAR(32) NOT NULL,
time DATETIME NOT NULL,
opened VARCHAR(3) NOT NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "Table follow created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
mysqli_close($conn);
?>