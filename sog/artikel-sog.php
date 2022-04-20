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


$nquery = mysql_query ("SELECT artikelid FROM artikel WHERE titel LIKE '%$sog_ord%' OR beskrivelse LIKE '%$sog_ord%' OR artikel LIKE '%$sog_ord%' AND aktiv = 'ja'") or die(mysql_error());
        $antal = mysql_num_rows($nquery);//Tæller antaller af resultater
if($antal != 0)
{        
echo"Der blev fundet $antal resultater<br><br>"; 
$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal) ? $_GET["visfra"] : 0;

echo"<table>";
$query = mysql_query ("SELECT *, DATE_FORMAT(dato, '%d-%m-%Y') AS dato FROM artikel WHERE titel LIKE '%$sog_ord%' OR beskrivelse LIKE '%$sog_ord%' OR artikel LIKE '%$sog_ord%' AND aktiv = 'ja' limit $vis_fra, $pr_side") or die(mysql_error());
while ($d = mysql_fetch_array($query)) 
{
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