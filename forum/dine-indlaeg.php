<?php
session_start();
$_SESSION['page'] = $_SERVER['REQUEST_URI'];
chdir('../layout/');
include '../settings/connect.php';
include '../settings/settings.php';

//sætter aktuel side i en session
$aktuel_side = $_SERVER["SCRIPT_NAME"];
$sideinfo = $_SERVER["QUERY_STRING"];
$_SESSION['page'] = "$side/$aktuel_side?$sideinfo";
//sætter aktuel side i en session

if(!$_SESSION['logget_in'] == 1 OR $_SESSION['logget_in'] == "ikke") 
{
$_SESSION['ikke_log'] = 1;
header("Location: $side");//Sender brugeren videre
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


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
<title>Brugerliste</title>
<meta name="description" content="Samlet liste over brugere">
<META name="keywords" content="">
<?php
include 'head.php';
?>
</head>

<body>

<?php
include 'header.php';
?>
<?php
//hvor mange pr. side
if(!empty ($_SESSION['forum_indlaeg']))
{
$pr_side = $_SESSION['forum_indlaeg'];
}
else
{
$indlag = mysql_query ("SELECT indlaeg FROM indlaeg_settings WHERE titel ='forum-indlaeg'") or die(mysql_error());
while ($vis = mysql_fetch_array($indlag)) 
{
$vaerdi = $vis[indlaeg];
$_SESSION['forum_indlaeg'] = $vaerdi;
$pr_side = $_SESSION['forum_indlaeg'];
}
}
//hvor mange pr. side slut

$id = mysql_real_escape_string($_GET['bruger']);

if(!empty ($id))
{
echo"<h1>Forumtråde tråde opretttet af<br>";


$get = mysql_query("SELECT brugernavn FROM brugere WHERE brugerid = '$id' LIMIT 1") or die(mysql_error()); // henter Brugernavn som er 1?
$show = mysql_fetch_array($get);
$brugernavn = $show['brugernavn'];

echo"$brugernavn</h1>";
echo"<table><tr><td></td><td></td><td><b>Lukket</b></td>";
$nquery = mysql_query("SELECT traadID FROM forumtraad WHERE bruger = '$brugernavn' AND spg = '0'") or die(mysql_error());
        $antal_ideer = mysql_num_rows($nquery);//Tæller antaller af resultater
        
$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal_ideer) ? $_GET["visfra"] : 0;

$traade = mysql_query("SELECT *, DATE_FORMAT(dato, '%d-%m-%Y') AS dato, DATE_FORMAT(dato, '%H:%i:%s') AS tid FROM forumtraad WHERE bruger = '$brugernavn' AND spg = 0 ORDER BY dato DESC limit $vis_fra, $pr_side") or die(mysql_error());
while($vise = mysql_fetch_array($traade))
{
$traad = $vise['traadID'];
$gr = $vise['grid'];
$overskrift = $vise['overskrift'];
$dato = $vise['dato'];
$tid = $vise['tid'];
$lukket = $vise['lukket'];
echo"<tr><td><a href='$side/forum/laesforum.php?menu=$menu&id=$id&gr=$gr&traad=$traad'>$overskrift</a></td><td>D. $dato Kl. $tid</td>";
if($lukket =="nej")
{
echo"<td>&nbsp;</td>";
}
else
{
echo"<td class='overskrift'>&nbsp;</td>";
}
echo"</tr>";

}
echo"</table>";
echo "<hr />";

if ($vis_fra > 0) {
$back= $vis_fra - $pr_side;
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&id=$id&gr=$gr&visfra=$back'>Forrige</a> ";
}
$page = 1;

for ($start = 0; $antal_ideer > $start; $start = $start + $pr_side) {
if($vis_fra != $page * $pr_side - $pr_side) {
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&id=$id&gr=$gr&visfra=$start'>$page</a> ";
} else {
echo "<b>$page</b> ";
}
$page++;
}
if ($vis_fra < $antal_ideer - $pr_side) {
$next = $vis_fra + $pr_side;
echo " <a href='$_SERVER[PHP_SELF]?menu=$menu&id=$id&gr=$gr&visfra=$next'>Næste</a>";
}

}
else
{
$nquery = mysql_query("SELECT traadID FROM forumtraad WHERE bruger = '$bruger' AND spg = '0'") or die(mysql_error());
        $antal_ideer = mysql_num_rows($nquery);//Tæller antaller af resultater
        
$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal_ideer) ? $_GET["visfra"] : 0;

echo"<h1>Forumtråde oprettet af dig</h1>";
if (isset($_GET["visfra"]))
{
$vis = $_GET["visfra"];
}
else
{
$vis = 0;
}
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
echo"<p align='center'>Du har oprettet <b>$antal_ideer</b> tråde. Her vises <b>$vise</b> - <b>$plus</b> </p>";

echo"<table><tr><td></td><td></td><td><b>Lukket</b></td>";
$traade = mysql_query("SELECT *, DATE_FORMAT(dato, '%d-%m-%Y') AS datoen, DATE_FORMAT(dato, '%H:%i:%s') AS tiden FROM forumtraad WHERE bruger = '$bruger' AND spg = 0 ORDER BY dato DESC limit $vis_fra, $pr_side") or die(mysql_error());
while($vise = mysql_fetch_array($traade))
{
$traad = $vise['traadID'];
$gr = $vise['grid'];
$overskrift = $vise['overskrift'];
$dato = $vise['datoen'];
$tid = $vise['tiden'];
$lukket = $vise['lukket'];
echo"<tr><td><a href='$side/forum/laesforum.php?menu=$menu&gr=$gr&traad=$traad'>$overskrift</a></td><td>D. $dato Kl. $tid</td>";
if($lukket =="nej")
{
echo"<td>&nbsp;</td>";
}
else
{
echo"<td class='overskrift'>&nbsp;</td>";
}
echo"</tr>";
}
echo"</table>";
echo "<hr />";

if ($vis_fra > 0) {
$back= $vis_fra - $pr_side;
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&gr=$gr&visfra=$back'>Forrige</a> ";
}
$page = 1;

for ($start = 0; $antal_ideer > $start; $start = $start + $pr_side) {
if($vis_fra != $page * $pr_side - $pr_side) {
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&gr=$gr&visfra=$start'>$page</a> ";
} else {
echo "<b>$page</b> ";
}
$page++;
}
if ($vis_fra < $antal_ideer - $pr_side) {
$next = $vis_fra + $pr_side;
echo " <a href='$_SERVER[PHP_SELF]?menu=$menu&gr=$gr&visfra=$next'>Næste</a>";
}

}
?>


<?php
include 'footer.php';
?>

</body>

</html>