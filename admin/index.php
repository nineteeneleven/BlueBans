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

print "	<div id='Welcome'><p>Welcome back {$Suser_name} </p></div>";

?>



<html>
	<head>
		<title>Blue Bans Admin Panel</title>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<link rel="stylesheet" type="text/css" href="../bluebans.css">
	</head>
	<body>
<center><img src="<?php echo $logo_url ?>"> </center>
<nav>
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
</nav>
<div id='searchBox'>
	<form action='index.php'  method='POST' id='searchForm'>
		<input type='text' size='30' placeholder='Search for Name or SteamID.' id='searchInput' name='searchInput' /><input type='image' src="../images/search-button.png" id='searchButton' form='searchForm' />
	</form>

<?php

if (isset($_POST['searchInput'])) {
		$search = $_REQUEST['searchInput'];
	$mysqli = new mysqli($host, $username, $pass, $db) or die($mysqli->connect_error);
		if($mysqli->connect_error)
		{
			print($mysqli->connect_error);
		}else{
			if(debug){echo "Connected ". $mysqli->host_info;}
		}
		$i = 0;
	$sql = "SELECT * FROM `tf2jail_blueban_logs` WHERE offender_steamid LIKE '%" . $search . "%' OR offender_name LIKE '%" . $search . "%';";
	$result = $mysqli->query($sql) or die($mysqli->error);
    //print "<div class='content'>";
    print("<div id='adminSearchResult'>");
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $i++;
            $SteamID = new SteamID;
            $admin64 = $SteamID->IDto64($row['admin_steamid']);
            $offender64 = $SteamID->IDto64($row['offender_steamid']);
            $adminLink= $SteamID->getSteamLink($admin64);
            $offenderLink= $SteamID->getSteamLink($offender64);
            $offender_name = preg_replace('/[^[:print:]]/', '', $row['offender_name']);
            $admin_name = preg_replace('/[^[:print:]]/', '', $row['admin_name']);
    
    print("<h3>". $offender_name . ": " .date("m/d/y g:i:s A",$row['timestamp']) . "</h3><br /><br /><br />");
    print("<div>");
    print("<p id='adminSearchContent'>");
    print("Name: <a href='{$offenderLink}' target='_blank'>". $offender_name . "</a><br />");
    print("Reason: " . $row['reason'] . "<br />");
    print("length: " . $row['bantime'] . " Minutes " . timeleft($row['timestamp'],$row['bantime']) ."<br />");
    print("Banned by: <a href='{$adminLink}' target='_blank'>". $admin_name . "</a>");
    Print("<br /><input type='button' id='edit" . $i . "' value='Edit User' />");
    print("</p>");
    print("</div>");
//make edit user forms
    print("<form id='edituser" . $i ."' method='POST' action='index.php'>");
    print("<input type='hidden' name='steamid' value='".$row['offender_steamid']."' />");
    print("<input type='text' name='reason' value='" . $row['reason'] . "' /> <br />");
    print("<input type='radio' name='banstatus' value ='-1' /> Un-ban");
    print("<input type='radio' name='banstatus' value='0' /> Make Perm");
    print("<input type='radio' name='banstatus' value='1' checked /> No Change");
    print("<br /><input type='submit' value='Confirm' name='submitEdit' form='edituser". $i . "' /><input type='button' id='Cancel' value='Cancel' />");
    print("</form>");
    print '<script type="text/javascript"> 
$(document).ready(function() {
	$("#edituser'.$i.'").hide()
	$("#edit'.$i.'").click(function(){
		$("#edituser'.$i.'").show()
		$("#edit'.$i.'").hide()
	});
});
</script>';
    }
//print("</div>");
$mysqli->close();
}else{
	print("<h3 class='animate'> Please search for the user you would like to edit </h3>");
}

if (isset($_POST['submitEdit'])) {
	$Off_steamid = $_REQUEST['steamid'];
	$reason = $_REQUEST['reason'];
	$banstatus= $_REQUEST['banstatus'];

	switch ($banstatus) {
		case '0':
			$sql = "UPDATE `tf2jail_blueban_logs` SET reason = '{$reason}', bantime = '{$banstatus}' WHERE offender_steamid = '{$Off_steamid}';";
			break;
		$unaban = false;
		case '1':
			$sql = "UPDATE `tf2jail_blueban_logs` SET reason = '{$reason}' WHERE offender_steamid = '{$Off_steamid}';";
			$unaban = false;
			break;
		
		default:
			$sql = "UPDATE `tf2jail_blueban_logs` SET reason = '{$reason}', bantime = '{$banstatus}', timeleft = '0' WHERE offender_steamid = '{$Off_steamid}';";
			$unban = true;
			$unbanSQL="DELETE FROM `tf2jail_bluebans` WHERE steamid = '" . $Off_steamid . "';";
			break;
	}

// if ($banstatus == 1) {

// 	$sql = "UPDATE `tf2jail_blueban_logs` SET reason = '{$reason}' WHERE offender_steamid = '{$Off_steamid}';";

// }else{

// 	$sql = "UPDATE `tf2jail_blueban_logs` SET reason = '{$reason}', bantime = '{$banstatus}' WHERE offender_steamid = '{$Off_steamid}';";

// }
$mysqli = new mysqli($host, $username, $pass, $db) or die($mysqli->connect_error);
$mysqli->query($sql);
if ($unban) {
	$mysqli->query($unbanSQL);
}
$mysqli->close();

print("<center><h1> User Edited Successfully </h1></center>");
print("<script type='text/javascript'> setTimeout('reload()' , 1000) 
	function reload(){
		window.location='index.php'
	}

	</script>");
}
echo $footer;
?>



	</div>
	</body>
</html>
