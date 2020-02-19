<?php
	
	$uname = $_SESSION['username'];
	//$post_noti_num = 0;
	//$unread_msg_num = 0;
	//$follow_num = 0;
	//getting profile pic path started
	
	$sql = "SELECT * FROM users WHERE username='$uname' ";
	$get_data = mysqli_query($conn, $sql);
	$get_pro_pic = mysqli_fetch_assoc($get_data);
	$get_pro_pic_path = $get_pro_pic['profile_pic'];
	
	//getting profile pic path ended
	
	//check for notification started
	
	$sql = "SELECT * FROM post_comments WHERE commented_to='$uname' AND commented_by != '$uname' AND opened='no' ORDER BY id DESC";
	$check_for_postc_noti = mysqli_query($conn, $sql);
	$post_noti_num = mysqli_num_rows($check_for_postc_noti);
	$sql = "SELECT * FROM post_likes where opened = 'no'";
	$sqlquery = mysqli_query($conn, $sql);
	while($res = mysqli_fetch_array($sqlquery))
	{
		$post_id = $res['post_id'];
		$sql = "SELECT * from post where id = '$post_id' AND posted_by = '$uname'";
		$check_for_postl_noti = mysqli_query($conn, $sql);
		$post_noti_num += mysqli_num_rows($check_for_postl_noti);
	}
	
	if($post_noti_num > 0)
	{
		$noti = "images/noti.png";
	}
	else
	{
		$noti = "images/noti_null.png";
	}
	//check for notification ended
	
	//check for frnd req started
	$sql = "SELECT * FROM friend_request WHERE user_to='$uname' AND opened='no' ";
	$check_for_frnd = mysqli_query($conn, $sql);
	$frnd_num = mysqli_num_rows($check_for_frnd);
	if($frnd_num > 0)
	{
		$frnd = "images/frnd_req.png";
	}
	else
	{
		$frnd = "images/frnd_req_null.png";
	}
	//check for frnd req ended
	
	//noti checked
	
	{
		if($post_noti_num > 0)
		{
			$sql = "UPDATE post_comments SET opened = 'yes' WHERE user_to = '$uname' ";
			$checked = mysqli_query($conn, $sql);
		}
	}
	
	
	
	echo "
		<div class='upper fix'>
			<!-- Logo started -->
			<div class='logo fix'>
				<img src='images/logo.png' alt='logo' height='30px' width='100px' />
			</div>
			<!-- Logo ended -->
			
			<!-- Search bar started -->
			<div class='search_bar fix'>
				<form name='search_form' action='search.php' method='POST'>
					<input type='text' class='search_area fix' name='searcharea' placeholder = 'Search here...'/>
					<select name='search_op' class='search_option'>
						<option>User</option>
					</select>
					<button type='submit' name='search' class='btn_search fix'><img src='images/search.png' height='20px' /></button>
				</form>
			</div>
			<!-- Search bar ended -->	
			
			<!-- Navigation started-->
			<div class='navigation fix'>
				<img class='pp_tiny' src = 'user/user_profile_pic/".$get_pro_pic_path."' alt='pp' height='30px' width='30px' />
				<ul class='navleft fix'>
					<li><a href='profile.php?u=".$uname."'>".$uname."</a></li>
					<li><a href='home.php'>Home</a></li>
				</ul>
			
				<ul class='navmid fix'>
					<li><a name='frnd_req' href='frndRequest.php' ><img src='".$frnd."' height='20px'/><font color='white'>".$frnd_num."</font></a></li>
					<li><a name='noti' href='notification.php'><img src='".$noti."' height='20px'/><font color='white'>".$post_noti_num."</font></a></li>
				</ul>
				
				<ul class='navlog fix'>	
					<li><a href='logout.php'>Logout</a></li>
				</ul>
			</div>
			<!-- Navigation ended-->
		</div>
	";
	
?>

