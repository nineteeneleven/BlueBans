<?php
session_start();
if(!isset($_SESSION['username']) || ($_SESSION['authlevel'] < 10))
{
header("location:./index.php");
// }elseif($_SESSION['authlevel'] < 10){
// header("location:../index.php");
}
else{
	$Suser_name = $_SESSION['username'];
	$Semail = $_SESSION['email'];
	$Sauthlevel = $_SESSION['authlevel']; 
}
define('NineteenEleven', TRUE);
require_once '../includes/class_lib.php';
//http://www.phpeasystep.com/phptu/6.html




if (isset($_POST['CreateUser'])) {

$mysqli = new mysqli($host, $username, $pass, $db) or die($mysqli->connect_error);
	if($mysqli->connect_error)
	{
		print($mysqli->connect_error);
	}else{
		if(debug){echo "Connected ". $mysqli->host_info;}
	}
	// username and password sent from form
	$user_name=trim($_POST['user_name']);
	$password=trim($_POST['password']);
	$password2=trim($_POST['password2']);
	$email = trim($_POST['email']);
	$steamid = trim($_POST['steamid']);
	$authlevel = $_POST['authlevel'];
	if(isset($_POST['emailUser'])){
		$emailUser = $_POST['emailUser'];
	}
	$sql= "SELECT * FROM `tf2jail_web_admins` WHERE username ='".$user_name."';";
	// if ($stmt = $mysqli->prepare($sql)) {
	// 	$stmt->execute() or die($mysqli->error);
	// 	$stmt->store_result();
	// 	$count = $stmt->num_rows;
	// 	$stmt->close();

	//lets see if that username is taken
	if ($result = $mysqli->query($sql)) {
		$count = $result->num_rows;
	}else{
		$count  = 0;
	}


	if($count===1){

		print "<center> <h1 style='color:red;'>User name is already in use, please use a different name</h1></center>";

	}else{
		//if not lets
		//check the password
		if ($password != $password2) {

			print "<center> <h1 style='color:red;'>Passwords do not match</h1></center>";

		}elseif (strlen($password) < 6) {

			print "<center> <h1 style='color:red;'>Password must be at least 6 characters</h1></center>";
		
		}else{
		// To protect MySQL injection lets take away those pesky slashes
			$user_name = stripslashes($user_name);
			$password = md5(stripslashes($password));

			$sql="INSERT INTO `tf2jail_web_admins` (username,password,email,authlevel,steamid) VALUES ('{$user_name}' , '{$password}', '${email}', '{$authlevel}','{$steamid}');";
			$mysqli->query($sql) or die($mysqli->error);
			print "<center> <h1 style='color:green;'>New User Created</h1></center>";
			if(sysEmail){
				if(isset($emailUser)){

					if ($emailUser == 1) {
						$subject = $user_name . " TF2 Blu Bans account has been created.";
						$message = $user_name .", \r\n A new account for TF2 Blue Bans has been created for you. You can log in with the following information. After you log in please change your password. \r\n Username: " . $user_name."\r\n". "Password: " . $password2;
						$header = "From: ". $Name . " <" . $HostEmail . ">\r\n";
						mail($email, $subject, $message, $header);
					}
				}
			}
			$password2 = null;
			$mysqli->close();
				print("<script type='text/javascript'> setTimeout('reload()' , 1000) 
					function reload(){
						window.location='./index.php'
					}

					</script>");
			exit();
		}
	}
}
?>


<html>
	<head>
		<title>Create New User</title>
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
			<form id="CreateUser" method="POST" action="create_user.php">
				<td>
					<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
						<tr>
							<td colspan="3"><strong>Create new user</strong></td>
						</tr>
						<tr>
							<td width="78">Username</td>
							<td width="6">:</td>
							<td width="294"><input name="user_name" type="text" id="user_name" required /></td>
						</tr>
						<tr>
							<td>Email</td>
							<td>:</td>
							<td><input name="email" type="email" id="email" /></td>
						</tr>						
						<tr>
							<td>Password</td>
							<td>:</td>
							<td><input name="password" type="password" id="password" required /></td>
						</tr>
						<tr>
							<td>Re-enter Password</td>
							<td>:</td>
							<td><input name="password2" type="password" id="password2" required /></td>
						</tr>
						<tr>
							<td>SteamID</td>
							<td>:</td>
							<td><input name="steamid" type="text" id="steamid" required/></td>
						</tr>						
						<tr>
							<td>Access Level</td>
							<td>:</td>							
							<td><input type="radio" name="authlevel" value='10'>Root<input type="radio" name="authlevel" value='9' checked/>Admin</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><input type="checkbox" name="emailUser" value="1" /> Email User <br/> With Login Info?</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><input type="submit" name="CreateUser" value="Create New User" form='CreateUser' /></td>
						</tr>
						<center>Password must be greater than 6 characters.</center>
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

