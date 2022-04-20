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

//sætter aktuel side i en session

if(!$_SESSION['logget_in'] == 1) {//Hvis brugeren ikke er logget in
header("Location: ../medlems-sider/index.php");//Sender brugeren videre
exit;//Sørger for at resten af koden, ikke bliver udført
}
//Al din kode herunder
//tjek om modul er aktiv
if(empty ($_SESSION['aktiv_Forum']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Forum' AND aktiv = 'nej' AND admin='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Forum'] ="nej";
}
else{
$_SESSION['aktiv_Forum'] ="ja";
}
}

if($_SESSION['aktiv_Forum'] == "nej")
{
$_SESSION['ikke_aktiv_modul'] = "nej";
header("Location: $side");
}
//tjek slut

//Nu skal vi opfange det id som blev sendt fra brugerlisten
$id = mysql_real_escape_string($_GET['id']);
$traad = mysql_real_escape_string($_GET['traad']);
$gr = mysql_real_escape_string($_GET['gr']);
$vis_fra = mysql_real_escape_string($_GET['visfra']);

$resultat = mysql_query("SELECT bruger, tekst, DATE_FORMAT(dato, '%d-%m-%Y') AS dato, DATE_FORMAT(dato, '%H:%i:%s') AS tid FROM forumtraad WHERE traadID = '$id'");//Spørger efter ID
$show = mysql_fetch_array($resultat);
$forfatter = $show['bruger'];
$tekst = $show['tekst'];
$dato = $show['dato'];
$tid = $show['tid'];

//fjerner [boks]noget tekst[/boks] fra strengen
$ny_tekst = preg_replace('%\[boks\].*?\[/boks\]%s', '', $tekst);
$ny_tekst =stripslashes($ny_tekst);

$_SESSION['kommentar'] ="[boks]$forfatter skrev d. $dato $tid: $ny_tekst [/boks] ";


     header("Location: $side/forum/laesforum.php?menu=$menu&id=$id&traad=$traad&gr=$gr&visfra=$vis_fra#svar");

?>