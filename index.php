
<?php
//    $headers = apache_request_headers();
//    foreach ($headers as $header => $value) {
//        echo "$header: $value <br />\n";
//    }
//Host: shop.laser-art.ch
//Te: trailers
//Upgrade-Insecure-Requests: 1
//Cookie: frontend=4iivlbuljarcbjgg8a9fooqu1vllifhg; frontend_cid=9q7ofvs38ibqYpBb; external_no_cache=1
//Dnt: 1
//Referer: https://shop.laser-art.ch/schilder-mit-eigenem-logo-text.html
//Accept-Encoding: gzip, deflate, br
//Accept-Language: de,en-US;q=0.7,en;q=0.3
//Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
//User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0
//Authorization: 
?>



<?php
include "config.php";

$ip = getUserIpAddr();

$query=$_SERVER["QUERY_STRING"];
list($query33,$queryVar)= explode ("=", $query,2);

if ($query){echo $query;echo"&nbsp;&nbsp;";echo"<a href='index.php'>Back</a>";}else{echo "Unique Visits";echo"&nbsp;&nbsp;";echo"<a href='index.php?blacklist'>Blacklist</a>";;echo"&nbsp;&nbsp;";echo"<a href='index.php?LoginAttempt'>Login Attempt</a>";}

$sqlWhere="";

$sql = "SELECT *,COUNT(*) as CO FROM Stats GROUP BY IP,Date Order by Date DESC " ;

if ($query33=="ip"){$sql = "SELECT * FROM Stats WHERE IP = '".$queryVar."' Order by Date DESC" ;}
if ($query33=="Land"){$sql = "SELECT *,COUNT(*) as CO FROM Stats WHERE country = '".$queryVar."' GROUP BY IP Order by ID" ;}
if ($query33=="Datum"){$sql = "SELECT * FROM Stats WHERE Date = '".$queryVar."' Order by ID" ;}
if ($query33=="blacklist"){$sql = "SELECT ID,Date,Time,IP,URI as Site,useragent as country,hits as CO FROM blacklist Order by Date,Time" ;}
if ($query33=="LoginAttempt"){$sql = "SELECT ID,Date,Time,IP,form_key as Site,username as country,password as city FROM LoginAttempt Order by Date,Time" ;}

$db_link = mysqli_connect($hostname, $db_login, $db_pass,$database);
if (!$db_link) {echo "Error: " . mysqli_connect_error();exit();}

$r_GroupList = mysqli_query($db_link,$sql) or die("Cannot get data .<br>" . mysqli_error());


echo "<table style='width:100%'>";
echo "  <tr>";
echo "    <th>Count</th>";
echo "    <th>Time</th>";
echo "    <th>IP</th>";
echo "   <th>URI</th>";
echo "    <th>Country</th>";
echo "    <th>City</th>";
echo "    <th>Host</th>";
echo "   <th>ISP</th>";
echo "    <th>Browser</th>";
echo "   <th>OS</th>";
echo "  </tr>";

while ($t_GroupList = mysqli_fetch_array($r_GroupList)) {

	if ($oldDate != $t_GroupList["Date"]){echo"<tr><td></td><td><a href='index.php?Datum=" . $t_GroupList["Date"]. "'>".$t_GroupList["Date"]."</a></td></tr>";}
	$oldDate=$t_GroupList["Date"];

	echo "<tr>";
	echo "<td>" . $t_GroupList["CO"] . "</td>";
	echo "<td>".$t_GroupList["Time"]. "</td>";
	echo "<td><a href='index.php?ip=" . $t_GroupList["IP"]. "'>".$t_GroupList["IP"]."</a></td>";

//"<p title='".$t_GroupList["Site"]."'>".substr($t_GroupList["Site"],0,30)."</p>"

	echo "<td>" . "<p title='".$t_GroupList["Site"]."'>".substr($t_GroupList["Site"],0,30)."</p>" . "</td>";
	echo "<td><a href='index.php?Land=" . $t_GroupList["country"]. "'>".$t_GroupList["country"]."</a></td>";
	echo "<td>" . "<p title='".$t_GroupList["city"]."'>".substr($t_GroupList["city"],0,15)."</p>" . "</td>";
	echo "<td>" . "<p title='".$t_GroupList["host"]."'>".substr($t_GroupList["host"],0,15)."</p>" . "</td>";
	echo "<td>" . "<p title='".$t_GroupList["isp"]."'>".substr($t_GroupList["isp"],0,20)."</p>" . "</td>";
	echo "<td>" . "<p title='".$t_GroupList["browser"]."'>".substr($t_GroupList["browser"],0,9)."</p>" . "</td>";
	echo "<td>" . "<p title='".$t_GroupList["os"]."'>".substr($t_GroupList["os"],0,9)."</p>" . "</td>";
	echo   "</tr>";
}

echo "</table>";



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

?>