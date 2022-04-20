<?php
session_start();
$_SESSION['page'] = $_SERVER['REQUEST_URI'];
chdir('../layout/');
include '../settings/connect.php';
include '../settings/settings.php';

//sætter aktuel side i en session
$_SESSION['page'] = $_SERVER['REQUEST_URI'];
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
<title>læs forum</title>
<meta name="description" content="forum oversigt">
<META name="keywords" content="">
<?php
include 'head.php';
?>
</head>

<body>

<?php
include 'header.php';
?>

<h1>Læs tråd</h1>
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

//Nu skal vi opfange det id som blev sendt fra brugerlisten
$traad = mysql_real_escape_string($_GET['traad']);
$gr = mysql_real_escape_string($_GET['gr']);

if(isset($_GET['visfra'])) 
{
$vise_fra = mysql_real_escape_string($_GET['visfra']);
$vis = $_GET["visfra"];
}
else
{
$vise_fra = 0;
$vis = 0;
}
$nquery = mysql_query("SELECT traadID FROM forumtraad WHERE parent = $traad AND spg = 1") or die(mysql_error());
        $antal_ideer = mysql_num_rows($nquery);//Tæller antaller af resultater
        
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
if($antal_ideer != 0)
{
echo"<p align='center'>Der er <b>$antal_ideer</b> svar i denne tråd. Her vises <b>$vise</b> - <b>$plus</b> </p>";
}
$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal_ideer) ? $_GET["visfra"] : 0;


//funktion der fager links i teksten
function url2link($txt)
    {
        $username = "[a-z0-9_\-]+";
        $password = "[a-z0-9_\-!#]+";
        $host = "[a-z0-9\-]+\.[a-z0-9\-\.]+";
        $port = "\d{1,5}";
        $path = "\/[a-z0-9\/\-_\.\(\)\%#]*";
        $querystr = "\?[a-z0-9&=\-_\.%\(\)#]+";
        $proto = "(https?|ftp):\/\/";
        $url = "$host(:$port)?($path($querystr)?)?";
        $protodomain = "/$proto(($username(:$password)?@)?$url)/i";
        $domain = "/(^| )(www\.$url)/im";
        $protomail = "/mailto:($username@$host)/i";
        $mail = "/(^| )($username@$host)/im";
        $replacements = array(
                $protodomain => "<a href=\"\\0\" target=\"_blank\">\\2</a>"
                ,$domain => "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>"
                ,$protomail => "<a href=\"\\0\">\\1</a>"
                ,$mail => "\\1<a href=\"mailto:\\2\">\\2</a>"
            );
        return preg_replace( array_keys( $replacements ), array_values( $replacements ), $txt );
    }
