<?php
	include 'includes/session.php';	
	include 'database/databasecon.php';
	
	if(!isset($_SESSION['username']))
	{
		header('location: index.php');
	}
	
	$uname = $_SESSION['username'];
	$email = $_SESSION['useremail'];
	
	$dest = "search.php";
	
	$posts = "";
	if(isset($_POST['search']))
	{
		if( 
			(isset($_POST['searcharea']) && $_POST['searcharea'] != NULL) &&
			(isset($_POST['search_op']) && $_POST['search_op'] != NULL)
		){
			if($_POST['search_op'] == "User")
			{
				$search_value = trim($_POST['searcharea']);
				
				$sql = "SELECT username,firstname,lastname,profile_pic,city,hometown,school,company FROM users WHERE ((username like '%$search_value%' OR firstname like '%$search_value%' OR lastname like '%$search_value%') AND closed = 'no')";
				$get_search_result = mysqli_query($conn,$sql) or die ("Could not count!!!");
				$num_search_result = mysqli_num_rows($get_search_result);
				if($num_search_result == 0)
				{
					$posts = "
						<div class = 'search_banner fix'>No match found!!!</div>
					";
				}
				else
				{
					
					$posts = "
						<div class = 'search_banner fix'>Result for:
							<span class='search_for fix'>".$search_value."</span><br/>
							<div class='search_num fix'>".$num_search_result." matches found.</div>
						</div>
					";
					while($search_result = mysqli_fetch_array($get_search_result))
					{
						$username = $search_result['username'];
						if($username!=$uname)
						{
							$firstname = $search_result['firstname'];
							$lastname = $search_result['lastname'];
							$pp_path = $search_result['profile_pic'];
							$city = $search_result['city'];
							$hometown = $search_result['hometown'];
							$school = $search_result['school'];
							$company = $search_result['company'];
							
							$frnd = "Send Friend Request";
							$check_frnd_sql = "SELECT * FROM friend where user= '$uname' AND friend_with= '$username' LIMIT 1";
							$check_frnd_result = mysqli_query($conn,$check_frnd_sql);
							$num_frnd_result = mysqli_num_rows($check_frnd_result);
							
							if($num_frnd_result == 1)
							{
								$frnd = "Friend";
							}
							
							$check_frnd_sql = "SELECT * FROM friend_request where user_from = '$uname' AND user_to = '$username' LIMIT 1";
							$check_frnd_result = mysqli_query($conn,$check_frnd_sql);
							$num_frnd_result = mysqli_num_rows($check_frnd_result);
							
							if($num_frnd_result == 1)
							{
								$frnd = "Friend Request Sent";
							}
							
							$posts .= "
							<div class = 'resultarea fix'>
								<a href = 'profile_others.php?u=".$username."'> <img class = 'search_propic fix' src = 'user/user_profile_pic/".$pp_path."' height= '80px' width = '80px' alt = 'pro_pic' /></a>
								<div class = 'search_info fix'>
									<a href = 'profile_others.php?u=".$username."'>".$firstname." ".$lastname."</a><br/> 
									<b>Hometown: </b>".$hometown."<br/>
									<b>Current city: </b>".$city." <br/>
									<b>School: </b>".$school."<br/>
									<b>Company: </b>".$company." <br/>
								</div>
								<div class='btn_search_per fix'>
									<?php if( $frnd != 'Friend'){?>
									<form action= 'frnd_req.php' method = 'POST'>
										<input type = 'hidden' name = 'destination' value ='".$dest."'>
										<input type = 'hidden' name = 'user_to' value ='".$username."'>
										<input type= 'submit' name = 'frnd_req' class = 'frnd_send fix' value = '".$frnd."'/>
									</form>
								</div>
							</div>
							<?php } ?>
							";
						}
						
					}
				}
			}
			else if($_POST['search_op'] == "Post")
			{
				
			}
		}
		else
		{
			header('Location: home.php');
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Search Result</title>
		<link rel="stylesheet" type="text/css" href="css/search.css">
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