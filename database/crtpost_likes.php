<?php
include 'databasecon.php';

$sql = "CREATE TABLE post_likes (
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
user_name VARCHAR(255) NOT NULL,
post_id int(11) NOT NULL,
opened VARCHAR(3) NOT NULL DEFAULT 'no',
)";

if (mysqli_query($conn, $sql)) {
    echo "Table postlike created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
mysqli_close($conn);
?>