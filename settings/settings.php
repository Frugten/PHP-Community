<?
if($_SESSION['logget_in'] == 1) 
{
$bruger = $_SESSION['brugernavn'];
$gruppe = $_SESSION['gruppe'];

$menu = mysql_real_escape_string($_GET['menu']);

$denne_side = $_SERVER['PHP_SELF'];

mysql_query("UPDATE brugere SET online = 'ja', ontid = NOW() WHERE brugernavn='$bruger'") or die(mysql_error());

$geth = mysql_query("SELECT ontid FROM brugere WHERE DATE_ADD(ontid, INTERVAL 5 MINUTE) < NOW()") or die(mysql_error()); // henter Brugernavn som er 1?
while ( $h = mysql_fetch_array($geth))
{
$ontid = $h[ontid];
mysql_query("UPDATE brugere SET online = 'nej', laston = '$ontid' WHERE ontid='$ontid'") or die(mysql_error());
}
}

?>