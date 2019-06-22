<!DOCTYPE html>
<?php 
	include 'includes/session.php';
	include 'database/databasecon.php';
	
	$_SESSION['flag']=0;
	$fname="";
	$lname = "";
	$uname="";
	$pass="";
	$cpass="";
	$email="";
	$pp="";
	$cnumber="";
	$gender="";
	$bday="";
	$cntry="";
	$a="";
	$e="";
	$uc="";
	$pfill="";
	$d="";
	
	//usernamecheck started
	
	if(isset($_POST['u_name']) && $_POST['u_name']!="")
	{
		$uname=$_POST['u_name'];
		$uname=trim($uname);
		$uname=strtolower($uname);
		$sql="SELECT user_id FROM users WHERE username='$uname' LIMIT 1";
		$sql_uname_check = mysqli_query($conn, $sql);
		$uname_check=mysqli_num_rows($sql_uname_check);
		if($uname_check<1)
		{
			$uc = "";
		}
		else
		{
			$uc = "Already exists!!";
		}
		//mysqli_close($conn);
	}
	
	//usernamecheck ended
	
	//emailcheck started
	
	if(isset($_POST['email']) && $_POST['email']!="")
	{
		$email = trim($_POST['email']);
		$sql="SELECT user_id FROM users WHERE email = '$email' LIMIT 1";
		$sql_email_check = mysqli_query($conn,$sql);
		$email_check = mysqli_num_rows($sql_email_check);
		if($email_check<1)
		{
			$e="";
		}
		else
		{
			$e="Already exists!!";
		}
		//mysqli_close($conn);
	}
	
	//emailcheck ended
	
	//signup started
	
	if(isset($_POST['signup']))
	{
		if(
			!empty($_POST['f_name']) &&
			!empty($_POST['l_name']) &&
			!empty($_POST['u_name']) &&
			!empty($_POST['set_pass']) &&
			!empty($_POST['confirm_pass']) &&
			!empty($_POST['email']) &&
			//!empty($_POST['propic']) &&
			!empty($_POST['contact']) &&
			!empty($_POST['gender']) &&
			!empty($_POST['brthday']) &&
			!empty($_POST['country']) &&
			!empty($_POST['agree_check'])
		){
			$fname=trim($_POST['f_name']);
			$lname=trim($_POST['l_name']);
			$pass=$_POST['set_pass'];
			$cpass=$_POST['confirm_pass'];
			//$pp=$_POST('propic');
			$cnumber=$_POST['contact'];
			$gender=$_POST['gender'];
			$bday=$_POST['brthday'];
			$cntry=$_POST['country'];
			$pattern = "/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i";
			
			
			if(!preg_match($pattern,$email)){
				$e="Invalid Email Address";
			}
			if($cpass!=$pass){
				$a="Password do not Match";
			}
			if($e=="" && $a=="" && $uc=="")
			{
				$fname=ucwords($fname);
				$lname=ucwords($lname);
				$pass=md5($pass);
				$d= date("Y-m-d");
				$sql="INSERT INTO users(firstname,lastname,username,pass,email,contact,gender,birthday,country,reg_date) VALUES('$fname','$lname','$uname','$pass','$email','$cnumber','$gender','$bday','$cntry','$d')";
				
				if (mysqli_query($conn, $sql)) {
					$_SESSION['useremail']= $email;
					$_SESSION['username']=$uname;
					header("Location: signup_success.php");
				}
				else {
					echo "Error: " . $sql . "<br>" . mysqli_error($conn);
				}
				mysqli_close($conn);
			}
		}
		else
		{
			$pfill="Please fill up the form completely!!!";
		}
	}
	
	//signup ended
	
	// login started
	$useremailcookie="";
	$passwordcookie="";
	$notmatched="";	
	if(isset($_POST['login']))
	{
		if(isset($_POST['useremail']) && isset($_POST['password']) )
		{
			$useremail = mysqli_real_escape_string($conn,$_POST['useremail']);
			$useremail = mb_convert_case($useremail, MB_CASE_LOWER, "UTF-8");
			$password = mysqli_real_escape_string($conn,$_POST['password']);
			$num = 0;
			$password_md5= md5($password);
			$sql="SELECT * FROM users WHERE email='$useremail' AND pass='$password_md5'";
			$result= mysqli_query($conn,$sql);
			$num= mysqli_num_rows($result);
			if($num>0)
			{
				if(isset($_POST['remember']))
				{
					setcookie('useremail',$_POST['useremail'],time()+60*60*24*7);
					setcookie('password',$_POST['password'],time()+60*60*24*7);
				}
				$_SESSION['useremail'] = $useremail;
				$get_user_name = mysqli_fetch_assoc($result);
				$_SESSION['username'] = $get_user_name['username']; 
				$_SESSION['loggedin'] = true;
			}
			else
			{
				$notmatched="Email or password is invalid!!";
			}
			mysqli_close($conn);
		}
	}
	if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)
	{
		header("Location: home.php");
	}
	if(isset($_COOKIE['useremail']) && isset($_COOKIE['password']))
	{
		$useremailcookie= $_COOKIE['useremail'];
		$passwordcookie = $_COOKIE['password'];
	}
	
	//login ended
	
