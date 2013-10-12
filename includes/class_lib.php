<?php
if(!defined('NineteenEleven')){die('Direct access not premitted');}

//import the bluebans.sql into your bluebans database. Then fill in the information for your self in the query below and run it against your database.
//INSERT INTO `tf2jail_web_admins` VALUES (1, '<ADMIN_NAME>', '21232f297a57a5a743894a0e4a801fc3', <ADMIN_EMAIL>, 10);

    define('debug', false);
    define('sysEmail', true); //Let panel send emails?
    $host = "localhost"; ///mysql info
    $username = "root";
    $pass = "password123";
    $db = "bluebans";

$communityUrl = "http://YOURCOMMUNITY.com/bluebans";
$emailAll = "admin@YOURCOMMUNITY.com";
$Name = "KI-BlueBans";                 //senders name
$HostEmail = "admin@YOURCOMMUNITY.com";    //senders e-mail adress

$logo_url = "http://YOURCOMMUNITY.com/logo.png";




function randomPassword($length) {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); 
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function make_table($sql,$accordNum){
    global $mysqli;
    $result = $mysqli->query($sql) or die($mysqli->error);
    print("<div id='{$accordNum}'>");
        while($row = $result->fetch_array(MYSQLI_ASSOC)){

            $SteamID = new SteamID;
            $admin64 = $SteamID->IDto64($row['admin_steamid']);
            $offender64 = $SteamID->IDto64($row['offender_steamid']);
            $adminLink= $SteamID->getSteamLink($admin64);
            $offenderLink= $SteamID->getSteamLink($offender64);
            $offender_name = preg_replace('/[^[:print:]]/', '', $row['offender_name']);
            $admin_name = preg_replace('/[^[:print:]]/', '', $row['admin_name']);
    
    print("<h3>". $offender_name . ": " .date("m/d/y g:i:s A",$row['timestamp']) . "</h3>");
    print("<div>");
    print("<p>");
    print("<div class='accInfo'>Name: </div><a href='{$offenderLink}' target='_blank'>". $offender_name . "</a><br />");
    print("<div class='accInfo'>Reason: </div>" . $row['reason'] . "<br />");
    print("<div class='accInfo'>length: </div>" . $row['bantime'] . " Minutes " . timeleft($row['timestamp'],$row['bantime']) ."<br />");
    print("<div class='accInfo'>Banned by: </div><a href='{$adminLink}' target='_blank'>". $admin_name . "</a>");
    print("</p>");
    print("</div>");
    }
    print("</div>");

}

function timeleft($timebaned,$bantime){
    switch ($bantime) {
        case '0':
            $msg = "(Permanent Ban)";
            break;
        case '-1':
            $msg = "(Unbanned by Admin)";
            break;

        default:
            $currentTime = time();
            $unbanTime = $timebaned + ($bantime * 60);
            if ($unbanTime <= $currentTime) {
                //not unbanned yet

                $msg = "(Ban Expired)";
            }else{
                
                $msg = "<br />(Expires on ". date("m/d/y \@ g:i:s A",$unbanTime) . ")";
            }
            break;
    }
    return $msg;
}


class SteamID
{
    function IDto64($steamId) {
        $iServer = "0";
        $iAuthID = "0";
         
        $szTmp = strtok($steamId, ":");
         
        while(($szTmp = strtok(":")) !== false)
        {
            $szTmp2 = strtok(":");
            if($szTmp2 !== false)
            {
                $iServer = $szTmp;
                $iAuthID = $szTmp2;
            }
        }
        if($iAuthID == "0")
            return "0";
     
        $steamId64 = bcmul($iAuthID, "2");
        $steamId64 = bcadd($steamId64, bcadd("76561197960265728", $iServer));
            if (strpos($steamId64, ".")) {
                $steamId64=strstr($steamId64,'.', true);
            }     
        return $steamId64;
    }
    
