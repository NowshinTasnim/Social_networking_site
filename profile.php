<?php
	include 'includes/session.php';
	include 'database/databasecon.php';
	
	if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']== false)
	{
		header("Location: index.php");
	}
	
	$uname = $_SESSION['username'];
	$email = $_SESSION['useremail'];
	
	$profile= $_GET['u'];
	
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
	
	//getting_pro_pic_started
	$sql = "SELECT * FROM users where username = '$profile' LIMIT 1";
	$get_pro_pic = mysqli_query($conn, $sql);
	$get_pro_pic = mysqli_fetch_assoc($get_pro_pic);
	$pro_pic_path = $get_pro_pic['profile_pic'];
	//getting_pro_pic_ended
	
	//checking frnd started
	$check_frnd ="";
	if($uname != $profile)
	{
		$sql = "SELECT * FROM friend WHERE (user = '$uname' AND friend_with = '$profile')";
		$get_frnd_info = mysqli_query($conn, $sql);
		$num_row = mysqli_num_rows($get_frnd_info);
		if($num_row<1)
		{
			$check_frnd ="Send Friend Request";
		}
		else
		{
			$check_frnd ="Already Friend";
		}
		
	}
	//checking frnd ended
	
	//checking msgbtn started
	$msgtoothers ="";
	if($uname != $profile)
	{
		$msgtoothers ="Message";
	}
	//checking msgbtn ended
	
	//coverphoto update started
	if(isset($_POST['upload_cover']))
	{
			//extraxting name and extention
				$cover_name = $_FILES['upload_cover']['name'];
				$file_basename = substr($cover_name,0,strripos($cover_name, '.'));
				$file_ext = substr($cover_name,strripos($cover_name, '.'));
				$type = $_FILES['upload_cover']['type'];
				$size= $_FILES['upload_cover']['size'];
				
				//checking type
				
				if($type == 'image/jpeg' || $type == 'image/jpg' || $type == 'image/png')
				{
					if($size>=10 && $size<=5000000)
					{
						if(!file_exists("user/user_cover_pic/$uname"))
						{
							mkdir("user/user_cover_pic/$uname");
						}
						
						$filename = strtotime(date('Y-m-d H:i:s')).$file_ext;
						$tempname = $_FILES['upload_cover']['tmp_name'];
						$cphoto = "$uname/$filename";
						move_uploaded_file($tempname,"user/user_cover_pic/$uname/".$filename);
						$sql="UPDATE users SET cover_pic = '$cphoto' WHERE username = 'uname'";
					
						if(mysqli_query($conn,$sql))
						{
							header('Location: profile.php?u =".$uname."');					
						}
						else
						{
							echo "Couldn't upload! Please try again!";
						}
					}
					else
					{
						echo"File size must be within 5MB";
					}
				}
	}
	//coverphoto update ended
	
	//posting started
	
	if(isset($_POST['post']))
	{
		//trimed the post 
		$post = trim($_POST['posttext']);
		//echo $_FILES['photo'];
		//checking null or not
		if($post != "" || isset($_FILES['photo']))
		{
			$date_added = date("Y-m-d");
			$posted_by = $uname;
			$posted_to = $profile;
			$privacy = $_POST['privacy'];
			//echo $_FILES['photo'];
			if(isset($_FILES['photo']))
			{
				$discription = "Added a new photo.";
				
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
						if(!file_exists("user/user_images/$uname"))
						{
							mkdir("user/user_images/$uname");
						}
						
						$filename = strtotime(date('Y-m-d H:i:s')).$file_ext;
						$tempname = $_FILES['photo']['tmp_name'];
						$photo = "$uname/$filename";
						move_uploaded_file($tempname,"user/user_images/$uname/".$filename);
						$sql="INSERT INTO post(body, date_added, posted_by, posted_to, discription, photos, privacy) VALUES('$post', '$date_added','$posted_by', '$posted_to', '$discription', '$photo', '$privacy')";
					
						if(mysqli_query($conn,$sql))
						{
							header('Location: home.php');					
						}
						else
						{
							$error="Couldn't upload! Please try again!";
						}
					}
					else
					{
						$error="File size must be within 5MB";
					}
				}
			}
			$sql="INSERT INTO post(body, date_added, posted_by, posted_to, privacy) VALUES('$post', '$date_added','$posted_by', '$posted_to', '$privacy')";
			if(mysqli_query($conn,$sql))
			{
				header('Location: home.php');
			}
		}
	}
	
	//posting ended
	
	//newsfeed query started
	
	$feed="";
	$num_likes =0;
	$num_comments =0;
	$sql= "SELECT * FROM post where ( posted_by = '$uname' || posted_to = '$uname') ORDER BY id DESC LIMIT 10";
	$get_posts = mysqli_query($conn, $sql) or die("Could not load!!");
 	while($result = mysqli_fetch_array($get_posts))
	{
		$id = $result['id'];
		$body = $result['body'];
		$posted_by = $result['posted_by'];
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
		
		$sql = "SELECT * FROM post_likes WHERE post_id = '$id'";
		$get_likes = mysqli_query($conn, $sql);
		$num_likes = mysqli_num_rows($get_likes);
		
		if($photo!="")
		{
			$feedper = '
			<div class = "perpost fix">
				<div class = "postheader fix">
					<img src = "user/user_profile_pic/'.$pic.'" height="30px" width="30px" />
					<span class = "fullname fix"><b>'.$firstname.' '.$lastname.'</b><br/>'.$date_added.'</span>
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
						<input class = "btn_like fix" type = "submit" name = "like" value="Like"/>
						<input class = "btn_comment fix"type = "submit" name = "comment" value="Comment"/>
					</form>
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
					<img src = "user/user_profile_pic/'.$pic.'" height="30px" width="30px" />
					<span class = "fullname fix"><b>'.$firstname.' '.$lastname.'</b><br/>'.$date_added.'</span>
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
						<input class = "btn_like fix" type = "submit" name = "like" value="Like"/>
						<input class = "btn_comment fix"type = "submit" name = "comment" value="Comment"/>
					</form>
				</div>
			</div>
		
			';
			$feed .= $feedper;
		}
	}
	
	//newsfeed query ended
	
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Profile</title>
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
				<form action='profile.php' method='POST' enctype='multipart/form-data'>
					<input type = "file" name = "upload_cover" class = "upload_cover fix"/>
				</form>
			</div>
			
			<div class="propic fix">
				<img src ="user/user_profile_pic/<?php echo $pro_pic_path; ?>" alt="pp" height="250px" width="250px"/>
				<input type = "file" name = "upload_propic" class = "upload_propic fix"/>
			</div>
			<div class="frnd_msg_op fix">
				<span class="frnd fix"><a href=""><?php echo $check_frnd; ?></a></span>
				<span class="message fix"><a href=""><?php echo $msgtoothers; ?></a></span>
			</div>
			<div class="middle_area fix">
				<div class="middle_left fix">
					<div class="prof fix">
						<button>Edit your profile</button>
					</div>
				</div>
				
				<div class = "middle_right fix">
					<div class = "middle_top fix">
						<?php include 'includes/post.php'; ?>
					</div>
				
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
	</body>
</html>