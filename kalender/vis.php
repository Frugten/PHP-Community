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

$_SESSION['menu'] = "kalender";
//Al din kode herunder
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>vis nyheder</title>
<meta name="Description" content="">
<meta name="Keywords" content="">
<?
include 'head.php';
?>
</head>
<body>
<?
include 'header.php';
?>
<?
//hvor mange pr. side
if(!empty ($_SESSION['kalender_indlaeg']))
{
$pr_side = $_SESSION['kalender_indlaeg'];
}
else
{
$indlag = mysql_query ("SELECT indlaeg FROM indlaeg_settings WHERE titel ='kalender-indlaeg'") or die(mysql_error());
while ($vis = mysql_fetch_array($indlag)) 
{
$vaerdi = $vis[indlaeg];
$_SESSION['kalender_indlaeg'] = $vaerdi;
$pr_side = $_SESSION['kalender_indlaeg'];
}
}
//hvor mange pr. side slut


$tstamp = $_GET['dag'];
if (!empty ($tstamp))
{
$dag=$tstamp;

			$for_dato = date("d", $tstamp); 
			$for_maaned = date("m", $tstamp); 
			$for_aar = date("Y", $tstamp); 
			
$fdag = strtotime("-1 days", $tstamp); 
$fuge = strtotime("-1 weeks", $tstamp);
$fmaaned = strtotime("-1 month", $tstamp);
$faar = strtotime("-1 year", $tstamp);

$ndag = strtotime("+1 days", $tstamp); 
$nuge = strtotime("+1 weeks", $tstamp);
$nmaaned = strtotime("+1 month", $tstamp);
$naar = strtotime("+1 year", $tstamp);


echo"<table align='center'border='1'><tr><td>";
echo"<p align='center'><b>Forrige</b></td><td>";
echo"<p align='center'><b>Næste</b></td></tr>";
echo"<tr><td><a href='vis.php?dag=$fdag'>Dag</a> - ";
echo"<a href='vis.php?dag=$fuge'>Uge</a> - ";
echo"<a href='vis.php?dag=$fmaaned'>Måned</a> - ";
echo"<a href='vis.php?dag=$faar'>År</a></td><td>";
echo"<a href='vis.php?dag=$ndag'>Dag</a> - ";
echo"<a href='vis.php?dag=$nuge'>Uge</a> - ";
echo"<a href='vis.php?dag=$nmaaned'>Måned</a> - ";
echo"<a href='vis.php?dag=$naar'>År</a></td>";
echo"</table><hr>";
if($_SESSION['slettet'] == 1)
{
echo"<div class='farvet'>Eventen er nu slettet</div>";
$_SESSION['slettet'] = 0;
}
if($_SESSION['rettet'] == 1)
{
echo"<div class='farvet'>Eventen er nu rettet</div>";
$_SESSION['rettet'] = 0;
}

echo"<h1>$for_dato-$for_maaned-$for_aar</h1>";

echo"<table border='1'><tr>";
echo"<td align='center'><b>TITEL</td>";
echo"<td align='center'><b>FRA</b></td>";
echo"<td align='center'><b>TIL</b></td>";
echo"<td align='center'><b>PRIS</b></td>";
echo"<td align='center'>&nbsp;</td></div><div class='lille'>";

$nquery = mysql_query("SELECT eventid FROM kalender WHERE YEAR(fra) = '$for_aar' AND MONTH(fra) = '$for_maaned' AND DAY(fra) = '$for_dato' ORDER BY fra ASC") or die(mysql_error());
 $antal_ideer = mysql_num_rows($nquery);//Tæller antaller af resultater

$vis = mysql_real_escape_string($_GET["visfra"]);
if ($vis >= $antal_ideer)
{
$vis = 0;
}
if(!empty($vis))
{
$vise = $vis+1;
}
else if ($antal_ideer > 0)
{
$vise = $vis+1;
}
else
{
$vise = 0;
}

if ($vis+$pr_side > $antal_ideer)
{
$plus = $antal_ideer;
}
else
{
$plus = $vis+$pr_side;
}
if($antal_ideer != 0)
{
echo"<p align='center'>Der er <b>$antal_ideer</b> events på denne dag. Her vises <b>$vise</b> - <b>$plus</b> </p>";
}
$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal_ideer) ? $_GET["visfra"] : 0;

