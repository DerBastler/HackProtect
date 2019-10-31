<?php
include "config.php";

$ip = getUserIpAddr();
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$user_os        = getOS();
$user_browser   = getBrowser();
$query=$_SERVER["REQUEST_URI"];


$db_link = mysqli_connect($hostname, $db_login, $db_pass,$database);
if (!$db_link) {echo "Error: " . mysqli_connect_error();exit();}



//Block List
if ($query=="/admin/"){userTryLogin();addBlacklist();exit;}
if ($query=="/index.php/admin/"){userTryLogin();addBlacklist();exit;}
if (substr($query,0,2)=="/?"){addBlacklist();exit;}
if (substr($query,-4,4)==".tgz"){addBlacklist();exit;}
if (substr($query,-4,4)==".zip"){addBlacklist();exit;}
if (substr($query,-4,4)==".sql"){addBlacklist();exit;}
if (substr($query,-3,3)==".gz"){addBlacklist();exit;}
if (substr($query,-4,4)==".rar"){addBlacklist();exit;}
if (substr($query,-3,3)==".7z"){addBlacklist();exit;}


//Block IPs from blacklist DB one Day
$sql = "SELECT * FROM blacklist WHERE IP = '".$ip."' AND Date = '".date("Y-m-d")."'" ;
$r_GroupList = mysqli_query($db_link,$sql) or die("Cannot get adresses .<br>" . mysqli_error());
$num_rows = mysqli_num_rows($r_GroupList);
$t_GroupList = mysqli_fetch_array($r_GroupList) ;
if ($num_rows>0){
	$hit=$t_GroupList["hits"];
	$id=$t_GroupList["ID"];
	$hit1=(int)$hit;
	$hit1=$hit1+1;
	$sql = "UPDATE blacklist SET  Time='".date("H:i:s")."',hits=".$hit1." WHERE ID=".$id;
	echo "update" . $sql;
	$result = mysqli_query($db_link,$sql) or die("<br><b>Error can't save :</b><br>" . mysqli_error());
	exit;
}

//First visit? Then get Geo Data
$sql = "SELECT * FROM Stats WHERE IP = '".$ip."' AND Date = '".date("Y-m-d")."'" ;
$r_GroupList = mysqli_query($db_link,$sql) or die("Cannot get data .<br>" . mysqli_error());
$num_rows = mysqli_num_rows($r_GroupList);
$t_GroupList = mysqli_fetch_array($r_GroupList) ;

if ($num_rows>0){
	$country=$t_GroupList["country"];
	$CountryName=$t_GroupList["countryName"];
	$continent=$t_GroupList["continent"];
	$state=$t_GroupList["state_prov"];
	$district=$t_GroupList["district"];
	$city=$t_GroupList["city"];
	$zip=$t_GroupList["zipcode"];
	$isp=$t_GroupList["isp"];
	$hostname1 = $t_GroupList["host"];
}else
{
	$location = get_geolocation($apiKey, $ip);
	$decodedLocation = json_decode($location, true);

	$country=$decodedLocation['country_code2'];
	$CountryName=$decodedLocation['country_name'];
	$continent=$decodedLocation['continent_name'];
	$state=$decodedLocation['state_prov'];
	$district=$decodedLocation['district'];
	$city=$decodedLocation['city'];
	$zip=$decodedLocation['zipcode'];
	$isp=$decodedLocation['isp'];
	$hostname1 = gethostbyaddr($ip);


}



//Write Visiter Data to DB
$sql="INSERT INTO `Stats` (`Date`, `Time`, `IP`, `host`, `Site`, `country`, `countryName`, `continent`, `state_prov`, `district`, `city`, `zipcode`, `isp`,`browser`,`os`) VALUES ( '" . date("Y-m-d") . "', '".date("H:i:s")."','" . $ip . "', '".$hostname1."', '".$query."', '".$country."', '".$CountryName."', '".$continent."', '".$state."', '".$district."', '".$city."', '".$zip."', '".$isp."','".  $user_browser."','".  $user_os."')"; 

$result = mysqli_query($db_link,$sql) or die("<br><b>Can't Save :</b><br>" . mysqli_error());



function get_geolocation($apiKey, $ip, $lang = "en", $fields = "*", $excludes = "") {
        $url = "https://api.ipgeolocation.io/ipgeo?apiKey=".$apiKey."&ip=".$ip."&lang=".$lang."&fields=".$fields."&excludes=".$excludes;
        $cURL = curl_init();

        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_HTTPGET, true);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));
        return curl_exec($cURL);
}
function userTryLogin(){
	global $db_link;
	global $db_link;
	global $user_agent;
	global $query;
	global $ip;

	$uName= $_POST['login']['username'];
	$uPass= $_POST['login']['password'];
	$uKey= $_POST['form_key'];
	if($uName){
		$sql="INSERT INTO `LoginAttempt` (`Date`, `Time`,`IP`, `form_key`, `username`, `password`) VALUES ( '" . date("Y-m-d") . "', '".date("H:i:s")."','" . $ip . "', '".$uKey."', '".$uName."', '".$uPass."')"; 
		$result = mysqli_query($db_link,$sql) or die("<br><b>Can't Save :</b><br>" . mysqli_error());
	}
}


