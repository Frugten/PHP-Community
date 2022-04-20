<?
session_start();
include '../settings/connect.php';
include '../settings/settings.php';

if ($_SESSION['deaktiver_auto'] == 1)
{
setcookie("huskbrugernavn","",time()-(60*60*24*30));
setcookie("huskpassword","",time()-(60*60*24*30));

mysql_query("UPDATE brugere SET online = 'nej', laston = NOW() WHERE brugernavn ='$bruger'") or die(mysql_error());

$_SESSION ['ikke_logget_in'] = "ja";

$_SESSION = array();
session_destroy();
header("Location: $side");
exit;
}

if(!empty($_COOKIE['huskbrugernavn']) && !empty($_COOKIE['huskpassword'])) 
{
$_SESSION ['ikke_logget_in'] = "ja";

        $resultat = mysql_query("SELECT brugerid, gruppe, deaktiver FROM brugere WHERE brugernavn = '" . $_COOKIE["huskbrugernavn"] . "' AND password = '" . $_COOKIE["huskpassword"] . "'");//Spørger efter ID
        $number = mysql_num_rows($resultat);//Tæller antaller af resultater
        $show = mysql_fetch_array($resultat);

if($number == 1)
{ 
$deaktiver = $show[deaktiver];

if ($deaktiver != '0')        
{       
echo"Hej $postbruger<br>";
echo"Admin har deaktiveret dig med følgende grund<br><br>";
echo"<b>$deaktiver</b><br><br>";
echo"Kontakt admin og hør om dine muligheder for igen at blive aktiveret";
}
else
{
         //Hvis der kun er et resultat, bliver brugeren logget ind
        $_SESSION['logget_in'] = 1;
        $_SESSION['brugernavn'] = $_COOKIE["huskbrugernavn"];
        $_SESSION['password'] = $_COOKIE["huskbrugernavn"]; 
        $_SESSION['gruppe'] = $show[gruppe];
        $_SESSION['auto_login'] = "ja";

$_SESSION['menu'] = "bruger";

$bruger = $_SESSION['brugernavn'];
//udregner hvor mange point brugeren skal have
$perio = mysql_query("SELECT * FROM settings WHERE tekst = 'periodepoints'") or die(mysql_error());
while ( $pe = mysql_fetch_array($perio))
{
$periodepoints = $pe[vaerdi];
}

$brug = mysql_query("SELECT ontid, logget_ind, point FROM brugere WHERE brugernavn ='$bruger'") or die(mysql_error());
while ( $b = mysql_fetch_array($brug))
{
$logget_ind = $b[logget_ind];
$ontid = $b[ontid];
$point = $b[point];

$ontid = strtotime("$ontid"); 
$logget_ind = strtotime("$logget_ind"); 

$sekunder = $ontid - $logget_ind; 
$time = ($sekunder / 60) / 60;

$udregnet_point = $time * $periodepoints;
$points = round($udregnet_point, 2);
$points = $point + $points;

mysql_query("UPDATE brugere SET point ='$points' WHERE brugernavn='$bruger'") or die(mysql_error());
}
//udregning og opdatering er slut

//gemmer login tid
mysql_query("UPDATE brugere SET logget_ind = NOW() WHERE brugernavn='$bruger'") or die(mysql_error());


$goto_page = $_SESSION['page'];
if(!empty($goto_page)) 
{
header("Location: $goto_page");
}
else
{
header("Location: $side/bruger/profil.php");
}
}
}}
else
{
$_SESSION ['ikke_logget_in'] = "ja";
$_SESSION['logget_in'] = 'ikke';
header("Location: $side");
}
?>