    //convertSteamId64ToStamId converts 76561197973578969 to STEAM_0:1:6656620.
    function IDfrom64($steamId64) {
        $iServer = "1";
        if(bcmod($steamId64, "2") == "0") {
            $iServer = "0";
        }
        $steamId64 = bcsub($steamId64,$iServer);
        if(bccomp("76561197960265728",$steamId64) == -1) {
            $steamId64 = bcsub($steamId64,"76561197960265728");
        }
        $steamId64 = bcdiv($steamId64, "2");
        if (strpos($steamId64, ".")) {
                $steamId64=strstr($steamId64,'.', true);
            }     
        return ("STEAM_0:" . $iServer . ":" . $steamId64);
    }

    function getSteamLink($steamId64){
        return "http://steamcommunity.com/profiles/".$steamId64;
    }

    function getSteam64Xml($steam_link_xml){
        $xml = @simplexml_load_file($steam_link_xml)
        or dun_fucked_up();;
        if(!empty($xml)) {
            $steamID64 = $xml->steamID64;
        }
        return $steamID64;
    }

    function steamIDCheck($steamiduser){
        $steamcommunity = "http://steamcommunity.com";
        //Look for STEAM_0:0:123456 variation
        if(preg_match("/^STEAM_/i", $steamiduser)){
            $steamId64= $this->IDto64($steamiduser);
            $steam_link = $this->getSteamLink($steamId64);
            $steam_id = strtoupper($steamiduser);
            $steamArray = array('steamid'=>$steam_id, 'steamID64' =>$steamId64, 'steam_link'=>$steam_link);
            return $steamArray;
        }else{
            if (preg_match("/^[a-z]/i", $steamiduser)) {
                if (preg_match("/(steamcommunity.com)+/i",$steamiduser)) {
                    if (preg_match("/(\/profiles\/)+/i", $steamiduser)) {
                        $i = preg_split("/\//i", $steamiduser);
                        $size = count($i) - 1;
                        $steamID64 = $i[$size];
                        $steam_link = $this->getSteamLink($steamID64);
                        $steam_id=$this->IDfrom64($steamID64);
                        $steamArray = array('steamid'=>$steam_id, 'steamID64' =>$steamID64, 'steam_link'=>$steam_link);
                        return $steamArray;

                    } elseif (preg_match("/(\/id\/)+/i",$steamiduser)) {
                        $i = preg_split("/\//i", $steamiduser);
                        $size = count($i) - 1;
                        $steam_link_xml = $steamcommunity . "/id/" . $i[$size] . "/?xml=1";
                        $steamID64 = $this->getSteam64Xml($steam_link_xml);
                        $steam_link = $this->getSteamLink($steamID64);
                        $steam_id=$this->IDfrom64($steamID64);
                        $steamArray = array('steamid'=>$steam_id, 'steamID64' =>$steamID64, 'steam_link'=>$steam_link);
                        return $steamArray;

                    } else {
                        return false;
                    }
                }else{
                    $steam_link_xml = $steamcommunity . "/id/" . $steamiduser . "/?xml=1";
                    $steamID64 = $this->getSteam64Xml($steam_link_xml);
                    $steam_link = $this->getSteamLink($steamID64);
                    $steam_id=$this->IDfrom64($steamID64);
                        if ($steam_id=="STEAM_0:0:0") {
                            return false;
                        }else{
                        $steamArray = array('steamid'=>$steam_id, 'steamID64' =>$steamID64, 'steam_link'=>$steam_link);
                        return $steamArray;
                        }

                }
            }else{
                return false;
            }
        }
    }
}
//please consider donating before removing or changing the footer, http://nineteeneleven.info
$footer = "<div id='footer' style='background-color:black;border-radius:10px;padding:10px;margin:5px;color:white;position:fixed;left:5px;bottom:0px;width:97%;z-index:99;'> <center>Powered by <a href='http://nineteeneleven.info' target='_blank' onmouseover=\"this.style.backgroundColor='red'\" onmouseout=\"this.style.backgroundColor=''\">NineteenEleven</a></center></div>";?>