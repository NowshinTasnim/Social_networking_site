<?php
	include 'includes/session.php';
	include 'database/databasecon.php';
	
	if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']== false)
	{
		header("Location: index.php");
	}
	
	$uname = $_SESSION['username'];
	$email = $_SESSION['useremail'];
	
	$dest = 'home.php';
	
	//getting first, last name and profile pic path started
	$sql = "SELECT * FROM users WHERE username='$uname' ";
	$get_data = mysqli_query($conn, $sql);
	$get_data_array = mysqli_fetch_assoc($get_data);
	
	//getting first name
	
	$get_firstname = $get_data_array['firstname'];
	
	//getting last name
	
	$get_lastname = $get_data_array['lastname'];
	
	//getting profile pic path
	
	$get_pro_pic_path = $get_data_array['profile_pic'];
	
	//getting first name and profile pic path ended
	
	//posting started
	if(isset($_POST['post']))
	{
		//trimed the post 
		$post = trim($_POST['posttext']);

		//checking null or not
		if($post != "" || isset($_FILES['photo']))
		{
			$date_added = date("Y-m-d");
			$posted_by = $uname;
			$posted_to = $uname;
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
							echo "Couldn't upload! Please try again!";
						}
					}
					else
					{
						echo "File size must be within 5MB";
					}
				}
			}
			else{
				$sql="INSERT INTO post(body, date_added, posted_by, posted_to, privacy) VALUES('$post', '$date_added','$posted_by', '$posted_to', '$privacy')";
				if(mysqli_query($conn,$sql))
				{
					header('Location: home.php');
				}
			}
		}
	}
	
	//posting ended
	
	//newsfeed query started
	
	$feed="";
	$num_likes =0;
	$num_comments =0;
	$sql= "SELECT * FROM post where ( posted_by = '$uname' OR posted_by = (SELECT friend_with FROM friend WHERE user = '$uname') AND ( privacy = 'Public' OR privacy = 'Friends')) ORDER BY id DESC";
	
	$get_posts = mysqli_query($conn, $sql);
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
		
		$goto_pb="";
		$goto_pt ="";
		
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
		
		//get_likes
		$sql = "SELECT * FROM post_likes WHERE post_id = '$id'";
		$get_likes = mysqli_query($conn, $sql);
		$num_likes = mysqli_num_rows($get_likes);
		
		//get_comments
		$sql = "SELECT * from post_comments where post_id = '$id'";
		$get_comments = mysqli_query($conn,$sql);
		$num_comments = mysqli_num_rows($get_comments);
		$comments="";
		$goto_prfl="";
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
						<span class = "com_name fix"><b><a href ="'.$goto_prfl.'">'.$com_fname.' '.$com_lname.'</a></b></span>
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
					<a href ="'.$goto_pb.'"><img src = "user/user_profile_pic/'.$pic.'" height="30px" width="30px" /></a>
					<span class = "fullname fix"><b><a href ="'.$goto_pb.'">'.$firstname.' '.$lastname.'</a></b>'.$discription.'<b><a href ="'.$goto_pt.'">'.$posted_to_fname.' '.$posted_to_lname.'</a></b><br/>'.$date_added.'</span>
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
					<a href ="'.$goto_pb.'"><img src = "user/user_profile_pic/'.$pic.'" height="30px" width="30px" /></a>
					<span class = "fullname fix"><b><a href ="'.$goto_pb.'">'.$firstname.' '.$lastname.'</a></b>'.$discription.'<b><a href ="'.$goto_pt.'">'.$posted_to_fname.' '.$posted_to_lname.'</a></b><br/>'.$date_added.'</span>
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
	
	//newsfeed query ended
	
	
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Home</title>
		<link rel="stylesheet" type="text/css" href="css/home.css">
		<link rel="stylesheet" type="text/css" href="css/mainnav.css">
		<link rel="stylesheet" type="text/css" href="css/leftnav.css">
		<link rel="stylesheet" type="text/css" href="css/post.css">
		<link rel="stylesheet" type="text/css" href="css/recentactivity.css">
		
	</head>
	
	<body>
		<div class = "main fix">
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
				<div class = "middle_top fix">
				<?php include 'includes/post.php'; ?>
				</div>
				
				<div class = "middle fix">
					<div class="middle_head fix">
						NEWSFEED
					</div>
					<div class="posts fix">
						<!-- Newsfeed-->
						<?php echo $feed; ?>
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
		
		<script type="text/javascript" src="js/bootstrap-filestyle.min.js"> </script>
		<script type="text/javascript">
			$('.photo_up').filestyle({
				text : 'Photos',
				dragdrop : false;
			});
		</script>
	</body>
</html>