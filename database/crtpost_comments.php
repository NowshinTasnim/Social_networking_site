<?php
include 'databasecon.php';

$sql = "CREATE TABLE post_comments (
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
comment_body TEXT NOT NULL,
date_added DATE NOT NULL,
time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
commented_by VARCHAR(255) NOT NULL,
commented_to VARCHAR(255) NOT NULL,
opened VARCHAR(3) NOT NULL DEFAULT 'no', 
post_id int(11) NOT NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "Table post_comments created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
mysqli_close($conn);
?>