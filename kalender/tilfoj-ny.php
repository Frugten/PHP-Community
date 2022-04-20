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
//tjek om modul er aktiv
if(empty ($_SESSION['aktiv_Kalender']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Kelnder' AND aktiv = 'nej' AND admin='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Kalender'] ="nej";
}
else{
$_SESSION['aktiv_Kalender'] ="ja";
}
}

if($_SESSION['aktiv_Kalender'] == "nej")
{
$_SESSION['ikke_aktiv_modul'] = "nej";
header("Location: $side");
}
//tjek slut

//Al din kode herunder

	$fra_dato = $_REQUEST["fra_dato"];
	$fra_kl = $_REQUEST["fra_kl"];
	$til_dato = $_REQUEST["til_dato"];
	$til_kl = $_REQUEST["til_kl"];
	$titel = $_REQUEST["titel"];
	$postby = $_REQUEST["postby"];
	$pris = $_REQUEST["pris"];
	$obs = $_REQUEST["obs"];
	$markeret = $_REQUEST["markeret"];

if ($pris == 0)
{
$pris ="00";
}

$fradatoen = strtotime(implode('-', array_reverse(explode('-', $fra_dato))) . ' ' . $fra_kl);
$tildatoen = strtotime(implode('-', array_reverse(explode('-', $til_dato))) . ' ' . $til_kl);

if (is_numeric ($pris) && $fradatoen < $tildatoen && preg_match("/^[0-9]{2}:[0-9]{2}/", $til_kl) && preg_match("/^[0-9]{2}:[0-9]{2}/", $fra_kl) && preg_match("/^[0-9]{1,2}-[0-9]{2}/", $fra_dato) && preg_match("/^[0-9]{1,2}-[0-9]{2}/", $til_dato) && !empty ($fra_dato) && !empty ($fra_kl) && !empty ($til_dato) && !empty ($til_kl) && !empty ($titel) && !empty ($postby) && !empty ($pris) && !empty ($obs))
{
if ($pris == '00')
{
$pris ="Gratis";
}

$frad = strtotime(implode('-', array_reverse(explode('-', $fra_dato))));
$tild = strtotime(implode('-', array_reverse(explode('-', $til_dato))));

$antal_dage =$tild - $frad;
if ($antal_dage != 0)
{
$antal_dage = $antal_dage / 86400;
}

function CorrectDate($shortDate)
{
  $tmp = explode("-", $shortDate);
  return date("Y")."-".$tmp[1]."-".$tmp[0];
}

$fra = CorrectDate($fra_dato);
$til = CorrectDate($til_dato);

$fra_dato = mysql_real_escape_string($fra_dato);
$fra_kl = mysql_real_escape_string($fra_kl);
$til_dato = mysql_real_escape_string($til_dato);
$til_kl = mysql_real_escape_string($til_kl);
$titel = mysql_real_escape_string($titel);
$postby = mysql_real_escape_string($postby);
$pris = mysql_real_escape_string($pris);
$obs = mysql_real_escape_string($obs);
$markeret = mysql_real_escape_string($markeret);
if($markeret != "ja")
{
$markeret ="nej";
}

if($antal_dage == 0)
{

mysql_query("INSERT INTO kalender (titel, bruger, fra, til, pris, postby, oplysninger, flere_dage, parent, markeret)
values('$titel', '$bruger', '$fra $fra_kl', '$til $til_kl', '$pris', '$postby', '$obs', 'nej', '0', '$markeret')") or die(mysql_error());
}

else
{

mysql_query("INSERT INTO kalender (titel, bruger, fra, til, pris, postby, oplysninger, flere_dage, parent, markeret)
values('$titel', '$bruger', '$fra $fra_kl', '$til $til_kl', '$pris', '$postby', '$obs', 'ja', '0', '$markeret' )") or die(mysql_error());

$parent = mysql_result(mysql_query("SELECT LAST_INSERT_ID()"),0);

$i = 0; 
$start = strtotime("+1 days", $frad); 

while($i < $antal_dage) 
{ 
$start = date("Y/m/d", $start); 

mysql_query("INSERT INTO kalender (titel, bruger, fra, til, pris, postby, oplysninger, flere_dage, parent, markeret)
values('$titel', '$bruger', '$start', '$til $til_kl', '$pris', '$postby', '$obs', 'ja', '$parent', '$markeret')") or die(mysql_error());

    $i++; 
$start = strtotime("$start");
$start = strtotime("+1 days", $start); 
}

}
$_SESSION['tilfojet'] = 1;

$_SESSION['fra_dato'] =	"";
$_SESSION['fra_kl'] = "";
$_SESSION['til_dato'] =	"";
$_SESSION['til_kl'] = "";
$_SESSION['titel'] = "";
$_SESSION['postby'] = "";
$_SESSION['pris'] =	"";
$_SESSION['obs'] =	"";

$ret = $_GET['ret'];
$event = $_GET['event'];

if ($ret =="ja")
{
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
$_SESSION['rettet'] = 1;
}
else
{

}

if($ret == "ja")
{
$fra_dato = strtotime("$fra");
}
header("Location: vis.php?menu=$menu&dag=$fra_dato");

}
else
{
$_SESSION['ikke'] = 1;
$_SESSION['fra_dato'] =	$fra_dato;
$_SESSION['fra_kl'] = $fra_kl;
$_SESSION['til_dato'] =	$til_dato;
$_SESSION['til_kl'] = $til_kl;
$_SESSION['titel'] = $titel;
$_SESSION['postby'] = $postby;
$_SESSION['pris'] =	$pris;
$_SESSION['obs'] =	$obs;


header("Location: tilfoj.php?menu=$menu&dag=$fra_dato");
}
?>