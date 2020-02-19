<?php

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
	
	echo "
		<div class = 'left fix'>
			<img class = 'pro_pic_side fix' src = 'user/user_profile_pic/".$get_pro_pic_path."' alt='pro_pic_side' height='100px' width='100px' />
			<div class='name fix'>
				<p class='first_name fix'>".$get_firstname."</p>
				<p class='last_name fix'>".$get_lastname."</p>
			</div>
			<ul class='side_nav fix'>
				<li><a href='home.php'>Newsfeed</a></li>
			</ul>
		</div>
	";
?>