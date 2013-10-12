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

if ($_POST) {
	$user = array('id' =>  $_POST['id'],
				'name' =>  trim($_POST['username']),
				'email' =>  trim($_POST['email']),
				'steamid' =>  trim($_POST['steamid']),
				'authlevel' =>  $_POST['authlevel']);

	if(isset($_POST['resetPass'])){
		$resetPass = $_POST['resetPass'];
		$password = randomPassword(8);
		$user['encPass'] = md5($password);
		$sql = "UPDATE `tf2jail_web_admins` SET username = '{$user['name']}',
															 email = '{$user['email']}',
															  password = '{$user['encPass']}',
															   steamid = '{$user['steamid']}',
															    authlevel = '{$user['authlevel']}'
															     WHERE id =".$user['id'].";";
	}else{
		$sql = "UPDATE `tf2jail_web_admins` SET username = '{$user['name']}',
															 email = '{$user['email']}',
															  steamid = '{$user['steamid']}',
															   authlevel = '{$user['authlevel']}' 
															   WHERE id =".$user['id'].";";
	}

	$mysqli = new mysqli($host, $username, $pass, $db) or die($mysqli->connect_error);
	

	$mysqli->query($sql) or die($mysqli->error);

	$mysqli->close();

	if(sysEmail){
		if(isset($resetPass)){

			if ($resetPass == 1) {
				$subject = $user_name . " TF2 Blu Bans account has been created.";
				$message = $user_name .", \r\n A new account for TF2 Blue Bans has been created for you. You can log in with the following information. After you log in please change your password. \r\n Username: " . $user_name."\r\n". "Password: " . $password;
				$header = "From: ". $Name . " <" . $HostEmail . ">\r\n";
				mail($user['email'], $subject, $message, $header);
			}
		}
	}
}
?>

<html>
	<head>
		<title>Edit Users</title>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<link rel="stylesheet" type="text/css" href="../bluebans.css"/>
		<title>Edit Admins</title>
		<script type="text/javascript">

		</script>
	</head>
	<body>
		<center><img src="<?php echo $logo_url ?>"> </center>
		<div id='menuBar'>
			<a href='../index.php'>Home</a>
			<a href='../protest.php'>Protest</a>
			<a href='view_protests.php'>View Protests</a>
			<a href='changepw.php' id='pwBtn' >Change Info</a>
				<?php
				if ($_SESSION['authlevel'] == 10){
					print("<a href='create_user.php' id='createBtn' >Create New User</a>");
					print("<a href='modify_users.php' id='modifyBtn' >Edit Users</a>");
				}
			?>
			<a href='logout.php' id='logoutBtn'> Logout </a>
			<a href='index.php' id='adminBtn' > Admin Page</a>
		</div>
<div class='content'>
<?php
$mysqli = new mysqli($host, $username, $pass, $db) or die($mysqli->connect_error);

$sql = "SELECT id,username,email,authlevel,steamid FROM `tf2jail_web_admins` WHERE id != 1";

$result = $mysqli->query($sql) or die($mysqli->error);
print('<center>');
print('<br />');
print('<table id="modifyUsersTable" border="1">');
$i=0;
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$i++;

	print('<form id="editUsers'.$i.'" method="POST" action="modify_users.php">');
	print('<div class="modifyUsers"');
	print('<tr>');
	
	print('<td><input type="text" name="username" size="20" required value="'.$row['username'].'" /></td>');
	print('<td><input type="text" name="email" size="30" required value="'.$row['email'].'" /></td>');
	print('<td><input type="text" name="steamid" size="30" required value="'.$row['steamid'].'" /></td>');
	//print('</tr><tr>');
	if ($row['authlevel'] == 10) {
		print('<td><input type="radio" name="authlevel" value="10" checked>Root '
		.'<input type="radio" name="authlevel" value="9" />Admin </td>');
	}else{
		print('<td><input type="radio" name="authlevel" value="10" >Root'
		.'<input type="radio" name="authlevel" value="9" checked/>Admin</td>');
	}
	print('<td><input type="checkbox" name="resetPass" value="1" /> Reset + Email New Password  </td>');
	print('<input type="hidden" name="id" size="5" required value="'.$row['id'].'" />');
	
	print('</form>');
	print('<td><input type="submit" name="editUsers'.$i.'" value="Edit '.$row['username'].'" form="editUsers'.$i.'" /></td>');
	print('</tr>');
	print('</div>');

}
print('</center>');
print('</table>');

$mysqli->close();
echo $footer;
?>
</div>
</body>
</html>
