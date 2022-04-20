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
//tjek om modul er aktiv
if(empty ($_SESSION['aktiv_Artikler']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Artikler' AND aktiv = 'nej' AND admin='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Artikler'] ="nej";
}
else{
$_SESSION['aktiv_Artikler'] ="ja";
}
}

if($_SESSION['aktiv_Artikler'] == "nej")
{
$_SESSION['ikke_aktiv_modul'] = "nej";
header("Location: $side");
}
//tjek slut

$id = mysql_real_escape_string($_GET['id']);

$rat = mysql_query("SELECT artikelid FROM artikel WHERE artikelid = '$id' AND brugernavn='$bruger'");
        $antal = mysql_num_rows($rat);//Tæller antaller af resultater
if($antal == 1)
{

if(!empty($id))
{
mysql_query("UPDATE artikel SET aktiv='ja' WHERE artikelid='$id'") or die(mysql_error());

    mysql_query("INSERT INTO rate (id, gruppe, samlet, rate, antal)
    values('$id', '$menu', '0', '0', '0')") or die(mysql_error());

$_SESSION['aktiv'] = 1;
header("Location: $side/artikler/ikke-aktive.php?menu=$menu");
}
else
{
header("Location: $side/artikler/ikke-aktive.php?menu=$menu");
}
}
else
{
echo"Den artikel du forsøger at aktivere er ikke skrevet af dig. Vælg en af dine egner <a href='$side/artikler/ikke-aktive.php?menu=$menu'>her</a>";
}

?>
