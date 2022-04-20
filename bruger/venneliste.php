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
?>

<?php
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

$id = mysql_real_escape_string($_GET["id"]);

if(!empty($id))
{
$hent = mysql_query("SELECT brugernavn FROM brugere WHERE brugerid = '$id'") or die(mysql_error());
$show = mysql_fetch_array($hent);
$brugernavn = $show[brugernavn];

echo"<h1>$brugernavn venneliste</h1>";

$nquery = mysql_query ("SELECT ven, ven_id FROM vennelister WHERE bruger = '$brugernavn'") or die(mysql_error());
        $antal = mysql_num_rows($nquery);//Tæller antaller af resultater

$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal) ? $_GET["visfra"] : 0;

$ven = mysql_query("SELECT ven, ven_id FROM vennelister WHERE bruger = '$brugernavn'") or die(mysql_error());
while ( $va = mysql_fetch_array($ven))
{
$vennen = $va['ven'];
$vennens_id = $va['ven_id'];

echo"<a href='$side/bruger/profil.php?menu=$menu&id=$vennens_id'>$vennen</a><br>";
}

}
else
{
echo"<a href='ansogninger.php?menu=$menu'>Se ansøgninger</a><hr>";
echo"<h1>Din venneliste</h1>";
$nquery = mysql_query ("SELECT ven, ven_id FROM vennelister WHERE bruger = '$bruger'") or die(mysql_error());
        $antal = mysql_num_rows($nquery);//Tæller antaller af resultater

$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal) ? $_GET["visfra"] : 0;

$ven = mysql_query("SELECT ven, ven_id FROM vennelister WHERE bruger = '$bruger'") or die(mysql_error());
while ( $va = mysql_fetch_array($ven))
{
$vennen = $va['ven'];
$vennens_id = $va['ven_id'];

echo"<a href='$side/bruger/profil.php?menu=$menu&id=$vennens_id'>$vennen</a><br>";
}
}
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