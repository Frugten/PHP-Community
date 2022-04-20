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
?>
<?php
$idet = mysql_real_escape_string($_GET['id']);
$gr = mysql_real_escape_string($_GET['gr']);
if ($gruppe =="admin")
{
echo"| <a href='$side/admin/artikler/slet-artikel.php?menu=$menu&gr=$gr&id=$idet'>Slet artikel</a> | ";
echo" <a href='$side/admin/artikler/flyt-artikel.php?menu=$menu&gr=$gr&id=$idet'>Flyt artikel</a> | ";
echo"<hr>";
}
?>
<?php
$rat = mysql_query("SELECT artikelid FROM artikel WHERE artikelid = '$idet' AND aktiv='ja'");
        $antal = mysql_num_rows($rat);//Tæller antaller af resultater
if($antal == 1)
{

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

$artikler = mysql_query("SELECT * ,DATE_FORMAT(dato, '%d-%m-%Y') AS dato FROM artikel WHERE artikelid = $idet");
while ($d = mysql_fetch_assoc($artikler))
{
$id = $d[artikelid];
$dato = $d[dato];
$brugernavn = $d[brugernavn];
$titel = $d[titel];
$beskrivelse = $d[beskrivelse];
$artikel = $d[artikel];
$rateid = $d[rateid];
$pris = $d[pris];

$beskrivelse = htmlentities($beskrivelse);
$beskrivelse = strip_tags($beskrivelse, "<b>, <i>, <u>, <div>");
$beskrivelse = nl2br("$beskrivelse");
$beskrivelse = stripslashes($beskrivelse);

$titel = htmlentities($titel);
$titel = strip_tags($titel, "<b>, <i>, <u>, <div>");
$titel = nl2br("$titel");
$titel = stripslashes($titel);

$bille = mysql_query("SELECT profil_billede FROM brugere WHERE brugernavn = '$brugernavn'") or die(mysql_error());
while ( $bil = mysql_fetch_array($bille))
{
$for_billede = $bil[profil_billede];
}

echo"<h1>$titel</h1>";
if ($for_billede != "0")
{
echo"<img width='50' height='60' align='left' src='$side/bruger/profil-billeder/$for_billede' alt='Profil Billede'>";
}
else
{
echo"";
}
echo"<b>$beskrivelse</b><br>";
echo"<table width='70%'><tr><td>";
echo"Tilføjet af $brugernavn den. $dato<br>";

//har brugeren købt artiklen
if($bruger != $brugernavn )
{
$gresultat = mysql_query("SELECT kob_id FROM artikel_kob WHERE artikel_id = '$id' AND brugernavn='$bruger'");//Spørger efter ID
$gnumber = mysql_num_rows($gresultat);//Tæller antaller af resultater
}
else
{
$gnumber =1;
}

if ($gnumber == 1 || $pris == 0)
{
$se_artikel = "ja";
}
else
{
$se_artikel= "nej";
}

if($se_artikel == "ja")
{
$artikel = htmlentities($artikel);

//replace [b] og [/b] til <b> og </b> i $text
$ord_der_skal_erstattes = array("[b]","[/b]","[i]","[/i]","[u]","[/u]","[boks]","[/boks]"); 
$erstat_ord_med = array("<b>","</b>","<i>","</i>","<u>","</u>","<div class='boks'>","</div>"); 
$artikel = str_replace($ord_der_skal_erstattes, $erstat_ord_med, $artikel); 
//erstat slut

$artikel = strip_tags($artikel, "<b>, <i>, <u>, <div>");
$artikel = nl2br("$artikel");
$artikel = stripslashes($artikel);
$artikel = url2link($artikel);

//tjekker hvilke ord der ikke er tilladte og erstatter med andre
$ban = mysql_query("SELECT p_ord, g_ord FROM ban ORDER BY banid");
while($rs = mysql_fetch_array($ban))
{
    $bad[]= "/" . preg_quote( $rs['g_ord'], "/" ) . "/i";
    $good[] = $rs['p_ord'];
}
$titel = preg_replace( $bad, $good, $titel );
$beskrivelse = preg_replace( $bad, $good, $beskrivelse );
$ban_tekst = preg_replace( $bad, $good, $artikel );
//tjek slut

//smileys insættes
$smil = mysql_query("SELECT tekst, billede FROM smiley ORDER BY smilid");
while($smi = mysql_fetch_array($smil))
{
  $ban_over = str_replace($smi['tekst'], "<img border='0' src='$side/smiley/".$smi['billede']."'>", $ban_over);
  $ban_tekst = str_replace($smi['tekst'], "<img border='0' src='$side/smiley/".$smi['billede']."'>", $ban_tekst);

}
$artikel = $ban_tekst;
//smiley slut

include '../favorit/tjek-favoritliste.php';

echo"</td><td>";
include '../rate/stem.php';
echo"</td></tr></table>";

echo"<hr>";
echo "$artikel<hr>";
include '../kommentar/vis-kommentare.php';

include '../kommentar/form.php';
}
else
{
$get = mysql_query("SELECT point FROM brugere WHERE brugernavn = '$bruger'") or die(mysql_error());
$pshow = mysql_fetch_array($get);

$point = $pshow[point];
$point = floor($point);
$resul = $point - $pris;
if($point >= $pris)
{
echo"<hr>Denne artikel koster <b>$pris</b> points at se. Du har lige nu <b>$point</b> point<br>";
echo"Ønsker du at se denne artikel vil du have <b>$resul</b> point<br><br>";
echo"<a href='kob-artikel.php?menu=$menu&gruppe=$gr&artikel=$id'>";
echo"Jeg ønsker at købe adgang til denne artikel";
echo"</a><br><br>";
echo"<b>Køb af en artikel kan ikke fortrydes</b>";
}
else
{
echo"<hr>Du har kun <b>$point</b> point så har ikke point nok til at se denne artikel. ";
echo"Du optjener point hele tiden så kom tilbage en anden gang";
}
}
}

}
else
{
echo"Den artikel du forsøger at åbner er endnu ikke aktiv. Vælg en anden <a href='$side/artikler/vis-alle.php?menu=$menu'>her</a>";
}

?>
</p>
<?php
include 'footer.php';
?>
</body>

</html>
