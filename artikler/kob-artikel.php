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

$artikel = mysql_real_escape_string($_GET['artikel']);
$gr = mysql_real_escape_string($_GET['gruppe']);

if(!empty ($artikel))
{
$get = mysql_query("SELECT point FROM brugere WHERE brugernavn = '$bruger'") or die(mysql_error());
$pshow = mysql_fetch_array($get);

$point = $pshow[point];
$point = floor($point);

$gett = mysql_query("SELECT pris FROM artikel WHERE artikelid = '$artikel'") or die(mysql_error());
$pshoww = mysql_fetch_array($gett);

$pris = $pshoww[pris];
$pris = floor($pris);
if($point >= $pris)
{
$ny_point = $point - $pris;

$ge = mysql_query("SELECT brugernavn FROM artikel WHERE artikelid = '$artikel'") or die(mysql_error());
$pbru = mysql_fetch_array($ge);
$bru = $pbru[brugernavn];

$bget = mysql_query("SELECT point FROM brugere WHERE brugernavn = '$bru'") or die(mysql_error());
$bps = mysql_fetch_array($bget);
$bpoint = $bps[point];
$prisen = $pris/10;
$b_ny_point = $bpoint + $prisen;
    mysql_query("UPDATE brugere SET point = '$b_ny_point' WHERE brugernavn = '$bru'") or die(mysql_error());

    mysql_query("UPDATE brugere SET point = '$ny_point' WHERE brugernavn = '$bruger'") or die(mysql_error());

    mysql_query("INSERT INTO artikel_kob (artikel_id, brugernavn)
    values('$artikel', '$bruger')") or die(mysql_error());


header("Location: $side/artikler/vis-artikel.php?menu=$menu&gr=$gr&id=$artikel");
}
else
{
header("Location: $side/artikler/vis-artikel.php?menu=$menu&gr=$gr&id=$artikel");
}
}
else
{
header("Location: $side/artikler/vis-alle.php?menu=$menu&gr=$gr");
}

?>