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
<h1>Ikke aktive artikler</h1>
<h2>Denne liste viser alle artikler som du har skrevet, men endnu ikke har gjort synlige for de 
andre brugere. Klik på titlen af en artikel og i toppen af den side der nu kommer frem kan du klikke på et link for at gøre
den synlig.</h2>
<p>
<?php
if ($_SESSION['aktiv'] == 1)
{
echo"<div class='farvet'>Artiklen er nu aktiveret</div>";
$_SESSION['aktiv'] = 0;
}

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

$nquery = mysql_query ("SELECT * FROM artikel WHERE brugernavn ='$bruger' AND aktiv = 'nej'") or die(mysql_error());
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
echo"<td>";
echo"</td>";
echo"</tr></table>";

$query = mysql_query ("SELECT * , DATE_FORMAT(dato, '%d-%m-%Y') AS dato FROM artikel WHERE brugernavn ='$bruger' AND aktiv = 'nej' ORDER BY artikelid DESC limit $vis_fra, $pr_side") or die(mysql_error());
while ($d = mysql_fetch_array($query)) {
$id = $d[artikelid];
$titel = $d[titel];
$beskrivelse = $d[beskrivelse];
$dato = $d[dato];
$rateid = $d[rateid];

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


echo "<a href='$side/artikler/laes-ikke-aktiv.php?menu=$menu&id=$id'>$titel</a> tilføjet $dato ";
echo" <br>";
echo "$beskrivelse<hr>";
}

if ($vis_fra > 0) {
$back= $vis_fra - $pr_side;
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&visfra=$back'>Forrige</a> ";
}
$page = 1;

for ($start = 0; $antal > $start; $start = $start + $pr_side) {
if($vis_fra != $page * $pr_side - $pr_side) {
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&visfra=$start'>$page</a> ";
} else {
echo $page." ";
}
$page++;
}

if ($vis_fra < $antal - $pr_side) {
$next = $vis_fra + $pr_side;
echo " <a href='$_SERVER[PHP_SELF]?menu=$menu&visfra=$next'>Næste</a>";
}

?> 

</p>
<?php
include 'footer.php';
?>
</body>

</html>
