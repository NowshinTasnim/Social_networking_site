<?php
	include 'includes/session.php';	
	include 'database/databasecon.php';
	
	if(!isset($_SESSION['username']))
	{
		header('location: index.php');
	}
	
	$uname = $_SESSION['username'];
	$email = $_SESSION['useremail'];
	
	$dest = "post_see.php";
	$id = $_GET['id'];
	
	$num_comments =0;
	
	$sql= "SELECT * FROM post where id = '$id'";
	
	$get_post = mysqli_query($conn, $sql) or die("Could not load!!");
 	$result = mysqli_fetch_assoc($get_post);
	
	$body = $result['body'];
	$posted_by = $result['posted_by'];
	$posted_to = $result['posted_to'];
	$photo = $result['photos'];
	$date_added = $result['date_added'];
	$discription = $result['discription'];
	$privacy = $result['privacy'];
		
	$sql = "SELECT * FROM users WHERE username = '$posted_by'";
	$get_info = mysqli_query($conn, $sql);
	$get_info = mysqli_fetch_assoc($get_info);
	$firstname = $get_info['firstname'];
	$lastname = $get_info['lastname'];
	$pic = $get_info['profile_pic'];
	
	$goto_pb="";
	$goto_pt="";
		
	if($posted_by == $uname)
	{
		$goto_pb = "profile.php";
	}
	else
	{
		$goto_pb = "profile_others.php?u=$posted_by";
	}
		
	$posted_to_fname = "";
	$posted_to_lname = "";
		
	if($posted_by != $posted_to)
	{
		$sql = "SELECT * FROM users WHERE username = '$posted_to'";
		$get_info = mysqli_query($conn, $sql);
		$get_info = mysqli_fetch_assoc($get_info);
		$posted_to_fname = $get_info['firstname'];
		$posted_to_lname = $get_info['lastname'];
		if($posted_to == $uname)
		{
			$goto_pt = "profile.php";
		}
		else
		{
			$goto_pt = "profile_others.php?u=$posted_to";
		}
	}
		
	$sql = "SELECT * FROM post_likes WHERE post_id = '$id'";
	$get_likes = mysqli_query($conn, $sql);
	$num_likes = mysqli_num_rows($get_likes);
	$post="";
	
	if($photo!="")
	{
		$post = '
			<div class = "perpost fix">
				<div class = "postheader fix">
					<a href ="'.$goto_pb.'"><img src = "user/user_profile_pic/'.$pic.'" height="30px" width="30px" /></a>
					<span class = "fullname fix"><b><a href ="'.$goto_pb.'">'.$firstname.' '.$lastname.'</a></b>'.$discription.'<b><a href ="'.$goto_pt.'">'.$posted_to_fname.' '.$posted_to_lname.'</a></b><br/>'.$date_added.'</span>
					<span class = "privacymode fix">'.$privacy.'</span>
					<span class = "delete_post fix"><a href="delete_post.php?id='.$id.'&dest='.$dest.'">Delete</a></span>
				</div>
				
				<div class="postbody fix">
				 '.$body.'
				</div>
				
				
				<div class="postphoto fix">
					<img src="user/user_images/'.$photo.'"  width="567px" height="auto"/>
				</div>
				<div class="count fix">
					<span class="count_like fix">Likes '.$num_likes.'</span>
					<span class="count_comment fix">Comments '.$num_comments.'</span>
				</div>
				<div class = "per_postfooter fix">			
					<form action ="like_comment.php?u='.$id.'" method="POST">
						<input type = "hidden" name = "destination" value="'.$dest.'"/>
						<input class = "btn_like fix" type = "submit" name = "like" value="Like"/>
						<input class = "btn_comment fix" onclick= name = "comment" value="Comment"/>
					</form>
				</div>
			</div>
		
		';
	}
	else
	{
		$post = '
			<div class = "perpost fix">
				<div class = "postheader fix">
					<a href ="'.$goto_pb.'"><img src = "user/user_profile_pic/'.$pic.'" height="30px" width="30px" /></a>
					<span class = "fullname fix"><b><a href ="'.$goto_pb.'">'.$firstname.' '.$lastname.'</a></b>'.$discription.'<b><a href ="'.$goto_pt.'">'.$posted_to_fname.' '.$posted_to_lname.'</a></b><br/>'.$date_added.'</span>
					<span class = "privacymode fix">'.$privacy.'</span>
					<span class = "delete_post fix"><a href="delete_post.php?id='.$id.'&dest='.$dest.'">Delete</a></span>
				</div>
				
				<div class="postbody fix">
				 '.$body.'
				</div>
				
				<div class="count fix">
					<span class="count_like fix">Likes '.$num_likes.'</span>
					<span class="count_comment fix">Comments '.$num_comments.'</span>
				</div>
				<div class = "per_postfooter fix">			
					<form action ="like_comment.php?u='.$id.'" method="POST">
						<input class = "btn_like fix" type = "submit" name = "like" value="Like"/>
						<input class = "btn_comment fix" type = "submit" name = "comment" value="Comment"/>
					</form>
				</div>
			</div>
		
		';
	}
	
	
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Post</title>
		<link rel="stylesheet" type="text/css" href="css/post_see.css">
		<link rel="stylesheet" type="text/css" href="css/mainnav.css">
		<link rel="stylesheet" type="text/css" href="css/leftnav.css">
		<link rel="stylesheet" type="text/css" href="css/recentactivity.css">
	</head>
	
	<body>
		<div class="main fix">
			<div class="top_nav fix">
				<?php include 'includes/mainnav.php'; ?>
			</div>
			<div class="left_nav fix">
				<?php include 'includes/leftnav.php'; ?>
			</div>
			<div class ="right_area fix">
				<?php include 'includes/recentactivity.php'; ?>
			</div>
			<div class="middle_area fix">
				<div class = "middle fix">
					<?php echo $post; ?>
				</div>
			</div>
		</div>
	</body>
	
	<footer>
	
	</footer>
</html>