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

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>vis artikler</title>
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
echo"<div class='farvet'>Artiklen er nu slettet</div>";
$_SESSION['slettet'] = 0;
}
$gr = mysql_real_escape_string($_GET['gr']);

if(!empty ($gr))
{
$tael = mysql_query("SELECT titel FROM artikelgr WHERE grID= '$gr'") or die(mysql_error());
$row = mysql_fetch_array($tael);
$titel = $row['titel'];
$titel =stripslashes($titel);

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
if(!empty ($_SESSION['artikel_indlaeg']))
{
$pr_side = $_SESSION['artikel_indlaeg'];
}
else
{
$indlag = mysql_query ("SELECT indlaeg FROM indlaeg_settings WHERE titel ='artikel-indlaeg'") or die(mysql_error());
while ($vis = mysql_fetch_array($indlag)) 
{
$vaerdi = $vis[indlaeg];
$_SESSION['artikel_indlaeg'] = $vaerdi;
$pr_side = $_SESSION['artikel_indlaeg'];
}
}
//hvor mange pr. side slut

$nquery = mysql_query ("SELECT * FROM artikel WHERE grid='$gr' AND aktiv = 'ja'") or die(mysql_error());
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
echo"<h2>Viser artikler $vise - $plus af $antal</h2>";
echo"</td>";
echo"</tr></table>";
echo"<table width='100%'>";

if (empty($sorter) OR $sorter == "ny")
{
$where_order="WHERE grid ='$gr' AND aktiv = 'ja' ORDER BY artikelid DESC limit $vis_fra, $pr_side";
}
if ($sorter == "gl")
{
$where_order="WHERE grid ='$gr' AND aktiv = 'ja' ORDER BY artikelid ASC limit $vis_fra, $pr_side";
}
if($sorter != "rate")
{
$query = mysql_query ("SELECT * , DATE_FORMAT(dato, '%d-%m-%Y') AS dato FROM artikel $where_order") or die(mysql_error());
while ($d = mysql_fetch_array($query)) {
$classen = ($classen=='overskrift' ? '' : 'overskrift');

$id = $d[artikelid];
$titel = $d[titel];
$beskrivelse = $d[beskrivelse];
$dato = $d[dato];

$titel = strip_tags($titel);
$beskrivelse = strip_tags($beskrivelse);
$pris = $d[pris];

//tjekker hvilke ord der ikke er tilladte og erstatter med andre
$ban = mysql_query("SELECT p_ord, g_ord FROM ban ORDER BY banid");
while($rs = mysql_fetch_array($ban))
{
    $bad[]= "/" . preg_quote( $rs['g_ord'], "/" ) . "/i";
    $good[] = $rs['p_ord'];
}
$titel = preg_replace( $bad, $good, $titel );
$beskrivelse = preg_replace( $bad, $good, $beskrivelse );
//tjek slut

$titel = stripslashes($titel);
$beskrivelse = stripslashes($beskrivelse);

$nuquery = mysql_query ("SELECT * FROM kommentar WHERE gruppe='Artikler' AND id = '$id'") or die(mysql_error());
        $antal_kom = mysql_num_rows($nuquery);//Tæller antaller af resultater

echo "<tr><td class='$classen'><a href='$side/artikler/vis-artikel.php?menu=$menu&gr=$gr&id=$id'>$titel</a> ";
echo"tilføjet $dato ($antal_kom kommentare)<br>";
echo "$beskrivelse<br>";
echo"Pris: $pris point<br>";
include '../rate/vis-stemmer.php';
echo "</td></tr>";
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
$yquery = mysql_query ("SELECT id FROM rate WHERE gruppe ='Artikler' ORDER BY $order limit $vis_fra, $pr_side") or die(mysql_error());
while ($y = mysql_fetch_array($yquery)) 
{
$artid = $y[id];

$query = mysql_query ("SELECT * , DATE_FORMAT(dato, '%d-%m-%Y') AS dato FROM artikel WHERE artikelid='$artid' AND grid ='$gr' AND aktiv = 'ja'") or die(mysql_error());
while ($d = mysql_fetch_array($query)) {
$classen = ($classen=='overskrift' ? '' : 'overskrift');

$id = $d[artikelid];
$titel = $d[titel];
$beskrivelse = $d[beskrivelse];
$dato = $d[dato];
$pris = $d[pris];

$titel = strip_tags($titel);
$beskrivelse = strip_tags($beskrivelse);

//tjekker hvilke ord der ikke er tilladte og erstatter med andre
$ban = mysql_query("SELECT p_ord, g_ord FROM ban ORDER BY banid");
while($rs = mysql_fetch_array($ban))
{
    $bad[]= "/" . preg_quote( $rs['g_ord'], "/" ) . "/i";
    $good[] = $rs['p_ord'];
}
$titel = preg_replace( $bad, $good, $titel );
$beskrivelse = preg_replace( $bad, $good, $beskrivelse );
//tjek slut

$titel = stripslashes($titel);
$beskrivelse = stripslashes($beskrivelse);

$nuquery = mysql_query ("SELECT * FROM kommentar WHERE gruppe='Artikler' AND id = '$id'") or die(mysql_error());
        $antal_kom = mysql_num_rows($nuquery);//Tæller antaller af resultater

echo "<tr><td class='$classen'><a href='$side/artikler/vis-artikel.php?menu=$menu&gr=$gr&id=$id'>$titel</a> ";
echo"tilføjet $dato ($antal_kom kommentare)<br>";
echo "$beskrivelse<br>";
echo"Pris: $pris point<br>";
include '../rate/vis-stemmer.php';
echo "</td></tr>";
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
