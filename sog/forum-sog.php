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

$nquery = mysql_query ("SELECT traadID FROM forumtraad WHERE overskrift LIKE '%$sog_ord%' OR tekst LIKE '%$sog_ord%'") or die(mysql_error());
while ( $bb = mysql_fetch_array($nquery))
{
$traadID = $bb['traadID'];

$query = mysql_query ("SELECT DISTINCT traadID FROM forumtraad WHERE traadID ='$traadID' AND parent='$traadID' AND spg = '0' ORDER BY traadID DESC") or die(mysql_error());
while ($b = mysql_fetch_array($query)) 
{
$antal++;
}
}

if($antal != 0)
{        
echo"Der blev fundet $antal resultater<br><br>"; 

$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal) ? $_GET["visfra"] : 0;

echo"<table border='1'>";
echo"<td align='center'>";
echo"<b>Overskrift</b></td><td><b>Svar</b></td>";
echo"<td align='center'><b>Forfatter</b></td>";
echo"<td align='center'><b>Oprettet</b></td><td><b>Lukket</b></td></tr>";


$traad = mysql_query("SELECT DISTINCT parent FROM forumtraad WHERE overskrift LIKE '%$sog_ord%' OR tekst LIKE '%$sog_ord%' ORDER BY traadID DESC limit $vis_fra, $pr_side") or die(mysql_error());
while ( $v = mysql_fetch_array($traad))
{
$parent = $v['parent'];

$query = mysql_query ("SELECT * ,DATE_FORMAT(dato, '%d') AS ndato, DATE_FORMAT(dato, '%m') AS nmaaned, DATE_FORMAT(dato, '%Y') AS naar, DATE_FORMAT(dato, '%H:%i:%s') AS ntid FROM forumtraad WHERE traadID ='$parent' AND spg = '0' ORDER BY traadID DESC") or die(mysql_error());
while ($b = mysql_fetch_array($query)) 
{

$overskrift = $b['overskrift'];
$overskrift = htmlentities($overskrift);
$overskrift = nl2br("$overskrift");
$overskrift =stripslashes($overskrift);

$odato = $b['dato'];
$traadid = $b['traadID'];
$sbruger = $b['bruger'];
$dato = $b['ndato'];
$maaned = $b['nmaaned'];
$aar = $b['naar'];
$tid = $b['ntid'];
$gr = $b['grid'];
$lukket = $b['lukket'];

//tjekker hvilke ord der ikke er tilladte og erstatter med andre samt indsætter smileys
$ban = mysql_query("SELECT p_ord, g_ord FROM ban ORDER BY banid");
while($rs = mysql_fetch_array($ban))
{
  $bad[]= "/" . preg_quote( $rs['g_ord'], "/" ) . "/i";
  $good[] = $rs['p_ord'];
}
$ban_over = preg_replace( $bad, $good, $overskrift);

$smil = mysql_query("SELECT tekst, billede FROM smiley ORDER BY smilid");
while($smi = mysql_fetch_array($smil))
{
  $ban_over = str_replace($smi['tekst'], "<img border='0' src='$side/smiley/".$smi['billede']."'>", $overskrift);

}
$smil_over = $ban_over;

//tjek slut
$tael = mysql_query("SELECT COUNT(*) AS antal FROM forumtraad WHERE grid= $gr AND parent = $traadid AND spg = 1") or die(mysql_error());
$row = mysql_fetch_array($tael);
$antallet = $row['antal'];

echo"<tr><td valign='top'><a href='$side/forum/laesforum.php?menu=$menu&gr=$gr&traad=$traadid&visfra=$vis_fra'>$parent $smil_over</a></td>";
echo"<td valign='top'>$antallet </td>";
echo"<td> $sbruger</td>";
echo"<td>$dato-$maaned-$aar Kl. $tid</td>";
if ($lukket == "ja")
{
echo"<td class='overskrift'>&nbsp;</td>";
}
else
{
echo"<td>&nbsp;</td>";
}
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