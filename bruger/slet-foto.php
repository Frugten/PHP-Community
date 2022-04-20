<?php
session_start();
$_SESSION['page'] = $_SERVER['REQUEST_URI'];
include '../settings/connect.php';
include '../settings/settings.php';

if(!$_SESSION['logget_in'] == 1) 
{
$_SESSION['ikke_log'] = 1;
header("Location: $side");//Sender brugeren videre
exit;//Sørger for at resten af koden, ikke bliver udført
}
//Al din kode herunder

if(!empty($bruger))
{
$hent = mysql_query("SELECT profil_billede FROM brugere WHERE brugernavn ='$bruger'") or die(mysql_error());
while($a = mysql_fetch_array($hent))
{
$profil_billede = $a[profil_billede];
unlink("profil-billeder/$profil_billede");
}
mysql_query("UPDATE brugere SET profil_billede='0' WHERE brugernavn='$bruger'") or die(mysql_error());

header("Location: profil.php?menu=$menu");
}
else
{
echo"du er ikke logget ind på denne bruger";
}
?>