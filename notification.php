<?php
	include 'includes/session.php';	
	include 'database/databasecon.php';
	
	if(!isset($_SESSION['username']))
	{
		header('location: index.php');
	}
	
	$uname = $_SESSION['username'];
	$email = $_SESSION['useremail'];
	
	$dest = "notification.php";
	
	//set opened
	$sql = "UPDATE post_comments SET opened = 'yes' where commented_to = '$uname'";
	mysqli_query($conn,$sql);
	
	$posts = "
		<div class='noti_header fix'>NOTIFICATIONS
		</div>
	";
		
	//check for noti started
	
	//comment check
	$sql = "SELECT * FROM post_comments WHERE commented_to='$uname' AND commented_by != '$uname' ORDER BY id DESC";
	$check_for_postc_noti = mysqli_query($conn, $sql);
	$post_noti_num = mysqli_num_rows($check_for_postc_noti);
	
	
				
	if($post_noti_num>0)
	{
			$posts .="
				<div class='com_noti_head fix'>Comments
				</div>
			";
			while($noti_com = mysqli_fetch_array($check_for_postc_noti))
			{
				$commented_by = $noti_com['commented_by'];
				$post_id = $noti_com['post_id'];
				
				$posts .="
					<div class='per_com_noti'> ".$commented_by." has commented on your <a href='post_see.php?id=".$post_id."'>post</a>.
					</div>
				";
			}
	}
	
	
	//like check
	$sql = "SELECT * FROM post_likes";
	$sqlquery = mysqli_query($conn, $sql);
	while($res = mysqli_fetch_array($sqlquery))
	{
		$post_id = $res['post_id'];
		
		$sql = "SELECT * from post where id = '$post_id' and posted_by = '$uname'";
		$check_for_postl_noti = mysqli_query($conn, $sql);
		$post_noti_num = mysqli_num_rows($check_for_postl_noti);
		
		if($post_noti_num > 0)
		{
			$posts .="
				<div class='like_noti_head fix'>Likes
				</div>
			";
			while($rsl = mysqli_fetch_array($check_for_postl_noti))
			{
				$id = $rsl['id'];
				$liked_by = $res['user_name'];
				$sql = "UPDATE post_likes SET opened = 'yes' where post_id = '$id'";
				mysqli_query($conn,$sql);
				$posts .="
					<div class='per_like_noti'> ".$liked_by." has liked your <a href='post_see.php?id=".$id."'>post</a>.
					</div>
				";
				
			}
			
		}
		
	}
	//check for noti ended
	
	
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Notifications</title>
		<link rel="stylesheet" type="text/css" href="css/notification.css">
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
					<?php echo $posts; ?>
				</div>
			</div>
		</div>
	</body>
	
	<footer>
	
	</footer>
</html>