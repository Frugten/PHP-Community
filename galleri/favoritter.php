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
$gr = mysql_real_escape_string($_GET['gr']);

echo"<h1>Dine favorit artikler</h1>";


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

$nquery = mysql_query ("SELECT id FROM favorit WHERE gruppe='$menu' AND bruger = '$bruger'") or die(mysql_error());
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

$nnquery = mysql_query ("SELECT id FROM favorit WHERE gruppe='$menu' AND bruger='$bruger' limit $vis_fra, $pr_side") or die(mysql_error());
while ($b = mysql_fetch_array($nnquery)) 
{
$fav_id = $b[id];
$query = mysql_query ("SELECT * , DATE_FORMAT(dato, '%d-%m-%Y') AS dato FROM galleri WHERE billed_id='$fav_id'") or die(mysql_error());
while ($d = mysql_fetch_array($query)) {
$id = $d[billed_id];
$gr = $d[gruppe];
$billede = $d[billede];

$titel = $d[titel];
$titel = htmlentities($titel);
$titel =stripslashes($titel);

$dato = $d[dato];

$nuquery = mysql_query ("SELECT * FROM kommentar WHERE gruppe='Galleri' AND id = '$id'") or die(mysql_error());
        $antal_kom = mysql_num_rows($nuquery);//Tæller antaller af resultater

echo"<table><tr><td>";
echo"<a href='vis-billede.php?menu=$menu&gr=$gr&id=$id'>";
$stor = list($bredde, $hojde) = getimagesize("$side/galleri/billeder/$billede");
if($stor[0] < $stor[1])
{
echo "<img border='0' align='left' src='$side/galleri/billeder/$billede' width='100' height='125'></a> ";
}
if($stor[0] > $stor[1])
{
echo "<img border='0' align='left' src='$side/galleri/billeder/$billede' width='125' height='100'></a> ";
}
echo"<b>$titel</b> tilføjet $dato ($antal_kom kommentare)<br>";
include '../rate/vis-stemmer.php';
echo"</td></tr></table>";
echo "<hr>";
}
}
if ($vis_fra > 0) {
$back= $vis_fra - $pr_side;
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&gr=$gr&visfra=$back'>Forrige</a> ";
}
$page = 1;

for ($start = 0; $antal > $start; $start = $start + $pr_side) {
if($vis_fra != $page * $pr_side - $pr_side) {
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&gr=$gr&visfra=$start'>$page</a> ";
} else {
echo $page." ";
}
$page++;
}

if ($vis_fra < $antal - $pr_side) {
$next = $vis_fra + $pr_side;
echo " <a href='$_SERVER[PHP_SELF]?menu=$menu&gr=$gr&visfra=$next'>Næste</a>";
}
?> 

</p>
<?php
include 'footer.php';
?>
</body>

</html>
