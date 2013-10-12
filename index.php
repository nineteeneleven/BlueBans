<?php 
define('NineteenEleven', TRUE);
require_once "includes/class_lib.php"; 
session_start();
if(isset($_SESSION['username']))
	{
		$Suser_name = $_SESSION['username'];
	}

//Check if user tried to log in, if it was successful, log them in, and start a session.
if (isset($_POST['loginSubmit'])) {

	$mysqli = new mysqli($host, $username, $pass, $db) or die($mysqli->connect_error);
	if($mysqli->connect_error)
	{
		print($mysqli->connect_error);
	}else{
		if(debug){echo "Connected ". $mysqli->host_info;}
	}
	// username and password sent from form
	$user_name=$_POST['user_name'];
	$password=$_POST['password'];

	// To protect MySQL injection (more detail about MySQL injection)
	$Suser_name = stripslashes($user_name);
	$password = md5(stripslashes($password));
	$Suser_name = $mysqli->real_escape_string($user_name);
	$password = $mysqli->real_escape_string($password);

	$sql="SELECT * FROM `tf2jail_web_admins` WHERE username='{$user_name}' and password='{$password}'";
	
	//get number of rows retured from sql query, if there is a row, it must be our user
	$count = 0;
	if ($result = $mysqli->query($sql)) {
		$count = $result->num_rows;
		
	}

	if($count===1){
		while($row = $result->fetch_array(MYSQLI_ASSOC)){
			$email = $row['email'];
			$authlevel = $row['authlevel'];
		}

	$_SESSION['username'] = $Suser_name;
	$_SESSION['email'] = $email;
	$_SESSION['authlevel'] = $authlevel;
		print("<center><h1 class='success'> Login Successful </h1></center>");
		print("<script type='text/javascript'> setTimeout('reload()' , 1000) 
		function reload(){
			window.location='index.php'
		}</script>");
	}else{
		print "<center><h1 class='error'>Wrong Username or Password</h1></center>";
	
	}
}


?> 
<html>
	<head>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<link rel="stylesheet" type="text/css" href="bluebans.css"/>
		<title>Blue Team Bans</title>
		<script type="text/javascript"> $(function(){
			$( "#accordion" ).accordion();
			$( "#accordion2" ).accordion();
			$("#login").hide();
			$("#loginBtn").click(function(){
				$("#login").show('explode', 1000);
			});
			$("#hideLogin").click(function(){
				$("#login").hide('explode', 'fast');
			});
			});
		</script>
	</head>
<body>
	<center><img src="<?php echo $logo_url ?>"> </center>
	<div id='login'>
	<table width="300" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
		<tr>
			<form id="loginSubmit" method="POST" action="index.php">
				<td>
					<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
						<tr>
							<td colspan="3"><strong>Admin Login </strong></td>
						</tr>
						<tr>
							<td width="78">Username</td>
							<td width="6">:</td>
							<td width="294"><input name="user_name" type="text" id="user_name"></td>
						</tr>
						<tr>
							<td>Password</td>
							<td>:</td>
							<td><input name="password" type="password" id="password"></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><input type="submit" name="loginSubmit" value="Login" form='loginSubmit' /><input type='button' id='hideLogin' value='Cancel' /></td>
							
						</tr>
					</table>
				</td>
			</form>
		</tr>
	</table>
</div>

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

?>
<div class='content'>
<div id='searchBox'>
	<form action='index.php'  method='POST' id='searchForm'>
		<input type='text' size='30' placeholder='Search for Name or SteamID.' id='searchInput' name='searchInput' /><input type='image' src="images/search-button.png" id='searchButton' form='searchForm' />
	</form>

</div>

<?php
//create new database connection
$mysqli = new mysqli($host, $username, $pass, $db) or die($mysqli->connect_error);
	if($mysqli->connect_error)
	{
		print($mysqli->connect_error);
	}else{
		if(debug){echo "Connected ". $mysqli->host_info;}
	}


//If search query was entered, grab it, and search the database.
//if results are found, print the jquery table with the results.
if (isset($_POST['searchInput'])) {
		$search = $_REQUEST['searchInput'];
		print("<div id='SearchResult'>");
		print("<center><h1> Results for \"{$search}\" </h1></center>");	
		$sql = "SELECT * FROM `tf2jail_blueban_logs` WHERE offender_steamid LIKE '%" . $search . "%' OR offender_name LIKE '%" . $search . "%';";
		make_table($sql,'accordion2');
		print("</div>");
	
	}
print("<div id='lastTen'>");
print("<center><h1>Last 10 bans here</h1></center>");	
$sql = "SELECT * FROM `tf2jail_blueban_logs` ORDER BY timestamp DESC LIMIT 10;";
make_table($sql,'accordion');
print("</div>");	

$mysqli->close();
print("<div style='height:900px'</div>");
print($footer);
print("</div>");
print("</body>");
print("</html>");


?>


