<?php
	include 'includes/session.php';	
	include 'database/databasecon.php';
	
	if(!isset($_SESSION['username']))
	{
		header('location: index.php');
	}
	
	$uname = $_SESSION['username'];
	
	//getting post id
	$post_id = $_GET['id'];
	
	$dest = $_GET['dest'];
	
	$sql = "Delete from post where id = '$post_id'";
	
	if(mysqli_query($conn, $sql))
	{
		header('Location: '.$dest);
	}
	
	
?>