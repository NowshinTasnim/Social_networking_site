<?php
	include 'database/databasecon.php';
	include 'includes/session.php';
	$not_matched = "";
	$uname = "";
	$email = "";
	$confirmCode = "";
	$vericode = "";
	$newpass = "";
	$cnewpass = "";
	$up = "";
	$mid = "";
	
	//first view started
	
	if($_SESSION['flag'] == 1)
	{
		$up="
			<p>Please enter your username and email!!</p>
		";
		$mid="
			<form name='recovery_form' action='passRecover.php' method='POST'>
				<div class='rec_lab fix'>User Name:</div>
				<div class='rec_in fix'>
					<input type='text' name='u_name' placeholder=' Enter your user name' value='' />
				</div>
				<div class='rec_lab fix'>Email:</div>
				<div class='rec_in fix'>
					<input type='email' name='useremail' placeholder=' Enter your email id' value='' />
				</div>
				<div class='btn_recovery fix'>
					<input type='submit' name='sendrecovery' value='Send Recovery Password' />
				</div>
				<div class='btn_back fix'>
					<input type='submit' name='back' value='Back to Login' />
				</div>
			</form>
		";
	}
	
	//first view ended
	
	//second view started
	
	else if($_SESSION['flag'] == 2)
	{
		$up="
			<p>Plese Enter the varification code and new password!</p>
		";
		$mid="
			<form class='confirm_form fix' name='confirmation' method='POST' action='passRecover.php'>
				<label>Verification Code: </label><div class='cf_in fix'><input type='text' name='veri_code' placeholder=' Enter Verify code' value='' /></div>
				<label>New Password: </label><div class='cf_in fix'><input type='password' name='new_pass' placeholder=' Enter Password' value='' /></div>
				<label>Retype Password: </label><div class='cf_in fix'><input type='password' name='cnew_pass' placeholder=' Re-enter Password' value='' /></div>
				<div class='bt_changepass fix'>
					<input type='submit' name='changepass' value='Change your password' />
				</div>
				<div class='btn_back fix'>
					<input type='submit' name='back' value='Back to Login' />
				</div>
			</form>
		";
	}
	
	//second view ended
	
	//third view started
	
	else if($_SESSION['flag'] == 3)
	{
		$up="
			<p>Successfully Updated your Password!!</p>
		";
		$mid="
			<form method='POST'>
				<div class='btn_back fix'>
					<input type='submit' name='back' value='Go to Login Page!' />
				</div>
			</form>
		";
		
	}
	
	//third view ended
	
	//backtologinpage started
	
	if(isset($_POST['back']))
	{
		header("Location: index.php");
	}
	
	//backtologinpage ended
	
	//email username check started
	
	if(isset($_POST['sendrecovery']))
	{
		if( !empty($_POST['u_name']) &&
			!empty($_POST['useremail'])
		)
		{
			$uname = trim($_POST['u_name']);
			$uname = strtolower($uname);
			$email = trim($_POST['useremail']);
			$sql = "SELECT * FROM users WHERE username='$uname' AND email='$email' LIMIT 1";
			$sql_uname_email_check = mysqli_query($conn,$sql);
			$uname_email_check = mysqli_num_rows($sql_uname_email_check);
			if($uname_email_check<1)
			{
				$sql = "SELECT * FROM users WHERE username='$uname' LIMIT 1";
				$sql_uname_check = mysqli_query($conn,$sql);
				$uname_check = mysqli_num_rows($sql_uname_check);
				$uname_fetch_query= mysqli_fetch_assoc($sql_uname_check);
				$email_fetch=$uname_fetch_query['email'];
				$sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
				$sql_email_check = mysqli_query($conn,$sql);
				$email_check = mysqli_num_rows($sql_email_check);
				$email_fetch_query = mysqli_fetch_assoc($sql_email_check);
				$uname_fetch = $email_fetch_query['username'];
				if($uname_check>=1 && $email!=$email_fetch)
				{
					$not_matched = "Email id is incorrect!!";
				}
				
				else if($uname!=$uname_fetch && $email_check>=1)
				{
					$not_matched = "User name is incorrect!!";
				}
				else 
				{
					$not_matched = "User name And Email id are incorrect!!";
				}
			}
			else
			{
				//confirmation code generate and save to database
				
				$uname_email_fetch_query = mysqli_fetch_assoc($sql_uname_email_check);
				$get_frst_name_fetch_query = $uname_email_fetch_query['firstname'];
				$confirmCode= substr(rand() *90000 +10000, 0, 6);
				$sql="Update users SET confirmation_code='$confirmCode' WHERE (username='$uname' AND email='$email')";
				if(mysqli_query($conn,$sql))
				{
					$_SESSION['username'] = $uname;
					$_SESSION['useremail'] = $email;
					
					//message construct and save to file 
					
					$msg = "
					Hi,".$get_frst_name_fetch_query.".
					Somebody recently asked you to reset your password.
					Your password reset code: ".$confirmCode."
					N.B. If it's not you, ignore it.";
				//ini_set('mail.log','');
				//ini_set('mail.add_x_header','0');
				//ini_set('sendmail_path','/usr/sbin/sendmail -t -i');
				//ini_set('sendmail_from','');
				//ini_set('smtp_port','25');
				//ini_set('SMTP','localhost');
				//mail($email,"BUDDY Confirmation Code", $msg, "From: BUDDY <no-reply@buddy.com>");
					file_put_contents("user/user_confirmcode/".$uname.".txt",$msg);
					$_SESSION['flag']=2;
					header('Location: dummy/passRecovervia.php');
				}
			}
		}
		else
		{
			$not_matched="Please Fill up all!!";
		}
	}
	
	//email username check ended

	//verification_code new_password check started
	
	if(isset($_POST['changepass']))
	{
		if(!empty($_POST['veri_code']) &&
			!empty($_POST['new_pass']) &&
			!empty($_POST['cnew_pass']) 
		)
		{
			$vericode = $_POST['veri_code'];
			$newpass = $_POST['new_pass'];
			$cnewpass = $_POST['cnew_pass'];
			$uname = $_SESSION['username'];
			$email = $_SESSION['useremail'];
			$sql="SELECT * FROM users WHERE username='$uname' AND email='$email' LIMIT 1";
			$sql_uname_email_check = mysqli_query($conn,$sql);
			$uname_email_fetch_query = mysqli_fetch_assoc($sql_uname_email_check);
			$confirmCode = $uname_email_fetch_query['confirmation_code'];
			
			if($cnewpass != $newpass)
			{
				$not_matched = "Password doesn't match!!";
			}
			if($vericode!=$confirmCode)
			{
				$not_matched="Wrong Verification code!!";
			}
			if($not_matched=="")
			{
				$newpass=md5($newpass);
				$sql="Update users SET pass='$newpass' WHERE (username='$uname' AND email='$email')";
				if(mysqli_query($conn,$sql))
				{
					mysqli_close($conn);
					$_SESSION['flag']=3;
					header('Location: dummy/passRecovervia.php');
				}
				else{
					echo "Error: " . $sql . "<br>" . mysqli_error($conn);
				}
			}
		}
		else
		{
			$not_matched="Please Fill up all!!";
		}
	}
	
	//verification_code new_password check started
	
?>


<!DOCTYPE html>
<html>
	<head>
		<title>Recover Your Password</title>
		<link rel="stylesheet" type="text/css" href="css/passRecover.css" />
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
			<div class='mid_up fix'><?php echo $up; ?></div>
			<div class='notmatched fix'><?php echo $not_matched; ?></div>
			<div class="mid_var fix"><?php echo $mid; ?></div>
		</div>
		<!-- Middle part ended -->
		
		<footer class="footer fix">
			<p><?php include 'includes/footer.php';?></p> 
		</footer>
	</body>
</html>