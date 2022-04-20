<?php
session_start();
$_SESSION['page'] = $_SERVER['REQUEST_URI'];
chdir('../layout/');
include '../settings/connect.php';
include '../settings/settings.php';

if(!$_SESSION['logget_in'] == 1 OR $_SESSION['logget_in'] == "ikke") 
{
$_SESSION['ikke_log'] = 1;
header("Location: $side");//Sender brugeren videre
exit;//Sørger for at resten af koden, ikke bliver udført
}

$dag = mysql_real_escape_string($_GET[dag]);
$event = mysql_real_escape_string($_GET[event]);


$get = mysql_query("SELECT flere_dage FROM kalender WHERE eventID = '$event' AND bruger='$bruger'") or die(mysql_error());
$show = mysql_fetch_array($get);

$flere = $show[flere_dage];

if($flere == "ja")
{
 mysql_query("DELETE FROM kalender WHERE parent ='$event' AND bruger='$bruger'") or die(mysql_error());
 mysql_query("DELETE FROM kalender WHERE eventID ='$event' AND bruger='$bruger'") or die(mysql_error());
}
else
{
 mysql_query("DELETE FROM kalender WHERE eventID ='$event' AND bruger='$bruger'") or die(mysql_error());
}
$_SESSION['slettet'] = 1;
header("Location: $side/kalender/vis.php?dag=$dag");
