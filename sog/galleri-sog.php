<?
if (!empty($sog_ord))
{
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


$nquery = mysql_query ("SELECT billed_id FROM galleri WHERE titel LIKE '%$sog_ord%' OR info LIKE '%$sog_ord%'") or die(mysql_error());
        $antal = mysql_num_rows($nquery);//Tæller antaller af resultater
if($antal != 0)
{        
echo"Der blev fundet $antal resultater<br><br>"; 
$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal) ? $_GET["visfra"] : 0;

echo"<table>";
$query = mysql_query ("SELECT  * , DATE_FORMAT(dato, '%d-%m-%Y') AS dato FROM galleri WHERE titel LIKE '%$sog_ord%' OR info LIKE '%$sog_ord%' limit $vis_fra, $pr_side") or die(mysql_error());
while ($d = mysql_fetch_array($query)) 
{
$classen = ($classen=='overskrift' ? '' : 'overskrift');

$id = $d[billed_id];
$gruppe = $d[gruppe];
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
echo"<a href='$side/galleri/vis-billede.php?menu=$menu&gr=$gruppe&id=$id'>";

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
echo"</table>";
echo "<hr />";

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
}
else
{
echo"Der blev ikke fundet nogen resultater";
}
}
else
{
echo"Der blev ikke fundet nogen resultater";
}

?>