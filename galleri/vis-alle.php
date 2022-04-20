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
if(empty ($_SESSION['aktiv_Galleri']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Galleri' AND aktiv = 'nej' AND admin='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Galleri'] ="nej";
}
else{
$_SESSION['aktiv_Galleri'] ="ja";
}
}

if($_SESSION['aktiv_Galleri'] == "nej")
{
$_SESSION['ikke_aktiv_modul'] = "nej";
header("Location: $side");
}
//tjek slut

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>vis billeder</title>
<meta name="Description" content="">
<meta name="Keywords" content="">
<?php
include 'head.php';
?>
</head>
<body>
<?php
include 'header.php';
?>
<p>
<?php
if($_SESSION['slettet'] == 1)
{
echo"<div class='farvet'>Billedet er nu slettet</div>";
$_SESSION['slettet'] = 0;
}
$gr = mysql_real_escape_string($_GET['gr']);

if(!empty ($gr))
{
$tael = mysql_query("SELECT vaerdi FROM settings WHERE tekst = 'bruger upload'") or die(mysql_error());
$row = mysql_fetch_array($tael);
$vaerdi = $row['vaerdi'];

if ($vaerdi == 'ja')
{
echo"<a href='upload-billede.php?menu=$menu&gr=$gr'>Upload et billede til denne gruppe</a>";
}
$tael = mysql_query("SELECT titel FROM gallerigr WHERE grID= '$gr'") or die(mysql_error());
$row = mysql_fetch_array($tael);
$titel = $row['titel'];

echo"<h1>Alle artikler i gruppen<br>$titel</h1>";
echo"<b>Sorter efter:</b> ";
echo"<a href='vis-alle.php?menu=$menu&gr=$gr&sorter=ny'>Nyeste</a> | ";
echo"<a href='vis-alle.php?menu=$menu&gr=$gr&sorter=gl'>Ældst</a> | ";
echo"<a href='vis-alle.php?menu=$menu&gr=$gr&sorter=rate&lav=hojst'>Højeste rating</a> | ";
echo"<a href='vis-alle.php?menu=$menu&gr=$gr&sorter=rate&lav=lav'>Laveste rating</a> | ";
echo"<hr>";
$sorter = mysql_real_escape_string($_GET['sorter']);
$lav = mysql_real_escape_string($_GET['lav']);

//hvor mange pr. side
if(!empty ($_SESSION['galleri_indlaeg']))
{
$pr_side = $_SESSION['galleri_indlaeg'];
}
else
{
$indlag = mysql_query ("SELECT indlaeg FROM indlaeg_settings WHERE titel ='galleri-indlaeg'") or die(mysql_error());
while ($vis = mysql_fetch_array($indlag)) 
{
$vaerdi = $vis[indlaeg];
$_SESSION['galleri_indlaeg'] = $vaerdi;
$pr_side = $_SESSION['galleri_indlaeg'];
}
}
//hvor mange pr. side slut

$nquery = mysql_query ("SELECT billed_id FROM galleri WHERE gruppe='$gr'") or die(mysql_error());
        $antal = mysql_num_rows($nquery);//Tæller antaller af resultater

$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal) ? $_GET["visfra"] : 0;

echo"<table width='100%'><tr>";
echo"<td>";
$vis = $_GET["visfra"];
if(!empty($vis))
{
$vise = $vis+1;
}
else if ($antal > 0)
{
$vise = $vis+1;
}
else
{
$vise = 0;
}

if ($vis+$pr_side > $antal)
{
$plus = $antal;
}
else
{
$plus = $vis+$pr_side;
}
echo"<h2>Viser billeder $vise - $plus af $antal</h2>";
echo"</td>";
echo"</tr></table>";

if (empty($sorter) OR $sorter == "ny")
{
$where_order="WHERE gruppe ='$gr' ORDER BY billed_id DESC limit $vis_fra, $pr_side";
}
if ($sorter == "gl")
{
$where_order="WHERE gruppe ='$gr' ORDER BY billed_id ASC limit $vis_fra, $pr_side";
}

