<?php 
define('NineteenEleven', TRUE);
require_once "includes/class_lib.php"; 
session_start();
if(isset($_SESSION['username']))
	{
		$Suser_name = $_SESSION['username'];
	}
?>
<html>
	<head>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<link rel="stylesheet" type="text/css" href="./bluebans.css"/>
		<title>Blue Team Bans</title>
		<script type="text/javascript"> 
			$(document).ready(function() {
			 
			    $('#btn-submit').click(function() { 
			 
			        $(".error").hide();
			        var hasError = false;
			        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
			 
			        var emailaddressVal = $("#email").val();
			        if(emailaddressVal == '') {
			            $("#email").after('<br /><span class="error">Please enter your email address.</span>');
			            hasError = true;
			        }
			 
			        else if(!emailReg.test(emailaddressVal)) {
			            $("#email").after('<br /><span class="error">Please enter a valid email address.</span>');
			            hasError = true;
			        }
			 
			        if(hasError == true) { return false; }
			 
			    });
			});
		</script>
	</head>
<body>
	<center><img src="<?php echo $logo_url ?>"> </center>

<?php if (isset($Suser_name)) {
	print "	<div id='Welcome'><p>Welcome back {$Suser_name} </p></div>";
} 
print("<div id='menuBar'>
	<a href='index.php'>Home</a>
	<a href='protest.php'> Protest </a>");
	if (isset($Suser_name)) {
		print ("<a href='admin/logout.php' id='logoutBtn'> Log Out</a>");
		print ("<a href='admin/' id='adminBtn' > Admin Page</a>");
		
	}else{
	print ("<a href='#' id='loginBtn'> Login </a>");
}
print("</div>");

//Check if forum was submitted
if (isset($_POST['protestForm'])) {
	$steamiduser=trim($_POST['steamid']);
		

		//Convert the Steam id and get various information about the user
	$Steam = new steamID;
	if ($protest=$Steam->steamIDCheck($steamiduser)) {
		 $protest['email'] = trim(stripslashes($_POST['email']));
		 $protest['reason'] = preg_quote(trim(stripslashes($_POST['reason'])),"'*^$*@");
		if(debug){print_r($protest);}
	}else{
		printf("<div class='error'><h1>Unable to match \"%s\" to any variation of a SteamID, Please re-enter your Steam ID</h1></div>", $steamiduser);
		exit();
	}

	//open mysqli connection
	$mysqli = new mysqli($host, $username, $pass, $db) or die($mysqli->connect_error);
	//Query the database, and find out why the user was banned, and by whome.
	$adminMatchSQL = "SELECT * FROM `tf2jail_blueban_logs` WHERE `offender_steamid`='{$protest['steamid']}';";
	
	$result = $mysqli->query($adminMatchSQL) or die($mysqli->error);
    $count = $result->num_rows;
    if ($count == 0) {
    	printf("<div class='error'><h1>We were unable to find any record of %s as being banned in our system. <br /> Please make sure you have the correct Steam ID and you are actually banned</h1></div>", $_POST['steamid']);
    	exit();
    }
    while($row = $result->fetch_array(MYSQLI_ASSOC)){
		if(debug){print_r($row);}
		$protest['ban_admin'] = $row['admin_name'];
		$protest['ban_reason'] = $row['reason'];
		$protest['name'] = $row['offender_name'];
		$admin_steamid = $row['admin_steamid'];
		$offender_name = $row['offender_name'];
	}
	
	if(debug){print_r($protest);}
	$now = date('U');
	//put the contents of the protest array into the database
	$protestSQL="INSERT INTO `TF2Jail_web_protests` (steamid,name,steam_link,email,reason,ban_admin,ban_reason,date) VALUES ('{$protest['steamid']}',
																													'{$protest['name']}',
																													'{$protest['steam_link']}',
																													'{$protest['email']}',
																													'{$protest['reason']}',
																													'{$protest['ban_admin']}',
																													'{$protest['ban_reason']}',
																													'{$now}');";		

	$mysqli->query($protestSQL) or die ($mysqli->error);

	//this sets up a link for the email. Currently not working correctly
	$linkToPage = $communityUrl."/admin/view_protests.php";
	if(debug){echo "<br />" . $linkToPage;}

	//get admins email address from our database
	$getEmailSQL = "SELECT email FROM `tf2jail_web_admins` WHERE steamid='".$admin_steamid."';";
	$result = $mysqli->query($getEmailSQL) or die($mysqli->error);
	$row = $result->fetch_array(MYSQLI_ASSOC);
	$admin_email = $row['email'];


$mysqli->close();


	//prepare and send the email to our admins.
	if(sysEmail){
		$to = $admin_email . ", " . $emailAll;
		$subject = $protest['name'] . "Has protested a Guard Ban";
		$body = $protest['ban_admin'].", There has been a protest of one of your bans by ". $protest['name']
				. "\r\n You previously banned him/her for " . $protest['ban_reason'] .". \r\n Their protest is as follows: "
				. stripslashes($protest['reason']) ."\r\n Please respond promptly to this protest ";
		$header = "From: ". $Name . " <" . $HostEmail . ">\r\n";
		mail($to, $subject, $body, $header);
	}

$reason = stripslashes($protest['reason']);
$reason = preg_replace("/[\r\n]+/","</p><p>", $reason);
print "

	<div id=protestSubmit>
		<p>".$protest['name'] .", Your protest has been submitted for review by " . $protest['ban_admin'] . ", he/she will look into your request as soon as possible.</p>
		<p>Ban reason: ". $protest['ban_reason'] . "</p>
		<p>Protest Reason: ". $reason . "</p>
	</div>

</body>
</html>";
}else{

print ("
<div class='content'>	
<div class='protestTable'>
	<table width='600' border='0' align='center' cellpadding='0' cellspacing='1'>
		<tr>
			<form method='POST' action='protest.php' id='protestForm' name='protestForm'>
				<tr>
					<td width='100'>
						<p>Steam ID</p>
					</td>
					
					<td width='500'>
						<input type='text' size='30' name='steamid' placeholder='STEAM_0:0:123456' required />
					</td>
				</tr>
				<tr>
					<td>
						<p>Email Address</p>
					</td>
					
					<td>
						<input type='email' size='40' name='email' id='email' placeholder='imBanned@blueteam.com' required />
					</td>
				</tr>
				<tr>
					<td>
						<p>Why you should be unbanned</p>
					</td>
					
					<td>
						<textarea rows='20' cols='40' name='reason' required maxlength='1000' spellcheck='true' placeholder='I should be un-banned because...'></textarea>
					</td>
				</tr>
				<tr>
					<td colspan='3'><input type='submit' id='btn-submit' name='protestForm' form='protestForm' value='Submit'></td>
				</tr>
			</form>
		</tr>
	</table>
</div>
</div>
");
echo $footer;
print("
</body>
</html>
");

}
?>