?>
<html>
	<head>
		<title>BUDDY</title>
		<link rel="stylesheet" type="text/css" href="css/index.css" media="all"/>
		<link rel="stylesheet" type="text/css" href="css/footer.css" media="all"/>
		
		<!-- slider content started-->
		
		<link rel="stylesheet" href="css/bar.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/nivo-slider.css" type="text/css" media="screen" />
		<script type="text/javascript" src="js/jquery-1.9.0.min.js"></script>
		<script type="text/javascript" src="js/jquery.nivo.slider.js"></script>
		<script type="text/javascript">
		$(window).load(function() {
			$('#slider').nivoSlider();
		});
		</script>
		<!--Slider content ended-->
	</head>
	
	<body>
		<!-- Upper part started -->
		<div class="upper fix">
			<!-- Logo part started -->
			<div class="logo fix">
				<img src="images/logo.png" alt="logo" height="75px" width="230px" />
			</div>
			<!-- Logo part ended -->
			
			<!-- login form started-->
			<div class="topright fix">
				<form name="log_form" method="POST" action="index.php">
					<div class="label fix">
						<div class="email fix">
							<label> USER EMAIL</label>
						</div>
						<div class="pass fix">
							<label>PASSWORD</label>
						</div>
					</div>
				
					<div class="label1 fix">
						<div class="emailtext fix">
							<input type="email" name="useremail" placeholder=" Enter your email id" value="<?php echo $useremailcookie;?>"/>
						</div>
						<div class="passtext fix">
							<input type="password" name="password" placeholder=" Enter your password" value="<?php echo $passwordcookie;?>"/>
						</div>
						<input name="login" type="submit" class="bt_log fix" value="LOGIN"/>
						<font class="not_matched fix"><?php echo '<b style="color:red; float:right;text-align:center; width:150px; background-color:white; font-size:10px;">'.$notmatched.'</b>';?></font>
						<div class="label2">
							<div class="remem fix">
								<input name="remember" type="checkbox"/><label> Remember me</label>
							</div>
							<div class="frgtpass"><a href="passRecover.php" onclick="<?php $_SESSION['flag']=1; ?>">FORGET PASSWORD?</a></div>
						</div>
					</div>
				</form>
			</div>
			<!-- login form ended-->
		</div>
		<!-- Upper part ended -->
		
		<!-- Down part started -->
		<div class="down fix">
			<div class="dleft fix">
				<font class="pfill fix"><?php echo '<b style="color:red; margin-left:50px; font-size:30px;">'.$pfill.'</b>';?></font>
				<!-- Slide pic started -->
				<div class="slider-wrapper theme-bar">
					<div id="slider" class="nivoSlider">
						<img src="images/sl_1.png" data-thumb="images/sl_1.png" height= "400px" width="600px" alt="communication" />
						<img src="images/sl_2.jpg" data-thumb="images/sl_2.jpg" height= "400px" width="600px" alt="chatting" />
						<img src="images/sl_3.png" data-thumb="images/sl_3.png" height= "400px" width="600px" alt="network" data-transition="slideInLeft" />
						<img src="images/sl_4.png" data-thumb="images/sl_4.png" height= "400px" width="600px" alt="post" />
					</div>
				</div>
				<!-- Slide pic started -->
			</div>
			
			
			<div class="dright fix">
				<div class="hvnac fix">Don't have an account?</div>
				<div class="sgup fix">CREATE AN ACCOUNT<br/>(It's totally free!!)</div>
				<div class="sign_form fix">
					<!-- Signup form started -->
					<form name="signup_form" action="" method="POST" enctype="multipart/form-data">
						<div class="textleft fix">First name:</div>
						<div class="textright fix">
							<input name="f_name" type="text" placeholder=" Enter your first name" value="" maxlength="30" required />
						</div>
						<div class="textleft fix">Last name:</div>
						<div class="textright fix">
							<input name="l_name" type="text" placeholder=" Enter your last name" value="" maxlength="30" required />
						</div>
						<div class="textleft fix">User name:</div>
						<div class="textright fix">
							<input name="u_name" type="text" placeholder=" Enter your User name" value="" maxlength="30" required />
							<font color="Red"><?php echo $uc; ?> </font>
						</div>
						<div class="textleft fix">Password:</div>
						<div class="textright fix">
							<input name="set_pass" type="password" placeholder=" Set password" value="" required />
						</div>
						<div class="textleft fix">Confirm Password:</div>
						<div class="textright fix">
							<input name="confirm_pass" type="password" placeholder=" Retype password" value="" required />
							<font color="Red"><?php echo $a; ?> </font>
						</div>
						<div class="textleft fix">Email:</div>
						<div class="textright fix">
							<input name="email" type="email" placeholder=" Enter your email id" value="" required />
							<font color="Red"><?php echo $e; ?> </font>
						</div>
						<div class="textleft fix">Contact number:</div>
						<div class="textright fix">
							<input name="contact" type="text" placeholder=" Enter your contact number" value="" required />
						</div>
						<div class="textleft fix">Gender:</div>
						<div class="textright fix">
							<input type="radio" name="gender" value="male"/>Male &nbsp;
							<input type="radio" name="gender" value="female"/>Female
						</div>
						<div class="textleft fix">Birthday:</div>
						<div class="textright fix">
							<input name="brthday" type="date" value="" required />
						</div>
						<div class="textleft fix">Country:</div>
						<div class="textright fix">
							<input name="country" type="text" placeholder=" Enter your country name" value="" required />
						</div>
						<div class="textleft1 fix">By clicking "I Agree" you agree to our <a href="terms_and_conditions.pdf" target="_blank">Terms & conditions </a></div>
						<div class="textleft2 fix">
							<input type="checkbox" name="agree_check" /> <label>I Agree</label>
						</div>
						<div class="textright1 fix">
							<input name="signup" type="submit" value="Sign UP" class="btnsignup fix"/>
						</div>
					</form>
					<!-- Signup form started -->
				</div>
			</div>
		</div>
		<!-- Downpart ended -->
		<footer class="footerarea fix">
			<p><?php include 'includes/footer.php';?></p> 
		</footer>
	</body>
</html>