echo"<table width='100%'>";
if($sorter != "rate")
{
$query = mysql_query ("SELECT * , DATE_FORMAT(dato, '%d-%m-%Y') AS dato FROM galleri $where_order") or die(mysql_error());
while ($d = mysql_fetch_array($query)) {
$classen = ($classen=='overskrift' ? '' : 'overskrift');

$id = $d[billed_id];
$billede = $d[billede];

$titel = $d[titel];
$titel = htmlentities($titel);
$titel =stripslashes($titel);

$bredde = $d[bredde];
$hojde = $d[hojde];

$dato = $d[dato];

$nuquery = mysql_query ("SELECT * FROM kommentar WHERE gruppe='Galleri' AND id = '$id'") or die(mysql_error());
        $antal_kom = mysql_num_rows($nuquery);//Tæller antaller af resultater


echo"<tr><td class='$classen'>";
echo"<a href='vis-billede.php?menu=$menu&gr=$gr&id=$id'>";

if($bredde < $hojde)
{
echo "<img border='0' align='left' src='$side/galleri/billeder/$billede' width='100' height='125'>";
}
if($bredde > $hojde)
{
echo "<img border='0' align='left' src='$side/galleri/billeder/$billede' width='125' height='100'>";
}
echo"</a>&nbsp;&nbsp;";

echo"<b>$titel</b> tilføjet $dato ($antal_kom kommentare)<br>&nbsp;";
include '../rate/vis-stemmer.php';
echo"</td></tr>";
}
}
else
{
//sortering efter rating
if ($lav == "hojst")
{
$order="rate DESC";
}
if ($lav == "lav")
{
$order="rate ASC";
}

$yquery = mysql_query ("SELECT id FROM rate WHERE gruppe ='Galleri' ORDER BY $order limit $vis_fra, $pr_side") or die(mysql_error());
while ($y = mysql_fetch_array($yquery)) 
{
$galid = $y[id];

$query = mysql_query ("SELECT * , DATE_FORMAT(dato, '%d-%m-%Y') AS dato FROM galleri WHERE billed_id='$galid' AND gruppe ='$gr'") or die(mysql_error());
while ($d = mysql_fetch_array($query)) {
$classen = ($classen=='overskrift' ? '' : 'overskrift');


$id = $d[billed_id];
$billede = $d[billede];
$dato = $d[dato];
$bredde = $d[bredde];
$hojde = $d[hojde];

$nuquery = mysql_query ("SELECT * FROM kommentar WHERE gruppe='Galleri' AND id = '$id'") or die(mysql_error());
        $antal_kom = mysql_num_rows($nuquery);//Tæller antaller af resultater

echo"<tr><td class='$classen'>";
echo"<a href='vis-billede.php?menu=$menu&gr=$gr&id=$id'>";

if($bredde < $hojde)
{
echo "<img border='0' align='left' src='$side/galleri/billeder/$billede' width='100' height='125'>";
}
if($bredde > $hojde)
{
echo "<img border='0' align='left' src='$side/galleri/billeder/$billede' width='125' height='100'>";
}
echo"</a>&nbsp;&nbsp;";

echo"<b>$titel</b> tilføjet $dato ($antal_kom kommentare)<br>&nbsp;";
include '../rate/vis-stemmer.php';
echo"</td></tr>";
}
}
}
echo"</table>";
if ($vis_fra > 0) {
$back= $vis_fra - $pr_side;
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&gr=$gr&sorter=$sorter&lav=$lav&visfra=$back'>Forrige</a> ";
}
$page = 1;

for ($start = 0; $antal > $start; $start = $start + $pr_side) {
if($vis_fra != $page * $pr_side - $pr_side) {
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&gr=$gr&sorter=$sorter&lav=$lav&visfra=$start'>$page</a> ";
} else {
echo $page." ";
}
$page++;
}

if ($vis_fra < $antal - $pr_side) {
$next = $vis_fra + $pr_side;
echo " <a href='$_SERVER[PHP_SELF]?menu=$menu&gr=$gr&sorter=$sorter&lav=$lav&visfra=$next'>Næste</a>";
}
}
else
{
echo"Du skal vælge en gruppe <a href='index.php?menu=$menu'>her</a>";
}
?> 

</p>
<?php
include 'footer.php';
?>
</body>

</html>
