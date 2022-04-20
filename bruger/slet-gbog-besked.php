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
//Al din kode herunder

$gid = mysql_real_escape_string($_GET['id']);
$vis_fra = mysql_real_escape_string($_GET['visfra']);

if(!empty ($gid))
{
$resultat = mysql_query("SELECT gaestbogid FROM gaestbog WHERE gaestbogid= '$gid' AND brugerbog='$bruger'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater

if($number == 1)
{
mysql_query("DELETE FROM gaestbog WHERE gaestbogid= '$gid' AND brugerbog='$bruger'") or die(mysql_error());
header("Location: profil.php?menu=$menu&visfra=$vis_fra");
}
else
{
header("Location: profil.php?menu=$menu&visfra=$vis_fra");
}
}
else
{
header("Location: profil.php?menu=$menu&visfra=$vis_fra");
}