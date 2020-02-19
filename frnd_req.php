<?php
	include 'includes/session.php';	
	include 'database/databasecon.php';
	
	if(!isset($_SESSION['username']))
	{
		header('location: index.php');
	}
	
	$uname = $_SESSION['username'];
	
	$ut = "";
	$uf = "";
	
	if(isset($_POST['frnd_req']))
	{
		$fr = $_POST['frnd_req'];
		
		if(isset($_POST['user_to']))
		{
			$ut = $_POST['user_to'];
		}
		
		if(isset($_POST['user_from']))
		{
			$uf = $_POST['user_from'];
		}
		
		if($fr == "Send Friend Request")
		{
			$sql="Insert into friend_request(user_from,user_to,opened) Values('$uname','$ut','no')";
			if(mysqli_query($conn,$sql))
			{
				header('Location: profile_others.php?u='.$ut);
			}
		}
		
		if($fr == "Friend Request Sent")
		{
			$sql="DELETE FROM friend_request where user_from = '$uname' and user_to = '$ut'";
			if(mysqli_query($conn,$sql))
			{
				header('Location: profile_others.php?u='.$ut);
			}
		}
		
		if($fr == "Accept")
		{
			$sql="Insert into friend(user,friend_with) Values('$uname','$uf')";
			if(mysqli_query($conn, $sql))
			{
				$sql="Insert into friend(user,friend_with) Values('$uf','$uname')";
				mysqli_query($conn, $sql);
				$sql="DELETE FROM friend_request where user_from = '$uf' and user_to = '$uname'";
				mysqli_query($conn, $sql);
				header('Location: frndRequest.php');
			}
		}
		
		if($fr == "Delete")
		{
			$sql="DELETE FROM friend_request where user_from = '$uf' and user_to = '$uname'";
			if(mysqli_query($conn,$sql))
			{
				header('Location: frndRequest.php');
			}
		}
		
	}
?>