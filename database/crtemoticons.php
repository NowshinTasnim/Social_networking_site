<?php
include 'databasecon.php';

$sql = "CREATE TABLE emoticons (
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
chars TEXT NOT NULL,
photos TEXT NOT NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "Table emoticons created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
mysqli_close($conn);
?>