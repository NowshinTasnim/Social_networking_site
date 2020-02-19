<?php
	include 'includes/session.php';	
	include 'database/databasecon.php';
	
	if(!isset($_SESSION['username']))
	{
		header('location: index.php');
	}
	
	$uname = $_SESSION['username'];
	$email = $_SESSION['useremail'];
	
	$posts="";
	
	//set opened 
	$sql = "UPDATE friend_request SET opened = 'yes' WHERE user_to = '$uname'";
	$checked = mysqli_query($conn, $sql);
	
	//get frnd requests started
	$sql = "SELECT * from friend_request where user_to = '$uname'";
	$result = mysqli_query($conn, $sql);
	
	//number of frnd request
	$num_frnd_req = mysqli_num_rows($result);
	$posts = "
			<div class='frnd_req_banner fix'>Friend Request : ".$num_frnd_req." </div>
		";
	
	// details	
	if($num_frnd_req != 0)
	{
		while($frnd_result = mysqli_fetch_array($result))
		{
			$username = $frnd_result['user_from'];
			$sql = "SELECT * from users where username = '$username'";
			$get_sql = mysqli_query($conn, $sql);
			$get_data = mysqli_fetch_assoc($get_sql);
			
			$firstname = $get_data['firstname'];
			$lastname = $get_data['lastname'];
			$pp_path = $get_data['profile_pic'];
			$city = $get_data['city'];
			$hometown = $get_data['hometown'];
			$school = $get_data['school'];
			$company = $get_data['company'];
			
			$posts .= "
					<div class = 'resultarea fix'>
						<a href = 'profile_others.php?u=".$username."'> <img class = 'sender_propic fix' src = 'user/user_profile_pic/".$pp_path."' height= '80px' width = '80px' alt = 'pro_pic' /></a>
						<div class = 'sender_info fix'>
							<a href = 'profile_others.php?u=".$username."'>".$firstname." ".$lastname."</a><br/> 
							<b>Hometown: </b>".$hometown."<br/>
							<b>Current city: </b>".$city." <br/>
							<b>School: </b>".$school."<br/>
							<b>Company: </b>".$company." <br/>
						</div>
						<div class='btn_sender_per fix'>
							<form action= 'frnd_req.php' method = 'POST'>
								<input type = 'hidden' name = 'user_from' value ='".$username."'>
								<input type= 'submit' name = 'frnd_req' class = 'frnd_send fix' value = 'Accept'/>
							</form>
									
							<form action ='frnd_req.php' method = 'POST'>
								<input type = 'hidden' name = 'user_from' value ='".$username."'>
								<input type= 'submit' name = 'frnd_req' class = 'frnd_delete fix' value = 'Delete'/>
							</form>
						</div>
					</div>
					";
		}
	}
	//get frnd request ended
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Friend Request</title>
		<link rel="stylesheet" type="text/css" href="css/mainnav.css">
		<link rel="stylesheet" type="text/css" href="css/leftnav.css">
		<link rel="stylesheet" type="text/css" href="css/frndRequest.css">
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
					<?php echo $posts; ?>
			</div>
		</div>
		
	</body>
</html>