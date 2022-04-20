<?
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
//Tjekker om modulet er aktivt
if(empty ($_SESSION['aktiv_Post']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Post' AND aktiv = 'nej' AND admin='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Post'] ="nej";
}
else{
$_SESSION['aktiv_Post'] ="ja";
}
}

if($_SESSION['aktiv_Post'] == "nej")
{
$_SESSION['ikke_aktiv_modul'] = "nej";
header("Location: $side");
}
//aktiv modul tjek slut

//Al din kode herunder

$id = mysql_real_escape_string($_GET['id']);

$resultat = mysql_query("SELECT laest FROM mail_ud WHERE udid= '$id' AND fra = '$bruger'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater

if($number == 1)
{
mysql_query("DELETE FROM mail_ud WHERE udid='$id'") or die(mysql_error());

  header("Location: udbakke.php?menu=$menu");
}
else
{
  header("Location: udbakke.php?menu=$menu");
}
?>
