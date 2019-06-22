<?php
include 'databasecon.php';

$sql = "CREATE TABLE pvt_msg (
msg_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
user_from VARCHAR(255) NOT NULL,
user_to VARCHAR(255) NOT NULL,
msg_body TEXT NOT NULL,
date DATE NOT NULL,
msg_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
opened VARCHAR(255) NOT NULL,
user_id int(11) NOT NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "Table pvtmsg created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
mysqli_close($conn);
?>