//funktion slut    
$traaden = mysql_query("SELECT * ,DATE_FORMAT(dato, '%d') AS dato, DATE_FORMAT(dato, '%m') AS maaned, DATE_FORMAT(dato, '%Y') AS aar, DATE_FORMAT(dato, '%H:%i:%s') AS tid FROM forumtraad WHERE traadID = '$traad' AND spg = '0'") or die(mysql_error());
while ( $a = mysql_fetch_array($traaden))
{
$id = $a['traadID'];
$overskrift = $a['overskrift'];
$overskrift = htmlentities($overskrift);
$overskrift = nl2br("$overskrift");
$overskrift =stripslashes($overskrift);

$tekst = $a['tekst'];

$tekst = htmlentities($tekst);

//replace [b] og [/b] til <b> og </b> i $text
$ord_der_skal_erstattes = array("[b]","[/b]","[i]","[/i]","[u]","[/u]","[boks]","[/boks]"); 
$erstat_ord_med = array("<b>","</b>","<i>","</i>","<u>","</u>","<div class='boks_fed'>","</div>"); 
$tekst = str_replace($ord_der_skal_erstattes, $erstat_ord_med, $tekst); 
//erstat slut

$tekst = nl2br("$tekst");
$tekst = url2link($tekst);
$tekst =stripslashes($tekst);

$spbruger = $a['bruger'];
$dato = $a['dato'];
$maaned = $a['maaned'];
$aar = $a['aar'];
$tid = $a['tid'];
$lukket = $a['lukket'];

//tjekker hvilke ord der ikke er tilladte og erstatter med andre
$ban = mysql_query("SELECT p_ord, g_ord FROM ban ORDER BY banid");
while($rs = mysql_fetch_array($ban))
{
  $bad[]= "/" . preg_quote( $rs['g_ord'], "/" ) . "/i";
  $good[] = $rs['p_ord'];
}
$ban_over = preg_replace( $bad, $good, $overskrift);
$ban_tekst = preg_replace( $bad, $good, $tekst );
//tjek slut

//smileys insættes
$smil = mysql_query("SELECT tekst, billede FROM smiley ORDER BY smilid");
while($smi = mysql_fetch_array($smil))
{
  $ban_over = str_replace($smi['tekst'], "<img border='0' src='$side/smiley/".$smi['billede']."'>", $ban_over);
  $ban_tekst = str_replace($smi['tekst'], "<img border='0' src='$side/smiley/".$smi['billede']."'>", $ban_tekst);

}
$smil_over = $ban_over;
$smil_tekst = $ban_tekst;
//smiley slut

$bille = mysql_query("SELECT profil_billede FROM brugere WHERE Brugernavn = '$spbruger'") or die(mysql_error());
while ( $bil = mysql_fetch_array($bille))
{
$spg_billede = $bil['profil_billede'];
}
if($spg_billede == 0)
{
$spg_billedet ="";
}
else
{
$spg_billedet= "<img width='65' height='75' align='left' src='$side/billeder/profil/$spg_billede' alt='Profil Billede' />";
}


$resultat = mysql_query("SELECT titel FROM forumgr WHERE grID = '$gr'");//Spørger efter ID
$show = mysql_fetch_array($resultat);
$titel = $show['titel'];
$titel =stripslashes($titel);

echo "<b>Tilbage til <a href='visforum.php?menu=$menu&gr=$gr&visfra=$vise_fra'>$titel</a></b><br>";

echo"<table width='100%'><tr><td>";

$abb = mysql_query("SELECT COUNT(*) AS antal FROM forum_abb WHERE brugernavn= '$bruger' AND forumgr = '$gr' AND traad ='$traad'") or die(mysql_error());
$abbrow = mysql_fetch_array($abb);
$abbantal = $abbrow['antal'];
if ($abbantal == 1)
{
echo"<a href='stop-abb-traad.php?menu=$menu&gr=$gr&traad=$traad'>Stop med at abonner på denne traad</a>";
}
else
{
echo"<a href='abb-traad.php?menu=$menu&gr=$gr&traad=$traad'>Abonner på denne traad</a>";
}
echo"</td><td>";
echo"</td></tr></table>";

$sresultat = mysql_query("SELECT brugerid FROM brugere WHERE brugernavn = '$spbruger'");//Spørger efter ID
$snumber = mysql_num_rows($sresultat);//Tæller antaller af resultater

if($snumber == 1)
{
$show = mysql_fetch_array($sresultat);
$spbrugerid = $show[brugerid];
$sppbruger ="<a href='$side/bruger/profil.php?menu=$menu&id=$spbrugerid'>$spbruger</a>";
}
else
{
$sppbruger ="$spbruger";
}
echo "<table border='1' width='100%'>";
if($vis_fra == 0)
{

echo "<tr><td width='25%'>";
echo "$spg_billedet";
echo "<b>$sppbruger</b><br>Kl. $tid<br>$dato-$maaned-$aar </td>";
echo "<td valign='top'><u><b>$smil_over</u></b><br>$smil_tekst</td>";
echo"<td width='15%'>";
include "indlaeg-menu.php";
}
echo"</td></tr>";
}

$get = mysql_query("SELECT *  FROM brugere WHERE Brugernavn = '$bruger' LIMIT 1") or die(mysql_error()); // henter Brugernavn som er 1?
$show = mysql_fetch_array($get);
$til = $show['Brugernavn'];

