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
<title>Vis forum</title>
<meta name="description" content="viser det valgte forum">
<META name="keywords" content="">
<?php
include 'head.php';
?>
<script type="text/javascript">
function addSmil(smil)
{
if(smil!="")
document.spg.tekst.value += smil;
}
</script>

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

$gr = mysql_real_escape_string($_GET['gr']);
$nquery = mysql_query("SELECT traadID FROM forumtraad WHERE grid = '$gr' AND spg = '0' ORDER BY traadID DESC") or die(mysql_error());
        $antal_ideer = mysql_num_rows($nquery);//Tæller antaller af resultater
        

$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal_ideer) ? $_GET["visfra"] : 0;
//Nu skal vi opfange det id som blev sendt fra brugerlisten
$get = mysql_query("SELECT * FROM brugere WHERE Brugernavn = '$bruger' LIMIT 1") or die(mysql_error()); // henter Brugernavn som er 1?
$show = mysql_fetch_array($get);
$til = $show['Brugernavn'];
$laston = $show['laston'];
$grupper = mysql_query("SELECT titel FROM forumgr WHERE grID = $gr ") or die(mysql_error());
while ( $a = mysql_fetch_array($grupper))
{
$gruppen = $a['titel'];
$gruppen =stripslashes($gruppen);

echo"<h1>Du er i under gruppen <br>$gruppen </h1>";
}
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
echo"<p align='center'>Der er <b>$antal_ideer</b> tråde i denne gruppe. Her vises <b>$vise</b> - <b>$plus</b> </p>";
echo"<table width='100%'><tr><td>";
$abb = mysql_query("SELECT COUNT(*) AS antal FROM forum_abb WHERE brugernavn= '$bruger' AND forumgr = '$gr' AND traad ='0'") or die(mysql_error());
$abbrow = mysql_fetch_array($abb);
$abbantal = $abbrow['antal'];
if ($abbantal == 1)
{
echo"<a href='stop-abb-gruppe.php?menu=$menu&gr=$gr'>Stop med at abonner på denne gruppe</a>";
}
else
{
echo"<a href='abb-gruppe.php?menu=$menu&gr=$gr'>Abonner på denne gruppe</a>";
}
echo"</td><td>";
echo"</td></tr></table>";
echo"<b><a href='ny-traad.php?menu=$menu&gr=$gr'>Opret ny tråd</a></b>";
echo"<table width='100%' border='1'><tr>";
echo"<td align='center'>";
echo"<b>Overskrift</b></td><td><b>Svar</b></td>";
echo"<td align='center'><b>Forfatter</b></td>";
echo"<td align='center'><b>Oprettet</b></td><td><b>Lukket</b></td><td><b>Ny</b></td></tr>";


$traad = mysql_query("SELECT * ,DATE_FORMAT(dato, '%d') AS ndato, DATE_FORMAT(dato, '%m') AS nmaaned, DATE_FORMAT(dato, '%Y') AS naar, DATE_FORMAT(dato, '%H:%i:%s') AS ntid FROM forumtraad WHERE grid = $gr AND spg = '0' ORDER BY traadID DESC limit $vis_fra, $pr_side") or die(mysql_error());
while ( $b = mysql_fetch_array($traad))
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
$antal = $row['antal'];
echo"<tr><td valign='top'><a href='laesforum.php?menu=$menu&gr=$gr&traad=$traadid&visfra=$vis_fra'>$smil_over</a></td>";
echo"<td valign='top'>$antal</td>";
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
if ($laston < $odato)
{
echo"<td class='overskrift'>&nbsp;</td></tr>";
}
else
{
$ghent = mysql_query("SELECT dato FROM forumtraad WHERE parent = '$traadid' ORDER BY traadID DESC LIMIT 1") or die(mysql_error());
while ($w = mysql_fetch_array($ghent))
{
$tdato = $w['dato'];
if ($laston < $tdato)
{
echo"<td class='overskrift'>&nbsp;</td></tr>";
}
else
{
echo"<td>&nbsp;</td></tr>";
}
}
}
}
echo"</table>";
echo"<b><a href='ny-traad.php?menu=$menu&gr=$gr'>Opret ny tråd</a></b>";
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
echo"<br><br>";

$_SESSION['overskrift'] ="";

?>


<?php
include 'footer.php';
?>

</body>

</html>