<?php
	
	//last post info
	$sql = "SELECT * from post where posted_by = '$uname' order by id desc limit 1";
	
	$post_activity = mysqli_query($conn, $sql);
	$num_post = mysqli_num_rows($post_activity);
	
	if($num_post>0)
	{
		$post_activity = mysqli_fetch_assoc($post_activity);
		$disc = $post_activity['discription'];
		$posted_to = $post_activity['posted_to'];
		if($posted_to == $uname)
		{
			$posted_to ='';
		}
	}
	else{
		$disc = "";
		$posted_to = "";
	}
	
	//last like info
	$sql = "SELECT * from post_likes where user_name = '$uname' order by id desc limit 1";
	
	$like_activity = mysqli_query($conn, $sql);
	$num_like = mysqli_num_rows($like_activity);
	
	if($num_like>0)
	{
		$like_activity = mysqli_fetch_assoc($like_activity);
		$post_id= $like_activity['post_id'];
		$sql = "SELECT * from post where id = '$post_id'";
		$post_details = mysqli_query($conn,$sql);
		$post_details = mysqli_fetch_assoc($post_details);
		$posted_by = $post_details['posted_by'];
	}
	else{
		$posted_by = "";
	}
	
	//last frnd req accepted
	$sql = "SELECT * from friend where user = '$uname' order by id desc limit 1";
	
	$frnd_activity = mysqli_query($conn, $sql);
	$frnd_like = mysqli_num_rows($frnd_activity);
	
	if($num_like>0)
	{
		$frnd_activity = mysqli_fetch_assoc($frnd_activity);
		$friend_with = $frnd_activity['friend_with'];
	}
	else{
		$friend_with = "";
	}
	
	$act='';
	
	if($disc == "" && $posted_to == "" && $posted_by == "" && $friend_with == "" )
	{ 
		$act .='Found nothing';
	}
	
	if($disc != "" && $posted_to != "")
	{
		$act .= '
			<div class="per_activity fix">'.$disc.' '.$posted_to.'.
				</div>
		';
	}
	
	if($posted_by != "")
	{
		$act .='
		<div class="per_activity fix">Liked '.$posted_by.'\'s post.
				</div>
		';
	}
	
	if($friend_with != "")
	{
		$act .='
		<div class="per_activity fix">Accepted '.$friend_with.'\'s friend request.
				</div>
		';
	}
	
	echo '
		<div class="activity fix">
			<div class="activity_header fix">RECENT ACTIVITY</div>
			<div class="activity_body fix">
				'.$act.'
			</div>
		</div>
	';
?>