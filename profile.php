<?php
	include 'includes/session.php';
	include 'database/databasecon.php';
	
	if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']== false)
	{
		header("Location: index.php");
	}
	
	//get_logged_in_user_data
	$uname = $_SESSION['username'];
	$email = $_SESSION['useremail'];
	
	$dest = 'profile.php';
	
	//getting_cover_pic_started
	$cov_pic = "images/default_cover.png";
	$sql = "SELECT * FROM users where (username = '$uname' AND cover_pic != '') LIMIT 1";
	$get_cover_pic = mysqli_query($conn, $sql);
	$num_cov = mysqli_num_rows($get_cover_pic);
	if($num_cov>0)
	{
		$get_cov = mysqli_fetch_assoc($get_cover_pic);
		$cov_pic = "user/user_cover_pic/".$get_cov['cover_pic'];
	}
	//getting_cover_pic_ended
	
	//getting_pro_pic_started
	$sql = "SELECT * FROM users where username = '$uname' LIMIT 1";
	$get_pro_pic = mysqli_query($conn, $sql);
	$get_pro_pic = mysqli_fetch_assoc($get_pro_pic);
	$pro_pic_path = $get_pro_pic['profile_pic'];
	//getting_pro_pic_ended
	
	
	//coverphoto update started
	if(isset($_POST['change_cover']))
	{
		if(isset($_FILES['upload_cover']))
		{
			//extraxting name and extention
				$cover_name = $_FILES['upload_cover']['name'];
				$file_basename = substr($cover_name,0,strripos($cover_name, '.'));
				$file_ext = substr($cover_name,strripos($cover_name, '.'));
				$type = $_FILES['upload_cover']['type'];
				$size = $_FILES['upload_cover']['size'];
			//checking type
				
				if($type == 'image/jpeg' || $type == 'image/JPEG' || $type == 'image/jpg' || $type == 'image/png')
				{
					if($size>=10 && $size<=5000000)
					{
						
						if(!file_exists("user/user_cover_pic/$uname"))
						{
							mkdir("user/user_cover_pic/$uname");
						}
						if(!file_exists("user/user_images/$uname"))
						{
							mkdir("user/user_images/$uname");
						}
						
						$filename = strtotime(date('Y-m-d H:i:s')).$file_ext;
						$tempname = $_FILES['upload_cover']['tmp_name'];
						$cphoto = "$uname/$filename";
						move_uploaded_file($tempname,"user/user_cover_pic/$uname/".$filename);
						copy("user/user_cover_pic/$uname/".$filename,"user/user_images/$uname/".$filename);
						$sql = "UPDATE users SET cover_pic = '$cphoto' WHERE username = '$uname'";
					
						if(mysqli_query($conn,$sql))
						{
							$date_added = date("Y-m-d");
							$posted_by = $uname;
							$posted_to = $uname;
							$privacy = "Public";
							$discription = " has Updated Cover Pic.";
							$post = $_POST['posttext'];
							$sql = "INSERT INTO post(body, date_added, posted_by, posted_to, discription, photos, privacy) VALUES('$post', '$date_added','$posted_by', '$posted_to', '$discription', '$cphoto', '$privacy')";
							mysqli_query($conn,$sql);
							header('Location: profile.php');					
						}
						else
						{
							echo "
							<script type='text/javascript'>
							alert(Couldn't upload! Please try again!);
							</script>";
						}
					}
					else
					{
						echo "
						<script type='text/javascript'>
							alert(File size must be within 5MB!);
						</script>";
					}
				}
		}
	}
	//coverphoto update ended
	
	
	//profilephoto update started
	if(isset($_POST['change_pp']))
	{
		if(isset($_FILES['edit_pp']))
		{
			//extraxting name and extention
				$pro_pic_name = $_FILES['edit_pp']['name'];
				$file_basename = substr($pro_pic_name,0,strripos($pro_pic_name, '.'));
				$file_ext = substr($pro_pic_name,strripos($pro_pic_name, '.'));
				$type = $_FILES['edit_pp']['type'];
				$size = $_FILES['edit_pp']['size'];
				
			//checking type
				
				if($type == 'image/jpeg' || $type == 'image/jpg' || $type == 'image/png')
				{
					if($size>=10 && $size<=5000000)
					{
						if(!file_exists("user/user_profile_pic/$uname"))
						{
							mkdir("user/user_profile_pic/$uname");
						}
						
						if(!file_exists("user/user_images/$uname"))
						{
							mkdir("user/user_images/$uname");
						}
						
						$filename = strtotime(date('Y-m-d H:i:s')).$file_ext;
						$tempname = $_FILES['edit_pp']['tmp_name'];
						$pphoto = "$uname/$filename";
						move_uploaded_file($tempname,"user/user_profile_pic/$uname/".$filename);
						copy("user/user_profile_pic/$uname/".$filename,"user/user_images/$uname/".$filename);
						$sql = "UPDATE users SET profile_pic = '$pphoto' WHERE username = '$uname'";
					
						if(mysqli_query($conn,$sql))
						{
							
							$date_added = date("Y-m-d");
							$posted_by = $uname;
							$posted_to = $uname;
							$privacy = "Public";
							$discription = " has Updated Profile Pic.";
							$post = $_POST['posttext'];
							$sql = "INSERT INTO post(body, date_added, posted_by, posted_to, discription, photos, privacy) VALUES('$post', '$date_added','$posted_by', '$posted_to', '$discription', '$pphoto', '$privacy')";
							mysqli_query($conn,$sql);
							header('Location: profile.php');					
						}
						else
						{
							echo "
							<script type='text/javascript'>
							alert(Couldn't upload! Please try again!);
							</script>";
						}
					}
					else
					{
						echo "
						<script type='text/javascript'>
							alert(File size must be within 5MB!);
						</script>";
					}
				}
		}
	}
	//profilephoto update ended
	
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
				$discription = " has Added a new photo.";
				
				//extraxting name and extention
				$pic_name = $_FILES['photo']['name'];
				$file_basename = substr($pic_name,0,strripos($pic_name, '.'));
				$file_ext = substr($pic_name,strripos($pic_name, '.'));
				$type = $_FILES['photo']['type'];
				$size = $_FILES['photo']['size'];
				
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
						$sql = "INSERT INTO post(body, date_added, posted_by, posted_to, discription, photos, privacy) VALUES('$post', '$date_added','$posted_by', '$posted_to', '$discription', '$photo', '$privacy')";
					
						if(mysqli_query($conn,$sql))
						{
							header('Location: profile.php');					
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
				$sql = "INSERT INTO post(body, date_added, posted_by, posted_to, privacy) VALUES('$post', '$date_added','$posted_by', '$posted_to', '$privacy')";
				if(mysqli_query($conn,$sql))
				{
					header('Location: profile.php');
				}
			}
			
		}
	}
	
	//posting ended
	
	//newsfeed query started
	
	$feed="";
	$num_likes =0;
	$num_comments =0;
	$sql= "SELECT * FROM post where (posted_by = '$uname' AND posted_to = '$uname') OR posted_to = '$uname' ORDER BY id DESC";
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
		$goto_pb="profile.php";
		$posted_to_fname="";
		$posted_to_lname="";
		
		if($posted_by != $posted_to)
		{
			$sql = "SELECT * FROM users WHERE username = '$posted_to'";
			$get_info = mysqli_query($conn, $sql);
			$get_info = mysqli_fetch_assoc($get_info);
			$posted_to_fname = $get_info['firstname'];
			$posted_to_lname = $get_info['lastname'];
			$goto_pb = "profile_others.php?u=$posted_by";
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
					<a href="'.$goto_pb.'"><img src = "user/user_profile_pic/'.$pic.'" height="30px" width="30px" /> </a>
					<span class = "fullname fix"><b><a href="'.$goto_pb.'"> '.$firstname.' '.$lastname.' </a></b>'.$discription.'<b><a href="profile.php">'.$posted_to_fname.' '.$posted_to_lname.'</a></b><br/>'.$date_added.'</span>
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
					<span class = "fullname fix"><b><a href="'.$goto_pb.'">'.$firstname.' '.$lastname.'</a></b>'.$discription.'<b><a href="profile.php">'.$posted_to_fname.' '.$posted_to_lname.'</a></b><br/>'.$date_added.'</span>
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
	
	//newsfeed query ended
	
	//edit profile started
	if(isset($_POST['add_to_profile']))
	{
		if(isset($_POST['city']) && $_POST['city'] !=''){
			$city = $_POST['city'];
			$sql = "Update users SET city = '$city' WHERE username = '$uname' ";
			mysqli_query($conn,$sql);
		}
		if(isset($_POST['hometown']) && $_POST['hometown'] !=''){
			$hometown = $_POST['hometown'];
			$sql = "Update users SET hometown = '$hometown' WHERE username = '$uname' ";
			mysqli_query($conn,$sql);
		}
		
		if(isset($_POST['company']) && $_POST['company'] !=''){
			$company = $_POST['company'];
			$sql = "Update users SET company = '$company' WHERE username = '$uname' ";
			mysqli_query($conn,$sql);
		}
		
		if(isset($_POST['position']) && $_POST['position'] !=''){
			$position = $_POST['position'];
			$sql = "Update users SET position = '$position' WHERE username = '$uname' ";
			mysqli_query($conn,$sql);
		}
		
		if(isset($_POST['school']) && $_POST['school'] !=''){
			$school = $_POST['school'];
			$sql = "Update users SET school = '$school' WHERE username = '$uname' ";
			mysqli_query($conn,$sql);
		}
		
		if(isset($_POST['conc']) && $_POST['conc'] !=''){
			$conc = $_POST['conc'];
			$sql = "Update users SET concentration = '$conc' WHERE username = '$uname' ";
			mysqli_query($conn,$sql);
		}
		
		if(isset($_POST['relation']) && $_POST['relation'] != 'none'){
			$relation = $_POST['relation'];
			$sql = "Update users SET relationship = '$relation' WHERE username = '$uname' ";
			mysqli_query($conn,$sql);
		}
	}
	//edit profile ended
	
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
				<button class="upload_coverp fix" onclick="open_cover_popup()">Edit Cover pic</button>
			</div>
			
			<div class="propic fix">
				<img src ="user/user_profile_pic/<?php echo $pro_pic_path; ?>" alt="pp" height="250px" width="250px"/>
			</div>
			<div class="middle_area fix">
				<div class="middle_left fix">
					<div class="prof fix">
						<button class = "btn_upload_propic fix" onclick="open_pp_popup()">Edit Profile Pic</button>
						<button class="edit_prof fix" onclick="open_edit_popup()">Edit your profile</button>
					</div>
				</div>
				
				<!-- Edit cover pic -->
				<div class="popup2 fix" id="edit_cover">
					<form action="profile.php" enctype='multipart/form-data' class="popup_container2 fix" method="POST">
						<button class="close" onclick="close_cover_popup()">&times;</button>
						<h2>Cover pic Upload</h2>
					<!--	<input id="new_pp" class="new_pp fix" src="#" alt=" Upload new pro pic" height="150px">
					-->	
						<input type = "file" name = "upload_cover" class = "edit_pp fix"/>
						<textarea type='text' name='posttext' rows='4' cols='50' class='covpost_text fix'></textarea>
						<br/>
						<button class="add_pp fix" type="submit" name="change_cover">Change cover Pic</button>
					</form>
				</div>
				
			
				
				<!-- edit pro pic popup-->
				<div class="popup2 fix" id="edit_pp">
					<form action="profile.php" enctype='multipart/form-data' class="popup_container2 fix" method="POST">
						<button class="close" onclick="close_pp_popup()">&times;</button>
						<h2>Profile pic Upload</h2>
					<!--	<input id="new_pp" class="new_pp fix" src="#" alt=" Upload new pro pic" height="150px">
					-->	
						<input type="file" id="up_pp" name="edit_pp" class="edit_pp fix"/>
						<textarea type='text' name='posttext' rows='4' cols='50' class='covpost_text fix'></textarea>
						<br/>
						<button class="add_pp fix" type="submit" name="change_pp">Change Profile Pic</button>
					</form>
				</div>
				
				<!-- edit profile info popup -->
				<div class="popup fix" id="edit_profile">
					<form action="profile.php" class="popup_container fix" method="POST">
						<button class="close" onclick="close_edit_popup()">&times;</button>
						<h2>Lives in</h2>
						<label>City</label>
						<input type="text" name="city" placeholder="City name"/>
						
						<label>Hometown</label>
						<input type="text" name="hometown" placeholder="Hometown name"/>
						
						<h2>Education</h2>
						<label>School</label>
						<input type="text" name="school" placeholder="School name"/>
						
						<label>Concentration</label>
						<input type="text" name="conc" placeholder="Your Concentration"/>
						
						<h2>Work</h2>
						<label>Company</label>
						<input type="text" name="company" placeholder="Company name"/>
						
						<label>Position</label>
						<input type="text" name="position" placeholder="Your position"/>
						
						<h2>Personal Info</h2>
						<label>Relationship status</label>
						<select name="relation">
							<option>none</option>
							<option>Single</option>
							<option>In a relationship</option>
							<option>Complicated</option>
						</select>
						<br/>
						<button class="add_to_profile fix" type="submit" name="add_to_profile">Save</button>
					</form>
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
		
		<script>
			function show_comment_box()
			{
				document.getElementById('comment_area').style.display ='block';
			}
	
			function open_edit_popup() {
				document.getElementById("edit_profile").style.display = "block";
			}

			function close_edit_popup() {
				document.getElementById("edit_profile").style.display = "none";
			}
			
			
			function open_pp_popup() {
				document.getElementById("edit_pp").style.display = "block";
			}

			function close_pp_popup() {
				document.getElementById("edit_pp").style.display = "none";
			}
			
			function open_cover_popup() {
				document.getElementById("edit_cover").style.display = "block";
			}

			function close_cover_popup() {
				document.getElementById("edit_cover").style.display = "none";
			}
		</script>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
		<script>
			$(document).ready(function()
			{
				$('#up_pp').change(function()
				{
					readimg(this);
				})
			
				function readimg(file)
				{
					if(file.files&&file.files[0])
					{
						var reader = new FileReader();
						reader.onload=function(e)
						{
							$('#new_pp').attr('src',e.target.result);
						}
						reader.readAsDataURL(file.files[0]);
					}
				}
			})	
		</script>
		
		
	</body>
</html>