function addBlacklist(){

	global $db_link;
	global $user_agent;
	global $query;
	global $ip;

	$sql = "SELECT * FROM blacklist WHERE IP = '".$ip."' AND Date = '".date("Y-m-d")."'" ;
	$r_GroupList = mysqli_query($db_link,$sql) or die("Cannot get data .<br>" . mysqli_error());
	$num_rows = mysqli_num_rows($r_GroupList);
	$t_GroupList = mysqli_fetch_array($r_GroupList) ;

	if ($num_rows>0)
	{
		$hit=$t_GroupList["hits"];

		$hit1=(int)$hit;
		$hit1=$hit1+1;

		$id=$t_GroupList["ID"];
	
		//URI nicht updaten
		$sql = "UPDATE blacklist SET Date='" . date("Y-m-d") . "', Time='".date("H:i:s")."',IP='" . $ip . "',useragent='".$user_agent."',hits=".$hit1." WHERE ID=".$id;

		echo "update" . $sql;
		$result = mysqli_query($db_link,$sql) or die("<br><b>Can't Save :</b><br>" . mysqli_error());

		//Enfernen aus guten clients
		$sql="DELETE FROM `Stats` WHERE IP = '" . $ip . "'"; 
		$result = mysqli_query($db_link,$sql) or die("<br><b>Can't Save :</b><br>" . mysqli_error());
		

	}else
	{
		$sql="INSERT INTO `blacklist` (`Date`, `Time`, `IP`, `URI`, `useragent`, `hits`) VALUES ( '" . date("Y-m-d") . "', '".date("H:i:s")."','" . $ip . "', '".$query."', '".$user_agent."', 1)"; 

		echo "insert" . $sql;
		$result = mysqli_query($db_link,$sql) or die("<br><b>Can't Save :</b><br>" . mysqli_error());
		
		//Enfernen aus guten clients
		$sql="DELETE FROM `Stats` WHERE IP = '" . $ip . "'"; 
		$result = mysqli_query($db_link,$sql) or die("<br><b>Can't Save :</b><br>" . mysqli_error());
		
	}

}
function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getOS() { 

    global $user_agent;
    $os_platform  = "Unknown OS Platform";
    $os_array     = array(
                          '/windows nt 10/i'      =>  'Windows 10',
                          '/windows nt 6.3/i'     =>  'Windows 8.1',
                          '/windows nt 6.2/i'     =>  'Windows 8',
                          '/windows nt 6.1/i'     =>  'Windows 7',
                          '/windows nt 6.0/i'     =>  'Windows Vista',
                          '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                          '/windows nt 5.1/i'     =>  'Windows XP',
                          '/windows xp/i'         =>  'Windows XP',
                          '/windows nt 5.0/i'     =>  'Windows 2000',
                          '/windows me/i'         =>  'Windows ME',
                          '/win98/i'              =>  'Windows 98',
                          '/win95/i'              =>  'Windows 95',
                          '/win16/i'              =>  'Windows 3.11',
                          '/macintosh|mac os x/i' =>  'Mac OS X',
                          '/mac_powerpc/i'        =>  'Mac OS 9',
                          '/linux/i'              =>  'Linux',
                          '/ubuntu/i'             =>  'Ubuntu',
                          '/iphone/i'             =>  'iPhone',
                          '/ipod/i'               =>  'iPod',
                          '/ipad/i'               =>  'iPad',
                          '/android/i'            =>  'Android',
                          '/blackberry/i'         =>  'BlackBerry',
                          '/webos/i'              =>  'Mobile'
                    );

    foreach ($os_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $os_platform = $value;

    return $os_platform;
}

function getBrowser() {

    global $user_agent;
    $browser        = "Unknown Browser";
    $browser_array = array(
                            '/msie/i'      => 'Internet Explorer',
                            '/firefox/i'   => 'Firefox',
                            '/safari/i'    => 'Safari',
                            '/chrome/i'    => 'Chrome',
                            '/edge/i'      => 'Edge',
                            '/opera/i'     => 'Opera',
                            '/netscape/i'  => 'Netscape',
                            '/maxthon/i'   => 'Maxthon',
                            '/konqueror/i' => 'Konqueror',
                            '/mobile/i'    => 'Handheld Browser'
                     );

    foreach ($browser_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $browser = $value;

    return $browser;
}



