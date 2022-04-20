<?php
session_start();
chdir('../layout/');
include '../settings/connect.php';
include '../settings/settings.php';

if(!$_SESSION['logget_in'] == 1 OR $_SESSION['logget_in'] == "ikke") 
{
$_SESSION['ikke_log'] = 1;
header("Location: $side");//Sender brugeren videre
exit;//Sørger for at resten af koden, ikke bliver udført
}
$_SESSION['menu'] = "profil";
//Al din kode herunder

$traad = mysql_real_escape_string($_GET['traad']);
$gr = mysql_real_escape_string($_GET['gr']);
$vis_fra = mysql_real_escape_string($_GET['visfra']);

$resultat = mysql_query("SELECT bruger, lukket FROM forumtraad WHERE traadID = '$traad' AND grid='$gr'");//Spørger efter ID
$show = mysql_fetch_array($resultat);

$spbruger = $show[bruger];
$lukket = $show[lukket];

if($spbruger == $bruger)
{
if($lukket == "nej")
{
$alukket ="ja";
}
else
{
$alukket ="nej";
}
mysql_query("UPDATE forumtraad SET lukket='$alukket' WHERE traadID = '$traad' AND grid='$gr'") or die(mysql_error());
}
else
{
}

 header("Location: $side/forum/laesforum.php?menu=$menu&gr=$gr&traad=$traad&visfra=$vis_fra");
?>
