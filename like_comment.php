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
	$dest = $_POST['destination'];
	
	//like
	if(isset($_POST['like']) && $_POST['like'] != NULL)
	{
		$sql = "SELECT * from post_likes where (post_id = '$post_id' AND user_name = '$uname')";
		$check = mysqli_query($conn,$sql);
		$num_check = mysqli_num_rows($check);
		
		
		if($num_check < 1)
		{
		
			$sql = "INSERT INTO post_likes(user_name,post_id) VALUES('$uname','$post_id')";
			if(mysqli_query($conn, $sql))
			{
				header('Location: '.$dest);
			}
			else 
			{
				echo "error!!";
			}
		}
		else{
			$sql = "DELETE FROM post_likes where (user_name = '$uname' AND post_id = '$post_id')";
			if(mysqli_query($conn, $sql))
			{
				header('Location: '.$dest);
			}
			else 
			{
				echo "error del!!";
			}
		}
	}
	
	
	//comment
	if(isset($_POST['cmnt']) && $_POST['cmnt'] != NULL)
	{
		$cmnt_body = $_POST['comment_body'];
		$sql="SELECT * from post where id ='$post_id'";
		$res = mysqli_query($conn,$sql);
		$res = mysqli_fetch_assoc($res);
		
		$posted_by = $res['posted_by'];
		
		$date = date("Y-m-d");
		
		$sql = "INSERT INTO post_comments(comment_body, date_added, commented_by, commented_to, post_id) VALUES('$cmnt_body', '$date', '$uname', '$posted_by', '$post_id')";
		if(mysqli_query($conn,$sql))
		{
			header('Location: '.$dest);
		}
		else{
			echo "error!!";
		}
	}
?>