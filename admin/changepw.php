<?php
define('NineteenEleven', TRUE);
require_once '../includes/class_lib.php';

//http://us1.php.net/manual/en/function.session-start.php
session_start();
if(!isset($_SESSION['username']))
{
header("location:../index.php");
}else{
	$Suser_name = $_SESSION['username'];
	$Semail = $_SESSION['email']; 
	$Sauthlevel = $_SESSION['authlevel']; 
}
if (isset($_POST['ChangePwSubmit'])) {

	$mysqli = new mysqli($host, $username, $pass, $db) or die($mysqli->connect_error);
		if($mysqli->connect_error)
		{
			print($mysqli->connect_error);
		}else{
			if(debug){echo "Connected ". $mysqli->host_info;}
		}
		// username and password sent from form
		$user_name=$_SESSION['username'];
		$oldpassword=$_POST['oldpassword'];
		$password=$_POST['password'];
		$password2=$_POST['password2'];
		$email=$_POST['email'];

	
	function leave(){
		print("<script type='text/javascript'> setTimeout('reload()' , 1000) 
		function reload(){
		window.location='logout.php'
		}

		</script>");
		exit();
	}
	//check password
	if ($_POST['password2']){
		if ($password != $password2) {

			print "<center> <h1 class='error'>Passwords do not match.</h1></center>";
			leave();

		}elseif (strlen($password) < 6) {

			print "<center> <h1 class='error'>Password must be at least 6 characters.</h1></center>";
			leave();
		}
	}

			// To protect MySQL injection (more detail about MySQL injection)
			$user_name = stripslashes($user_name);
			$oldpassword = md5(stripslashes($oldpassword));
			$password = md5(stripslashes($password));
			//Check old password
			$sql="SELECT * FROM `tf2jail_web_admins` WHERE username='{$user_name}' and password='{$oldpassword}'";
			//get number of rows retured from sql query, if there is a row, it must be our user
			$count = 0;
			if ($stmt = $mysqli->prepare($sql)) {
				$stmt->execute();
				$stmt->store_result();
				$count = $stmt->num_rows;
				$stmt->close();

			}
			if($count===1){
				if ($_POST['password2']){
					//if all is well, change the password
					$sql= "UPDATE `tf2jail_web_admins` SET password = '{$password}', email ='{$email}' WHERE username = '{$user_name}';";
					$mysqli->query($sql) or die($mysqli->error);
					print "<center> <h1 style='color:green;'>Information Changed Successfully</h1></center>";
					$mysqli->close();
					leave();
				}else{
					$sql= "UPDATE `tf2jail_web_admins` SET email ='{$email}' WHERE username = '{$user_name}';";
					$mysqli->query($sql) or die($mysqli->error);
					print "<center> <h1 style='color:green;'>Email Changed Successfully</h1></center>";
					$mysqli->close();
					leave();
				}
			}else{
				print("<center> <h1 class='error'>Your current password is incorrect.</h1></center>");
				leave();
			}

		}
	

print "	<div id='Welcome'><p>Welcome back {$Suser_name} </p></div>";

?>



<html>
	<head>
		<title>Change Password</title>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<link rel="stylesheet" type="text/css" href="../bluebans.css">
	</head>
	<body>
<center><img src="<?php echo $logo_url ?>"> </center>
<div id='menuBar'>
	<a href='../index.php'>Home</a>
	<a href='../protest.php'> Protest </a>
	<a href='view_protests.php'>View Protests</a>
	<a href='changepw.php' id='pwBtn' > Change Password</a>
	<?php
		if ($_SESSION['authlevel'] == 10){
			print("<a href='./create_user.php' id='createBtn' >Create New User</a>");
			print("<a href='modify_users.php' id='modifyBtn' >Edit Users</a>");
		}
	?>
	<a href='logout.php' id='loginBtn'> Logout </a>
	<a href='index.php' id='adminBtn' > Admin Page</a>
</div>
<div class='content'>
<center>
	<table width="300" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
		<tr>
			<form id="ChangePwSubmit" method="POST" action="changepw.php">
				<td>
					<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
						<tr>
							<td colspan="3"><strong>Change Password</strong></td>
						</tr>
						<tr>
							<td width="300">Current Password</td>
							<td width="6">:</td>
							<td width="294"><input name="oldpassword" type="password" id="oldpassword" required></td>
						</tr>
						<tr>
							<td>New Password</td>
							<td>:</td>
							<td><input name="password" type="password" id="password"></td>
						</tr>						
						<tr>
							<td>New Password</td>
							<td>:</td>
							<td><input name="password2" type="password" id="password2"></td>
						</tr>
						<tr>
							<td>Email</td>
							<td>:</td>
							<td><input name="email" type="email" id="email" value="<?php echo $Semail; ?>" size='30' ></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><input type="submit" name="ChangePwSubmit" value="Login" form='ChangePwSubmit' /><input type='reset' id='hideLogin' value='Clear' /></td>
						</tr>
					</table>
				</td>
			</form>
		</tr>
	</table>
</center>
<?php echo $footer; ?>
</div>
</body>
</html>