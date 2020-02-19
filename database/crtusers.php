<?php
include 'databasecon.php';

$sql = "CREATE TABLE users (
user_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
firstname VARCHAR(255) NOT NULL,
lastname VARCHAR(255) NOT NULL,
username VARCHAR(255) NOT NULL,
pass VARCHAR(255) NOT NULL,
email VARCHAR(255) NOT NULL,
profile_pic TEXT NOT NULL,
contact VARCHAR(11) NOT NULL,
gender VARCHAR(7) NOT NULL,
birthday DATE NOT NULL,
country VARCHAR(255) NOT NULL,
reg_date DATE NOT NULL,
city TEXT NOT NULL,
hometown TEXT NOT NULL,
cover_pic TEXT NOT NULL,
closed VARCHAR(3) NOT NULL DEFAULT 'no',
relationship VARCHAR(255) NOT NULL,
company TEXT NOT NULL,
position TEXT NOT NULL,
school TEXT NOT NULL,
concentration TEXT NOT NULL,
confirmation_code TEXT NOT NULL,
chatOnlineTime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "Table users created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
mysqli_close($conn);
?>