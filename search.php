<?php
	include 'includes/session.php';	
	include 'database/databasecon.php';
	
	if(!isset($_SESSION['username']))
	{
		header('location: index.php');
	}
	
	$uname = $_SESSION['username'];
	$email = $_SESSION['useremail'];
	
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
						$firstname = $search_result['firstname'];
						$lastname = $search_result['lastname'];
						$pp_path = $search_result['profile_pic'];
						$city = $search_result['city'];
						$hometown = $search_result['hometown'];
						$school = $search_result['school'];
						$company = $search_result['company'];
						
						$posts .= "
							<div class = 'resultarea fix'>
								<img class = 'search_propic fix' src = 'user/user_profile_pic/".$pp_path."' height= '50px' width = '50px' alt = 'pro_pic' />
								<span class = 'search_info fix'>
								".$firstname." ".$lastname."<br/> 
								".$hometown.", ".$city."
								</span>
							</div>
						";
						
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
	</head>
	
	<body>
		<div class="main fix">
			<div class="top_nav fix">
				<?php include 'includes/mainnav.php'; ?>
			</div>
			<div class="left_nav fix">
				<?php include 'includes/leftnav.php'; ?>
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