<?php
	include 'includes/session.php';	
	include 'database/databasecon.php';
	
	if(!isset($_SESSION['username']))
	{
		header('location: index.php');
	}
	
	$uname = $_SESSION['username'];
	$email = $_SESSION['useremail'];
	$error="";
	
	if($_SESSION['flag'] == 1)
	{
		$middown = "
			<form action='signup_success.php' method='POST' enctype='multipart/form-data' >
				<label>Profile picture:</label><input name='propic' type='file' class='propic_up fix' />
				<input name='uploadpp' type='submit' class='btnupload fix' value='Upload' />
			</form>
		";
	}
	
	if($_SESSION['flag'] == 2)
	{
		$_SESSION['flag'] = 0;
		$middown="
			<p class='glog fix'>Successfully uploaded !!!<a href='index.php'>Go to login!</a></p>
		";
	}
	
	//upload pro pic started
	
	if(isset($_POST['uploadpp']))
	{
		if(isset($_FILES['propic']))
		{
			//extraxting name and extention
			$profile_pic_name = $_FILES['propic']['name'];
			$file_basename = substr($profile_pic_name,0,strripos($profile_pic_name, '.'));
			$file_ext = substr($profile_pic_name,strripos($profile_pic_name, '.'));
			$type = $_FILES['propic']['type'];
			$size= $_FILES['propic']['size'];
			//checking type
				
			if($type == 'image/jpeg' || $type == 'image/jpg' || $type == 'image/png')
			{
				if($size>=10 && $size<=5000000)
				{
					if(!file_exists("user/user_profile_pic/$uname"))
					{
						mkdir("user/user_profile_pic/$uname");
					}
						
					$filename = strtotime(date('Y-m-d H:i:s')).$file_ext;
					$tempname = $_FILES['propic']['tmp_name'];
					$photo = "$uname/$filename";
					move_uploaded_file($tempname,"user/user_profile_pic/$uname/".$filename);
					$sql="UPDATE users SET profile_pic = '$photo' WHERE username = '$uname' ";
					if(mysqli_query($conn,$sql))
					{
						$_SESSION['flag']=2;
						header('Location: dummy/signup_successvia.php');
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
			else
			{
				$error="File type must be either .jpg, .jpeg, .png";
			}
		}
		else{
			$error="Please Choose profile picture!";
		}
	}
	
	//upload pro pic ended
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Successfully signed up!</title>
		<link rel="stylesheet" type="text/css" href="css/signup_success.css"/>
		<link rel="stylesheet" type="text/css" href="css/footer.css"/>
	</head>
	
	<body>
		<!-- Upper part started -->
		<div class="upper fix">
			<!-- Logo part started -->
			<div class="logo fix">
				<img src="images/logo.png" alt="logo" height="75px" width="230px" />
			</div>
			<!-- Logo part ended -->
		</div>
		<!-- Upper part ended -->
		
		<!-- Middle part started -->
		<div class="middle fix">
			<div class="middleup fix" align="center">
				<p class="suc fix">You have successfully signed up!!</p>
				<p>
					<label>User Name:</label> <?php echo $uname; ?> <br/>
					<label>Email:</label> <?php echo $email; ?> <br/>
				</p>	
			</div>
			<div class="middledown fix" align="center">
				<?php echo $middown;?>
				<div class="ermsg fix"><?php echo $error;?></div>
			</div>
		</div>
		<!-- Middle part ended -->
		
		<footer class="footerarea fix">
			<p><?php include 'includes/footer.php';?></p> 
		</footer>
	</body>
</html>
