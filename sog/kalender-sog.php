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

echo"<table>";
$nquery = mysql_query ("SELECT eventid FROM kalender WHERE titel LIKE '%$sog_ord%' OR fra LIKE '%$sog_ord%' OR til LIKE '%$sog_ord%' OR pris LIKE '%$sog_ord%' OR postby LIKE '%$sog_ord%' OR oplysninger LIKE '%$sog_ord%'") or die(mysql_error());
        $antal = mysql_num_rows($nquery);//Tæller antaller af resultater
if($antal != 0)
{   

echo"Der blev fundet $antal resultater<br><br>"; 
$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal) ? $_GET["visfra"] : 0;

echo"<table>";
$query = mysql_query ("SELECT *,DATE_FORMAT(fra, '%d-%m-%Y') AS fradato, DATE_FORMAT(fra, '%H:%i:%s') AS fratid ,DATE_FORMAT(til, '%d-%m-%Y') AS tildato, DATE_FORMAT(til, '%H:%i:%s') AS tiltid FROM kalender WHERE titel LIKE '%$sog_ord%' OR fra LIKE '%$sog_ord%' OR til LIKE '%$sog_ord%' OR pris LIKE '%$sog_ord%' OR postby LIKE '%$sog_ord%' OR oplysninger LIKE '%$sog_ord%'limit $vis_fra, $pr_side") or die(mysql_error());
while ($a = mysql_fetch_array($query)) 
{
$classen = ($classen=='overskrift' ? '' : 'overskrift');

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


echo"<tr><td class='$classen'>$titel</td>";
echo"<td class='$classen'>$fradato</td>";
echo"<td class='$classen'>$tildato</td>";
echo"<td class='$classen'>$pris</td>";
echo"<td class='$classen'><a href='$side/kalender/laes-mere.php?menu=$menu&id=$eventid&dag=$dag'>Læs mere</a></td></tr>";

}

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