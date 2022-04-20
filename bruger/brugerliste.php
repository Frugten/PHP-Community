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
$_SESSION['menu'] = "bruger";
//Al din kode herunder


$_SESSION['sogning'] = "brugerliste";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title></title>
<meta name="Description" content="">
<meta name="Keywords" content="">
<?php
include 'head.php';
?>
</head>
<body>
<?php
include 'header.php';

//tjekker om der kan sendes interne mails rundt til brugere
if(empty ($_SESSION['aktiv_post']))
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Post' AND aktiv = 'ja' AND admin ='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_post'] ="ja";
}
else{
$_SESSION['aktiv_post'] ="nej";
}
}
$dagen_idag = date("d-m");

?>
<h1>Brugerliste</h1>
<b>Sorter efter:</b> 
<?php
echo"<a href='brugerliste.php?menu=$menu&sorter=brugernavn'>Brugernavn</a> | ";
echo"<a href='brugerliste.php?menu=$menu&sorter=point'>Point</a> | ";
echo"<a href='brugerliste.php?menu=$menu&sorter=online'>Online</a> | ";
echo"<a href='brugerliste.php?menu=$menu&sorter=mand'>Mænd</a> | ";
echo"<a href='brugerliste.php?menu=$menu&sorter=kvinder'>Kvinder</a> | ";
?>
<hr>
<?php
$sorter = mysql_real_escape_string($_GET['sorter']);

if ($sorter == "online"){
$where_order ="WHERE deaktiver ='0' ORDER BY online ASC";
}
else if ($sorter == "brugernavn"){
$where_order ="WHERE deaktiver ='0' ORDER BY brugernavn ASC";
}
else if($sorter == "mand"){
$where_order ="WHERE kon ='Mand' AND deaktiver ='0' ORDER BY online ASC";
}
else if($sorter == "kvinder"){
$where_order ="WHERE kon ='Kvinde' AND deaktiver ='0' ORDER BY online ASC";
}
else if ($sorter == "point"){
$where_order ="WHERE deaktiver ='0' ORDER BY point DESC";
}
else{
$where_order ="WHERE deaktiver ='0' ORDER BY online ASC";
}
//hvor mange pr. side
if(!empty ($_SESSION['bruger_indlaeg']))
{
$pr_side = $_SESSION['bruger_indlaeg'];
}
else
{
$indlag = mysql_query ("SELECT indlaeg FROM indlaeg_settings WHERE titel ='bruger-indlaeg'") or die(mysql_error());
while ($vis = mysql_fetch_array($indlag)) 
{
$vaerdi = $vis[indlaeg];
$_SESSION['bruger_indlaeg'] = $vaerdi;
$pr_side = $_SESSION['bruger_indlaeg'];
}
}
//hvor mange pr. side slut


$nquery = mysql_query ("SELECT * FROM brugere $where_order") or die(mysql_error());
        $antal = mysql_num_rows($nquery);//Tæller antaller af resultater
$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal) ? $_GET["visfra"] : 0;

echo"<table>";
$query = mysql_query ("SELECT brugerid, brugernavn, DATE_FORMAT(birth, '%d-%m') AS fodsel, online, point FROM brugere $where_order limit $vis_fra, $pr_side") or die(mysql_error());
while ($vis = mysql_fetch_array($query)) 
{
$classen = ($classen=='overskrift' ? '' : 'overskrift');

$brugerid = $vis[brugerid];
$brugernavn = $vis[brugernavn];
$fodsel = $vis[fodsel];
$online = $vis[online];
$point = $vis[point];
$point = floor($point);

if ($online == 'ja')
{
$on ="<font class='online'>online</font>";
}
else
{
$on ="<font class='offline'>offline</font>";
}
if($vis_fra == 0 && $nr == 0)
{
$nr = 1;
}
else if ($nr >= $vis_fra)
{
$nr++;
}
else
{
$nr = $vis_fra;
}
if($vis_fra == $nr)
{
$nr = $nr + 1;
}
if ($brugernavn == $bruger)
{
$nbrugernavn ="<b>$brugernavn</b>";
}
else
{
$nbrugernavn ="<a href='profil.php?menu=$menu&id=$brugerid'>$brugernavn</a>";
}
if ($dagen_idag == $fodsel)
{
$flag="<img border='0' width='50' src='$side/billeder/flag.jpg'>";
}
else
{
$flag="";
}

echo"<tr><td class='$classen'>";

echo"<table>";
echo "<tr><td class='$classen'>$nr</td><td class='$classen'>$nbrugernavn</td><td class='$classen'>$on</td></tr><tr>";
echo"<td class='$classen'></td><td class='$classen'>$point points</td>";
if($_SESSION['aktiv_post'] =="ja" && $brugernavn != $bruger)
{
echo"<td class='$classen'><a href='$side/intern-mail/send-ny.php?menu=$menu&bruger=$brugerid'>Send mail</a></td>";
}
else
{
echo"<td class='$classen'></td>";
}
echo"</tr></table>";
echo"</td><td class='$classen'>$flag</td></tr>";
}

echo"</table>";
echo "<hr />";

if ($vis_fra > 0) {
$back= $vis_fra - $pr_side;
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&sorter=$sorter&visfra=$back'>Forrige</a> ";
}
$page = 1;

for ($start = 0; $antal > $start; $start = $start + $pr_side) {
if($vis_fra != $page * $pr_side - $pr_side) {
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&sorter=$sorter&visfra=$start'>$page</a> ";
} else {
echo $page." ";
}
$page++;
}

if ($vis_fra < $antal - $pr_side) {
$next = $vis_fra + $pr_side;
echo " <a href='$_SERVER[PHP_SELF]?menu=$menu&sorter=$sorter&visfra=$next'>Næste</a>";
}

?>

<?php
include 'footer.php';
?>
</body>

</html>