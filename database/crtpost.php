<?php
include 'databasecon.php';

$sql = "CREATE TABLE post (
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
body TEXT NOT NULL,
date_added DATE NOT NULL,
post_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
posted_by VARCHAR(255) NOT NULL,
posted_to VARCHAR(255) NOT NULL,
share_post int(11) NOT NULL,
buddy_post int(11) NOT NULL,
discription TEXT NOT NULL,
photos TEXT NOT NULL,  
newsfeedshow int(1) NOT NULL DEFAULT '1',
report int(1) NOT NULL DEFAULT '0',
note int(1) NOT NULL DEFAULT '0',
note_privacy TEXT NOT NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "Table post created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
mysqli_close($conn);
?>