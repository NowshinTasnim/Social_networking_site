<?php
	include 'includes/session.php';
	include 'database/databasecon.php';
	
	if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']== false)
	{
		header("Location: index.php");
	}
	
	$uname = $_SESSION['username'];
	$email = $_SESSION['useremail'];
	
	$post_id = $_GET['u'];
	
	if(isset($_POST['like']))
	{
		$sql = "INSERT INTO post_likes(user_name,post_id) VALUES('$uname','$post_id')";
		if(mysqli_query($conn, $sql))
		{
			header('Location: home.php');
		}
		else 
		{
			echo "error!!";
		}
	}
?>