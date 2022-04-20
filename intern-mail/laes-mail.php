<?
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
//Tjekker om modulet er aktivt
if(empty ($_SESSION['aktiv_Post']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Post' AND aktiv = 'nej' AND admin='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Post'] ="nej";
}
else{
$_SESSION['aktiv_Post'] ="ja";
}
}

if($_SESSION['aktiv_Post'] == "nej")
{
$_SESSION['ikke_aktiv_modul'] = "nej";
header("Location: $side");
}
//aktiv modul tjek slut

//Al din kode herunder
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title></title>
<meta name="Description" content="">
<meta name="Keywords" content="">
<?
include 'head.php';
?>
</head>
<body>
<?
include 'header.php';
?>
<?
$id = mysql_real_escape_string($_GET['id']);

$resultat = mysql_query("SELECT *, DATE_FORMAT(sendt, '%d-%m-%Y') AS dato, DATE_FORMAT(sendt, '%H-%i-%s') AS klok FROM mail_ind WHERE indid= '$id' AND til = '$bruger'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if ($number == 1)
{
$show = mysql_fetch_array($resultat);
$fra = $show[fra];
$emne = $show[emne];
$mail = $show[mail];
$dato = $show[dato];
$klok = $show[klok];
$laest = $show[laest];

$brresultat = mysql_query("SELECT brugerid FROM brugere WHERE brugernavn= '$fra'");//Spørger efter ID
$brshow = mysql_fetch_array($brresultat);
$brugerid = $brshow[brugerid];


if ($laest != "ja")
{
mysql_query("UPDATE mail_ind SET laest='ja' WHERE indid='$id'") or die(mysql_error());
mysql_query("UPDATE mail_ud SET laest='ja' WHERE indid='$id'") or die(mysql_error());
}
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


$mail = htmlentities($mail);
$emne = htmlentities($emne);

//replace [b] og [/b] til <b> og </b> i $text
$ord_der_skal_erstattes = array("[b]","[/b]","[i]","[/i]","[u]","[/u]","[boks]","[/boks]"); 
$erstat_ord_med = array("<b>","</b>","<i>","</i>","<u>","</u>","<div class='boks'>","</div>"); 
$mail = str_replace($ord_der_skal_erstattes, $erstat_ord_med, $mail); 
//erstat slut

$mail = stripslashes($mail);
$emne = stripslashes($emne);

//tjekker hvilke ord der ikke er tilladte og erstatter med andre
$ban = mysql_query("SELECT p_ord, g_ord FROM ban ORDER BY banid");
while($rs = mysql_fetch_array($ban))
{
    $bad[]= "/" . preg_quote( $rs['g_ord'], "/" ) . "/i";
    $good[] = $rs['p_ord'];
}
$emne = preg_replace( $bad, $good, $emne );
$mail = preg_replace( $bad, $good, $mail );
//tjek slut

$mail = nl2br("$mail");
$mail = url2link($mail);

//smileys insættes
$smil = mysql_query("SELECT tekst, billede FROM smiley ORDER BY smilid");
while($smi = mysql_fetch_array($smil))
{
  $mail = str_replace($smi['tekst'], "<img border='0' src='$side/smiley/".$smi['billede']."'>", $mail);

}
//smiley slut


echo"<h1>$emne</h1>";
echo"Sendt fra <b>$fra</b> den $dato kl. $klok<br>";
echo"<hr>";
echo"<a href='besvar-mail.php?menu=$menu&bruger=$brugerid&mail=$id'>Besvar mail</a>";
echo"<hr>";
echo"$mail<br><br>";

}
else
{
echo"Du har ikke modtaget denne mail. Vælg en af dine egne <a href='indbakke.php?menu=$menu'>her</a>";
}
?>
<?
include 'footer.php';
?>
</body>

</html>