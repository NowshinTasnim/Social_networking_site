<?php
	include 'includes/session.php';
	include 'database/databasecon.php';
	
	if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']== false)
	{
		header("Location: index.php");
	}
	
	$uname = $_SESSION['username'];
	$email = $_SESSION['useremail'];
	
	$profile =$_GET['u'];
	
	$fw = "Send Friend Request";
	$check_frnd_sql = "SELECT * FROM friend where user= '$uname' AND friend_with= '$profile' LIMIT 1";
	$check_frnd_result = mysqli_query($conn,$check_frnd_sql);
	$num_frnd_result = mysqli_num_rows($check_frnd_result);
							
	if($num_frnd_result == 1)
	{	
		$fw = "Friend";
	}
							
	$check_frnd_sql = "SELECT * FROM friend_request where user_from = '$uname' AND user_to = '$profile' LIMIT 1";
	$check_frnd_result = mysqli_query($conn,$check_frnd_sql);
	$num_frnd_result = mysqli_num_rows($check_frnd_result);
							
	if($num_frnd_result == 1)
	{
		$fw = "Friend Request Sent";
	}
	
	$dest = 'profile_others.php?u='.$profile;
	
	//getting_cover_pic_started
	$cov_pic = "images/default_cover.png";
	$sql = "SELECT * FROM users where (username = '$profile' AND cover_pic != '') LIMIT 1";
	$get_cover_pic = mysqli_query($conn, $sql);
	$num_cov = mysqli_num_rows($get_cover_pic);
	if($num_cov>0)
	{
		$get_cov = mysqli_fetch_assoc($get_cover_pic);
		$cov_pic = "user/user_cover_pic/".$get_cov['cover_pic'];
	}
	//getting_cover_pic_ended
	
	//getting_pro_info_started
	$sql = "SELECT * FROM users where username = '$profile' LIMIT 1";
	$get_pro_info = mysqli_query($conn, $sql);
	$get_pro_info = mysqli_fetch_assoc($get_pro_info);
	$pro_pic_path = $get_pro_info['profile_pic'];
	$fname = $get_pro_info['firstname'];
	$lname = $get_pro_info['lastname'];
	$city = $get_pro_info['city'];
	$hometown = $get_pro_info['hometown'];
	$company = $get_pro_info['company'];
	$position = $get_pro_info['position'];
	$school = $get_pro_info['school'];
	$conc = $get_pro_info['concentration'];
	$relation = $get_pro_info['relationship'];
	
	//getting_pro_info_ended
	
	//posting started
	
	if(isset($_POST['post']) && $_POST['post'] != "" && $fw == "Friend")
	{
		//trimed the post 
		$post = trim($_POST['posttext']);
		
		//checking null or not
		if($post != "" || isset($_FILES['photo']))
		{
			$date_added = date("Y-m-d");
			$posted_by = $uname;
			$posted_to = $profile;
			$privacy = "Friends";
			
			
			if(isset($_FILES['photo']))
			{
				$discription = " has Added a new photo to ";
				
				//extraxting name and extention
				$pic_name = $_FILES['photo']['name'];
				$file_basename = substr($pic_name,0,strripos($pic_name, '.'));
				$file_ext = substr($pic_name,strripos($pic_name, '.'));
				$type = $_FILES['photo']['type'];
				$size= $_FILES['photo']['size'];
				
				//checking type
				
				if($type == 'image/jpeg' || $type == 'image/jpg' || $type == 'image/png')
				{
					if($size>=10 && $size<=5000000)
					{
						if(!file_exists("user/user_images/$profile"))
						{
							mkdir("user/user_images/$profile");
						}
						
						$filename = strtotime(date('Y-m-d H:i:s')).$file_ext;
						$tempname = $_FILES['photo']['tmp_name'];
						$photo = "$profile/$filename";
						move_uploaded_file($tempname,"user/user_images/$profile/".$filename);
						$sql="INSERT INTO post(body, date_added, posted_by, posted_to, discription, photos, privacy) VALUES('$post', '$date_added','$posted_by', '$posted_to', '$discription', '$photo', '$privacy')";
					
						if(mysqli_query($conn,$sql))
						{
							header('Location: profile_others.php?u='.$profile);					
						}
						else
						{
							echo "Couldn't upload! Please try again!";
						}
					}
					else
					{
						echo "File size must be within 5MB";
					}
				}
			}
			else
			{
				$discription =" has Posted to ";
				$sql="INSERT INTO post(body, date_added, posted_by, posted_to,discription, privacy) VALUES('$post', '$date_added','$posted_by', '$posted_to', '$discription', '$privacy')";
				if(mysqli_query($conn,$sql))
				{
					header('Location: profile_others.php?u='.$profile);
				}
			}
		}
	}
	
	//posting ended
	
	//timeline query started
	
	$feed="";
	$num_likes =0;
	$num_comments =0;
	$prv= "Public";
	$prv2="";
	if($fw == "Friend")
	{
		$prv2 = "Friends";
	}
	
	$sql= "SELECT * FROM post where ( (posted_by = '$profile' && posted_to = '$profile') || posted_to = '$profile') AND ( privacy = '$prv' || privacy = '$prv2') ORDER BY id DESC Limit 10";
	$get_posts = mysqli_query($conn, $sql) or die("Could not load!!");
 	while($result = mysqli_fetch_array($get_posts))
	{
		$id = $result['id'];
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
		$posted_to_fname = "";
		$posted_to_lname = "";
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
		
		if($posted_by != $posted_to)
		{
			$sql = "SELECT * FROM users WHERE username = '$posted_to'";
			$get_info = mysqli_query($conn, $sql);
			$get_info = mysqli_fetch_assoc($get_info);
			$posted_to_fname = $get_info['firstname'];
			$posted_to_lname = $get_info['lastname'];
			$goto_pt="profile_others.php?u=$posted_to";
		}
		
		//get_likes
		$sql = "SELECT * FROM post_likes WHERE post_id = '$id'";
		$get_likes = mysqli_query($conn, $sql);
		$num_likes = mysqli_num_rows($get_likes);
		
		//get_comments
		$sql = "SELECT * from post_comments where post_id = '$id'";
		$get_comments = mysqli_query($conn,$sql);
		$num_comments = mysqli_num_rows($get_comments);
		$comments="";
		if($num_comments>0)
		{
			while($com = mysqli_fetch_array($get_comments))
			{
				$com_by = $com['commented_by'];
				$com_body = $com['comment_body'];
				
				$sql = "Select * from users where username = '$com_by'";
				
				$get_data = mysqli_query($conn,$sql);
				
				$get_data = mysqli_fetch_assoc($get_data);
				
				$com_fname = $get_data['firstname'];
				$com_lname = $get_data['lastname'];
				$com_pic = $get_data['profile_pic'];
				
				if($com_by == $uname)
				{
					$goto_prfl = "profile.php";
				}
				else
				{
					$goto_prfl = "profile_others.php?u=$com_by";
				}
				
				$comments .='
					<div class = "percom fix">
						<a href="'.$goto_prfl.'"><img src = "user/user_profile_pic/'.$com_pic.'" height="30px" width="30px" /></a>
						<span class = "com_name fix"><b><a href ="'.$goto_pb.'">'.$com_fname.' '.$com_lname.'</a></b></span>
						<p class = "com_body fix">'.$com_body.'</p>
					</div>
				';
			}
		}
		
		if($photo!="")
		{
			$feedper = '
			<div class = "perpost fix">
				<div class = "postheader fix">
					<a href="'.$goto_pb.'"><img src = "user/user_profile_pic/'.$pic.'" height="30px" width="30px" /></a>
					<span class = "fullname fix"><b><a href="'.$goto_pb.'"> '.$firstname.' '.$lastname.' </a></b>'.$discription.' <b><a href="'.$goto_pt.'"> '.$posted_to_fname.' '.$posted_to_lname.' </a></b><br/>'.$date_added.'</span>
					<span class = "privacymode fix">'.$privacy.'</span>
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
						<input type="text" class = "btn_comment fix" onclick = "show_comment_box()" name = "comment" value="Comment"/>
						<div id="comment_area">
							<input class = "comment_area fix" type = "text" name ="comment_body">
							<input class = "btn_cmnt fix" type = "submit" name ="cmnt" value="Comment">
						</div>
					</form>
					<div class = "comments fix">
					'.$comments.'
					</div>
				</div>
			</div>
		
			';
			$feed .= $feedper;
		}
		else
		{
			$feedper = '
			<div class = "perpost fix">
				<div class = "postheader fix">
					<a href="'.$goto_pb.'"><img src = "user/user_profile_pic/'.$pic.'" height="30px" width="30px" /></a>
					<span class = "fullname fix"><b><a href="'.$goto_pb.'">'.$firstname.' '.$lastname.'</a></b>'.$discription.' <b><a href="'.$goto_pt.'">'.$posted_to_fname.' '.$posted_to_lname.'</a></b><br/>'.$date_added.'</span>
					<span class = "privacymode fix">'.$privacy.'</span>
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
						<input type = "hidden" name = "destination" value="'.$dest.'"/>
						<input class = "btn_like fix" type = "submit" name = "like" value="Like"/>
						<input type="text" class = "btn_comment fix" onclick = "show_comment_box()" name = "comment" value="Comment"/>
						<div id="comment_area">
							<input class = "comment_area fix" type = "text" name ="comment_body">
							<input class = "btn_cmnt fix" type = "submit" name ="cmnt" value="Comment">
						</div>
					</form>
					<div class = "comments fix">
					'.$comments.'
					</div>
				</div>
			</div>
		
			';
			$feed .= $feedper;
		}
	}
	
	//timeline query ended
	
	//profile_info_Show
	$profile_info = "
		<div class = 'profile_info_head fix'> ABOUT
		</div>
	";
	
	if($fw == "Friend")
	{
		$profile_info .= "
		<div class = 'profile_info fix'>
			<a href = 'profile_others.php?u=".$profile."&f=".$fw."'>".$fname." ".$lname."</a></span><br/> 
			<b>Hometown: </b>".$hometown."<br/>
			<b>Permanent city: </b>".$city." <br/>
			<b>School: </b>".$school."<br/>
			<b>Concentrate: </b>".$conc."<br/>
			<b>Company: </b>".$company." <br/>
			<b>Position: </b>".$position." <br/>
			<b>Relationship Status: </b>".$relation." <br/>
		</div>
	";
	}
	else{
		$profile_info .= "
		<div class = 'profile_info fix'>
			<a href = 'profile_others.php?u=".$profile."&f=".$fw."'>".$fname." ".$lname."</a></span><br/> 
			<b>Hometown: </b>".$hometown."<br/>
			<b>Current city: </b>".$city." <br/>
			<b>School: </b>".$school."<br/>
			<b>Company: </b>".$company." <br/>
			</span>
		</div>
	";
	}
	
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $profile;?></title>
		<link rel="stylesheet" type="text/css" href="css/profile.css">
		<link rel="stylesheet" type="text/css" href="css/mainnav.css">
		<link rel="stylesheet" type="text/css" href="css/post.css">
	</head>
	
	<body>
		<div class = "main fix">
			<div class="top_nav fix">
				<?php include 'includes/mainnav.php'; ?>
			</div>
			<div class="coverphoto fix">
				<img src = "<?php echo $cov_pic;?>" height = "300px" width = "837px" alt="cover_pic"/>
			</div>
			
			<div class="propic fix">
				<img src ="user/user_profile_pic/<?php echo $pro_pic_path; ?>" alt="pp" height="250px" width="250px"/>
			</div>
			
			
			
			<div class="middle_area fix">
				<div class="middle_left fix">
					<?php echo $profile_info;?>
				</div>
				
				<div class = "middle_right fix">
					<div class="btn_msg_frnd fix">
					
						<?php if($fw != "Friend"){?>
						<form action='frnd_req.php' method = 'POST'>
							<input type = 'hidden' name = 'user_to' value ='<?php echo $profile;?>'>
							<input type= 'submit' name = 'frnd_req' class = 'frnd_send fix' value = '<?php echo $fw;?>'/>
						</form>
					<?php } ?>
					</div>
					
					<?php if($fw == "Friend"){?>
					<div class = "middle_top fix">
						<?php include 'includes/posttoothers.php'; ?>
					</div>
					<?php } ?>
					
					<div class = "middle fix">
						<div class="middle_head fix">
							TIMELINE
						</div>
						<div class="posts fix">
							<!-- timeline-->
							<?php echo $feed; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<script>
			function show_comment_box()
			{
				document.getElementById('comment_area').style.display ='block';
			}
		</script>
	</body>
</html>