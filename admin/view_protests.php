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

?>
<html>
	<head>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<link rel="stylesheet" type="text/css" href="../bluebans.css"/>
		<title>View Protests</title>
		<script type="text/javascript"> $(function(){
			$( "#protestAcc" ).accordion();


			});
		</script>
	</head>
	<body>
		<center><img src="<?php echo $logo_url ?>"> </center>
		<div id='menuBar'>
			<a href='/bluebans/index.php'>Home</a>
			<a href='/bluebans/protest.php'>Protest</a>
			<a href='/bluebans/admin/view_protests.php'>View Protests</a>
			<a href='/bluebans/admin/changepw.php' id='pwBtn' >Change Info</a>
				<?php
				if ($Sauthlevel == 10){
					print("<a href='/bluebans/admin/create_user.php' id='createBtn' >Create User</a>");
					print("<a href='/bluebans/admin/modify_users.php' id='modifyBtn' >Edit Users</a>");
				}
			?>
			<a href='/bluebans/admin/logout.php' id='logoutBtn'> Logout </a>
			<a href='/bluebans/admin/index.php' id='adminBtn' > Admin Page</a>
		</div>
<div class='content'>
<?php

$mysqli = new mysqli($host, $username, $pass, $db) or die($mysqli->connect_error);


if (isset($_POST['archive'])) {
	$idToArchive = $_POST['idToArchive'];
	foreach ($idToArchive as $id) {
		$archiveSQL = "UPDATE `TF2Jail_web_protests` SET `archived` = '1' WHERE `id`='" .$id."';";
		$mysqli->query($archiveSQL) or die();
	}
	
}

    if($Sauthlevel == 10){

    	print('<form id="archive" method="POST" action="view_protests.php">');
    }

	$sql = "SELECT * FROM `TF2Jail_web_protests` WHERE 1";
    $result = $mysqli->query($sql) or die($mysqli->error);
    print("<div id='protestAcc'>");
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
        	$isArchived = $row['archived'];
        	if(!$isArchived){
			    $name = preg_replace('/[^[:print:]]/', '', $row['name']);
			    $ban_admin = preg_replace('/[^[:print:]]/', '', $row['ban_admin']);
			    $steam_link = $row['steam_link'];
			    $reason = preg_replace("/[\r\n]+/","</p><p>", $row['reason']);
			    print("<h3>". $name . ": " .date("m/d/y g:i:s A",$row['date']) . "</h3>");
			    print("<div>");
			    print("<p>");
			    print("<div class='accInfo'>Name: </div><a href='{$steam_link}' target='_blank'>". $name . "</a><br />");
			    print("<hr />");
			    print("<div class='accInfo'>Ban Reason: </div>" . $row['ban_reason'] . "<br />");
				print("<hr />");
			    print("<div class='accInfo'>Banned by: </div>". $ban_admin . "<br />");
				print("<hr />");
			    print("<div class='accInfo'>Protest Reason: </div>" . $reason);
			    if($Sauthlevel == 10){
			    	print("<hr />");
			    	print("<input type='checkbox' name='idToArchive[]' value=". $row['id'] ." /> Archive");
			    }
			    print("</p>");
			    print("</div>");
			}
    	}
    print("</div>");

    $mysqli->close();

    if($Sauthlevel == 10){
    	print('<br />');
    	print('<input type="submit" name="archive" value="Archive selected" form="archive" />');
    	print('<br />');
    	print('<br />');
    	print('<br />');
    	print('<br />');
    }
    echo $footer;
?>
</div>s
</body>
</html>




	