$grupper = mysql_query("SELECT * ,DATE_FORMAT(dato, '%d') AS ndato, DATE_FORMAT(dato, '%m') AS nmaaned, DATE_FORMAT(dato, '%Y') AS naar, DATE_FORMAT(dato, '%H:%i:%s') AS ntid FROM forumtraad WHERE parent = $traad AND spg = 1 ORDER BY dato ASC limit $vis_fra, $pr_side") or die(mysql_error());
while ( $b = mysql_fetch_array($grupper))
{
$id = $b['traadID'];

$ntekst = $b['tekst'];

$ntekst = htmlentities($ntekst);

//replace [b] og [/b] til <b> og </b> i $text
$ord_der_skal_erstattes = array("[b]","[/b]","[i]","[/i]","[u]","[/u]","[boks]","[/boks]"); 
$erstat_ord_med = array("<b>","</b>","<i>","</i>","<u>","</u>","<div class='boks'>","</div>"); 
$ntekst = str_replace($ord_der_skal_erstattes, $erstat_ord_med, $ntekst); 
//erstat slut

$ntekst = nl2br("$ntekst");
$ntekst = url2link($ntekst);
$ntekst =stripslashes($ntekst);

$nbbruger = $b['bruger'];
$ndato = $b['ndato'];
$nmaaned = $b['nmaaned'];
$naar = $b['naar'];
$ntid = $b['ntid'];

$nban_tekst = str_replace($bad, $good, $ntekst);
//smileys insættes
$nsmil = mysql_query("SELECT tekst, billede FROM smiley ORDER BY smilid");
while($nsmi = mysql_fetch_array($nsmil))
{
  $nban_tekst = str_replace($nsmi['tekst'], "<img border='0' src='$side/smiley/".$nsmi['billede']."'>", $nban_tekst);
}
$nsmil_tekst = $nban_tekst;
//smiley slut

$bille = mysql_query("SELECT profil_billede FROM brugere WHERE Brugernavn = '$nbbruger'") or die(mysql_error());
while ( $bil = mysql_fetch_array($bille))
{
$svar_billede = $bil['profil_billede'];
}
if($svar_billede == 0)
{
$svar_billedet ="";
}
else
{
$svar_billedet= "<img width='65' height='75' align='left' src='$side/billeder/profil/$svar_billede' alt='Profil Billede' />";
}

$sresultat = mysql_query("SELECT brugerid FROM brugere WHERE brugernavn = '$nbbruger'");//Spørger efter ID
$snumber = mysql_num_rows($sresultat);//Tæller antaller af resultater

if($snumber == 1 && $nbbruger != $bruger)
{
$show = mysql_fetch_array($sresultat);
$nbbrugerid = $show[brugerid];
$nbruger ="<a href='$side/bruger/profil.php?menu=$menu&id=$nbbrugerid'>$nbbruger</a>";
}
else
{
$nbruger ="$nbbruger";
}


echo"<tr><td width='25%'>";
echo "$svar_billedet";
echo"<b>$nbruger</b><br><a name='$id'>$ndato-$nmaaned-$naar</a> <br>Kl. $ntid</td>";
echo"<td valign='top'>$nsmil_tekst</td>";
echo"<td width='15%'>";

include "indlaeg-menu.php";

}
echo"</td></tr>";
echo"</table>";
echo "<hr />";

if ($vis_fra > 0) {
$back= $vis_fra - $pr_side;
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&gr=$gr&traad=$traad&visfra=$back'>Forrige</a> ";
}
$page = 1;

for ($start = 0; $antal_ideer > $start; $start = $start + $pr_side) {
if($vis_fra != $page * $pr_side - $pr_side) {
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&gr=$gr&traad=$traad&visfra=$start'>$page</a> ";
} else {
echo "<b>$page</b> ";
}
$page++;
}
if ($vis_fra < $antal_ideer - $pr_side) {
$next = $vis_fra + $pr_side;
echo " <a href='$_SERVER[PHP_SELF]?menu=$menu&gr=$gr&traad=$traad&visfra=$next'>Næste</a> ";
}
echo"<br><br>";
if($spbruger == $bruger)
{
if($lukket =="nej")
{
echo"<a href='luk-aaben.php?menu=$menu&gr=$gr&traad=$traad&visfra=$vis_fra'>Luk tråd</a> så der ikke kan skrive i denne tråd længere";
}
else
{
echo"<a href='luk-aaben.php?menu=$menu&gr=$gr&traad=$traad&visfra=$vis_fra'>Åben tråd</a> Så brugere kan skriv i den igen";
}
}
else
{
if($lukket =="ja")
{
echo"$spbruger har lukket denne tråd";
}
}
include "svar-traad.php";

$_SESSION['overskrift'] = "";
$_SESSION['tekst'] = "";
$_SESSION['prove'] = "";
$_SESSION['test_overskrift'] = "";
$_SESSION['test_tekst'] = "";
?>

</div>	
<?php
include 'footer.php';
?>

</body>

</html>