$nquery = mysql_query ("SELECT *,DATE_FORMAT(fra, '%d-%m-%Y') AS fradato, DATE_FORMAT(fra, '%H:%i:%s') AS fratid ,DATE_FORMAT(til, '%d-%m-%Y') AS tildato, DATE_FORMAT(til, '%H:%i:%s') AS tiltid FROM kalender WHERE YEAR(fra) = '$for_aar' AND MONTH(fra) = '$for_maaned' AND DAY(fra) = '$for_dato' ORDER BY fra ASC limit $vis_fra, $pr_side") or die(mysql_error());
while ( $a = mysql_fetch_array($nquery))
{
$eventid = $a[eventID];
$parent = $a[parent];

if($parent == 0)
{
$eventid = $a[eventID];
$titel = $a["titel"];
$pris = $a["pris"];
$postby = $a["postby"];
$fradato = $a["fradato"];
$fratid = $a["fratid"];
$tildato = $a["tildato"];
$tiltid = $a["tiltid"];
$markeret = $a["markeret"];
if($markeret == "ja")
{
$class ="overskrift";
}
else
{
$class ="";
}

$titel = stripslashes($titel);
$postby = stripslashes($postby);

echo"<tr><td class='$class'>$titel</td>";
echo"<td class='$class'>$fradato</td>";
echo"<td class='$class'>$tildato</td>";
echo"<td class='$class'>$pris</td>";
echo"<td class='$class'><a href='laes-mere.php?menu=$menu&visfra=$vis_fra&id=$eventid&dag=$dag'>Læs mere</a></td></tr>";

}
else
{
$query = mysql_query ("SELECT * ,DATE_FORMAT(fra, '%d-%m-%Y') AS fradato, DATE_FORMAT(fra, '%H:%i:%s') AS fratid ,DATE_FORMAT(til, '%d-%m-%Y') AS tildato, DATE_FORMAT(til, '%H:%i:%s') AS tiltid FROM kalender WHERE eventID ='$parent'") or die(mysql_error());
while ( $b = mysql_fetch_array($query))
{
$titel = $b["titel"];
$pris = $b["pris"];
$postby = $b["postby"];
$fradato = $b["fradato"];
$fratid = $b["fratid"];
$tildato = $b["tildato"];
$tiltid = $b["tiltid"];
$markeret = $a["markeret"];
if($markeret == "ja")
{
$class ="overskrift";
}
else
{
$class ="";
}

echo"<tr><td class='$class'>$titel</td>";
echo"<td class='$class'>$fradato</td>";
echo"<td class='$class'>$tildato</td>";
echo"<td class='$class'>$pris</td>";
echo"<td class='$class'><a href='laes-mere.php?menu=$menu&id=$parent&dag=$dag'>Læs mere</a></td></tr>";
}
}

}
echo"</table>";

echo "<hr />";

if ($vis_fra > 0) {
$back= $vis_fra - $pr_side;
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&dag=$dag&visfra=$back'>Forrige</a> ";
}
$page = 1;

for ($start = 0; $antal_ideer > $start; $start = $start + $pr_side) {
if($vis_fra != $page * $pr_side - $pr_side) {
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&dag=$dag&visfra=$start'>$page</a> ";
} else {
echo "<b>$page</b> ";
}
$page++;
}
if ($vis_fra < $antal_ideer - $pr_side) {
$next = $vis_fra + $pr_side;
echo " <a href='$_SERVER[PHP_SELF]?menu=$menu&dag=$dag&visfra=$next'>Næste</a> ";
}

}
else
{
echo"Du skal vælge en dato i <a href='kalender.php?menu=$menu'>kalenderen</a>";
}
?>
<?
include 'footer.php';
?>
</body>

</html>
