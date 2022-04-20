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
//tjek om modul er aktiv
if(empty ($_SESSION['aktiv_Kalender']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Kelnder' AND aktiv = 'nej' AND admin='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Kalender'] ="nej";
}
else{
$_SESSION['aktiv_Kalender'] ="ja";
}
}

if($_SESSION['aktiv_Kalender'] == "nej")
{
$_SESSION['ikke_aktiv_modul'] = "nej";
header("Location: $side");
}
//tjek slut

$_SESSION['menu'] = "kalender";
//Al din kode herunder
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>vis nyheder</title>
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
$id = $_GET[id];
$dag = $_GET[dag];

if(!empty($id))
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

$event = mysql_query("SELECT *, DATE_FORMAT(fra, '%d') AS fradato, DATE_FORMAT(fra, '%m') AS framaaned, DATE_FORMAT(fra, '%Y') AS fraaar, DATE_FORMAT(til, '%d') AS tildato, DATE_FORMAT(til, '%m') AS tilmaaned, DATE_FORMAT(til, '%Y') AS tilaar, DATE_FORMAT(fra, '%H:%i:%s') AS fratid, DATE_FORMAT(til, '%H:%i:%s') AS tiltid  FROM kalender WHERE eventID='$id'") or die(mysql_error());
while ( $a = mysql_fetch_array($event) )
{
$eventID = $a[eventID];$titel = $a[titel];
$npris = $a[pris];
$brugeren = $a[bruger];
$postby = $a[postby];
$fradato = $a[fradato];
$framaaned = $a[framaaned];
$fraaar = $a[fraaar];
$fratid = $a[fratid];
$tildato = $a[tildato];
$tilmaaned = $a[tilmaaned];
$tilaar = $a[tilaar];
$tiltid = $a[tiltid];
$obs = $a[oplysninger];
$obs = nl2br("$obs");
$obs = url2link($obs);

$titel = stripslashes($titel);
$postby = stripslashes($postby);
$obs = stripslashes($obs);

if ($npris <= 0)
{
$pris = 'Gratis';
}
else
{
$pris = "$npris kr.";
}
echo"| "; 
if($gruppe == "admin" && $brugeren != $bruger)
{
echo"<a href='$side/admin/kalender/slet.php?dag=$dag&event=$eventID'>Slet event</a> | ";
}
if ($brugeren == $bruger)
{
echo"<a href='$side/kalender/tilfoj.php?dag=$dag&event=$eventID&ret=ja'>Ret event</a> | ";
echo"<a href='$side/kalender/slet.php?dag=$dag&event=$eventID'>Slet event</a> | ";
}
include '../favorit/tjek-favoritliste.php';
echo" |";
echo"<hr>";
echo"<h1>$titel</h1>"; 
echo"<b>Tilføjet af:</b> $bruger<br>";
echo"<b>Fra den:</b> $fradato-$framaaned-$fraaar Kl. $fratid<br>";
echo"<b>Til den:</b> $tildato-$tilmaaned-$tilaar Kl. $tiltid<br>";
echo"<b>Det sker i:</b> $postby<br>";
echo"<b>Pris:</b> $pris<br>";
echo"<b>Yderlige oplysninger:</b><br>$obs<br><hr>";

$vis_fra = mysql_real_escape_string($_GET["visfra"]);

echo "Tilbage til oversigten over events klik <a href='vis.php?menu=$menu&visfra=$vis_fra&dag=$dag'>her</a>";
}

}

//Ellers skal vi hente id for den bruger der er logget ind
else 
{
echo "<h2>Du skal vælge en event som du vil læse mere om</h2>";
echo "<h2>For at vælge en event klik <a href='vis.php?menu=$menu&dag=$dag'>her</a></h2>";
}
?>

<?
include 'footer.php';
?>
</body>

</html>
