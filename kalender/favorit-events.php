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
exit;//S�rger for at resten af koden, ikke bliver udf�rt
}
//tjek om modul er aktiv
if(empty ($_SESSION['aktiv_Kalender']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Kelnder' AND aktiv = 'nej' AND admin='brugermenu'");//Sp�rger efter ID
$number = mysql_num_rows($resultat);//T�ller antaller af resultater
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

$dag=$tstamp;

			$for_dato = date("d", $tstamp); 
			$for_maaned = date("m", $tstamp); 
			$for_aar = date("Y", $tstamp); 
			
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

echo"<h1>Favorit events</h1>";

echo"<table border='1'><tr>";
echo"<td align='center'><b>TITEL</td>";
echo"<td align='center'><b>FRA</b></td>";
echo"<td align='center'><b>TIL</b></td>";
echo"<td align='center'><b>PRIS</b></td>";
echo"<td align='center'>&nbsp;</td></div><div class='lille'>";

$nquery = mysql_query("SELECT favorit_id FROM favorit WHERE gruppe='Kalender' AND bruger='$bruger' ORDER BY favorit_id ASC") or die(mysql_error());
 $antal_ideer = mysql_num_rows($nquery);//T�ller antaller af resultater

$vis = $_GET["visfra"];
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
echo"<p align='center'>Der er <b>$antal_ideer</b> events p� denne dag. Her vises <b>$vise</b> - <b>$plus</b> </p>";
}
$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal_ideer) ? $_GET["visfra"] : 0;
$nnquery = mysql_query ("SELECT id FROM favorit WHERE gruppe='Kalender' AND bruger='$bruger' ORDER BY favorit_id ASC limit $vis_fra, $pr_side") or die(mysql_error());
while ( $na = mysql_fetch_array($nnquery))
{
$kalenderid = $na[id];

$nquery = mysql_query ("SELECT *,DATE_FORMAT(fra, '%d-%m-%Y') AS fradato, DATE_FORMAT(fra, '%H:%i:%s') AS fratid ,DATE_FORMAT(til, '%d-%m-%Y') AS tildato, DATE_FORMAT(til, '%H:%i:%s') AS tiltid FROM kalender WHERE eventID ='$kalenderid' ORDER BY fra ASC") or die(mysql_error());
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

$titel = stripslashes($titel);
$postby = stripslashes($postby);

$dag = strtotime($fradato); 

echo"<tr><td>$titel</td>";
echo"<td>$fradato</td>";
echo"<td>$tildato</td>";
echo"<td>$pris</td>";
echo"<td><a href='laes-mere.php?menu=$menu&id=$eventid&dag=$dag'>L�s mere</a></td></tr>";

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

echo"<tr><td>$titel</td>";
echo"<td>$fradato</td>";
echo"<td>$tildato</td>";
echo"<td>$pris</td>";
echo"<td><a href='laes-mere.php?menu=$menu&id=$parent&dag=$dag'>L�s mere</a></td></tr>";
}
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
echo " <a href='$_SERVER[PHP_SELF]?menu=$menu&dag=$dag&visfra=$next'>N�ste</a> ";
}
?>
<?
include 'footer.php';
?>
</body>

</html>
