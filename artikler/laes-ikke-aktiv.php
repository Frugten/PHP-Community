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
<p>
<?php
$id = mysql_real_escape_string($_GET['id']);
$rat = mysql_query("SELECT artikelid FROM artikel WHERE artikelid = '$id' AND aktiv='nej'");
        $nantal = mysql_num_rows($rat);//Tæller antaller af resultater
if($nantal == 1)
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

$artikler = mysql_query("SELECT * ,DATE_FORMAT(dato, '%d-%m-%Y') AS dato FROM artikel WHERE artikelid = $id");
while ($d = mysql_fetch_assoc($artikler))
{
$id = $d[artikelid];
$dato = $d[dato];
$brugernavn = $d[brugernavn];
$titel = $d[titel];
$beskrivelse = $d[beskrivelse];
$artikel = $d[artikel];
$pris = $d[pris];

$bille = mysql_query("SELECT profil_billede FROM brugere WHERE brugernavn = '$brugernavn'") or die(mysql_error());
while ( $bil = mysql_fetch_array($bille))
{
$for_billede = $bil[profil_billede];
}


$titel = htmlentities($titel);
$beskrivelse = htmlentities($beskrivelse);
$artikel = htmlentities($artikel);

//replace [b] og [/b] til <b> og </b> i $text
$ord_der_skal_erstattes = array("[b]","[/b]","[i]","[/i]","[u]","[/u]","[boks]","[/boks]"); 
$erstat_ord_med = array("<b>","</b>","<i>","</i>","<u>","</u>","<div class='boks'>","</div>"); 
$artikel = str_replace($ord_der_skal_erstattes, $erstat_ord_med, $artikel); 
//erstat slut

$titel = strip_tags($titel, "<b>, <i>, <u>, <div>");
$titel = nl2br("$titel");
$titel = stripslashes($titel);

$beskrivelse = strip_tags($beskrivelse, "<b>, <i>, <u>, <div>");
$beskrivelse = nl2br("$beskrivelse");
$beskrivelse = stripslashes($beskrivelse);

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
echo"<b>Denne artikel er endnu ikke aktiv ønsker du at:</b> ";
echo"<a href='rette.php?menu=$menu&id=$id'>Rette artiklen</a> | <a href='aktiver.php?menu=$menu&id=$id'>Aktivere artiklen</a>";

echo"<h1>$titel</h1>";
if ($for_billede != 0)
{
echo"<img width='50' height='60' align='left' src='$side/bruger/profil-billeder/$for_billede' alt='Profil Billede'>";
}
else
{
echo"";
}

echo"<b>$beskrivelse</b><br>";
echo"Tilføjet af $brugernavn den. $dato<br>";
echo"Ariklen koster $pris point<hr>";
echo "<p>$artikel<br><br></p>";

}
}
else
{
echo"Den artikel du forsøger at åbner er allerede aktiv. Vælg en anden <a href='$side/artikler/ikke-aktive.php?menu=$menu'>her</a>";
}

?>
</p>
<?php
include 'footer.php';
?>
</body>

</html>
