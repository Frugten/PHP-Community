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

//Nu skal vi opfange det id som blev sendt fra brugerlisten
$id = mysql_real_escape_string($_GET["id"]);

$hent = mysql_query("SELECT brugerid, brugernavn, privat FROM brugere WHERE brugerid = '$id'") or die(mysql_error());
$show = mysql_fetch_array($hent);
$privat = $show[privat];
$brugerid = $show[brugerid];
$brugernavn = $show[brugernavn];

if($brugernavn == $bruger)
{
header("Location: profil.php?menu=$menu");
}

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
$dagen_idag = date("d-m");
$resultat = mysql_query("SELECT brugerid FROM brugere WHERE brugerid = '$id'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater

//Hvis $id er sat, skal vi hente fra brugere med det id
if(!empty($id))
{
if ($number == 1)
{

$gresultat = mysql_query("SELECT venid FROM vennelister WHERE bruger = '$brugernavn' AND ven='$bruger'");//Spørger efter ID
$gnumber = mysql_num_rows($gresultat);//Tæller antaller af resultater

echo "<h1>Profil for $brugernavn</h1>";

if($gruppe == "admin"){
$vis_profil = "ja";
}
else if ($privat == "nej"){
$vis_profil = "ja";
}
else if ($privat == "ja" && $gnumber == 1){
$vis_profil = "ja";
}
else{
$vis_profil = "nej";
}
if($vis_profil =="ja")
{
$get = mysql_query("SELECT *, DATE_FORMAT(laston, '%d-%m-%Y') AS aktivdato, DATE_FORMAT(laston, '%H:%i:%s') AS aktivtid, DATE_FORMAT(oprettet, '%d-%m-%Y') AS oprettet, DATE_FORMAT(birth, '%d') AS dato, DATE_FORMAT(birth, '%m') AS maaned, DATE_FORMAT(birth, '%Y') AS aar FROM brugere WHERE brugerid = '$id'") or die(mysql_error());
$show = mysql_fetch_array($get);

$point = $show[point];
$city = $show[city];
$postnr = $show[postnr];
$kon = $show[kon];
$oprettet = $show[oprettet];
$dato = $show[dato];
$maaned = $show[maaned];
$aar = $show[aar];
$birth = $dato-$maaned-$aar;
$web = $show[web];
$interesser = $show[interesser];
$point = $show[point];
$aktivdato = $show[aktivdato];
$aktivtid = $show[aktivtid];
$online = $show[online];
$vis_gal_fav = $show[vis_galleri_favorit];
$profil_billede = $show[profil_billede];

if($profil_billede != 0)
{
$stor = list($bredde, $hojde) = getimagesize("$side/bruger/profil-billeder/$profil_billede");
$bredden = $stor[0];
$hojden = $stor[1];

if($bredde > $hojde)
{
if($bredde < 175 && $hojde < 150)
{
$profil_billede = "<img border='0' align='left' src='$side/bruger/profil-billeder/$profil_billede?cache=".microtime()."'>";
}
else
{
$profil_billede = "<img border='0' align='left' src='$side/bruger/profil-billeder/$profil_billede?cache=".microtime()."' width='175' height='150'>";
}
}
if($bredde < $hojde)
{
if($bredde < 150 && $hojde < 175)
{
$profil_billede = "<img border='0' align='left' src='$side/bruger/profil-billeder/$profil_billede?cache=".microtime()."'>";
}
else
{
$profil_billede = "<img border='0' align='left' src='$side/bruger/profil-billeder/$profil_billede?cache=".microtime()."' width='150' height='175'>";
}
}
}

$point = floor($point);

if ($online == ja)
{
$on ="<font class='online'>online</font>";
}
else
{
$on ="<font class='offline'>offline</font><br> <b>Sidst aktiv</b><br>den $aktivdato Kl. $aktivtid";
}
if ($aar-$maaned-$dato == "0000-00-00")
{
$alder = "Ikke oplyst";
}
else
{
$idag = date("Y") . date("m") . date("d");
$birth = $aar . $maaned . $dato;
$nalder = substr($idag - $birth,0,-4);
$alder = "$nalder år";
}
echo"<p align='center'>| ";
//er intern mail aktiv
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Post' AND aktiv = 'ja' AND admin ='brugermenu'");//Spørger efter ID
$mail_number = mysql_num_rows($resultat);//Tæller antaller af resultater
if ($mail_number == 1)
{
echo"<a href='$side/intern-mail/send-ny.php?menu=$menu&bruger=$brugerid'>Send intern mail til $brugernavn</a> | ";
}
//intern mail slut
echo"<a href='venneliste.php?menu=$menu&id=$id'>Venneliste for $brugernavn</a> | ";

$vsresultat = mysql_query("SELECT venid FROM vennelister WHERE bruger = '$brugernavn' AND ven='$bruger'");//Spørger efter ID
$vsnumber = mysql_num_rows($vsresultat);//Tæller antaller af resultater

if($vsnumber != 1)
{
$vresultat = mysql_query("SELECT ven_ansogid FROM ven_ansog WHERE ven = '$bruger' AND bruger='$brugernavn'");//Spørger efter ID
$vnumber = mysql_num_rows($vresultat);//Tæller antaller af resultater
if($vnumber == 1)
{
echo"Du har ansøgt om at komme på venne listen";
}
else
{
echo"<a href='ven-ansog.php?menu=$menu&id=$brugerid'>Ansøg om at komme på venneliste</a> | ";
}
}
//er Forum aktiv
$fresultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Forum' AND aktiv = 'ja' AND admin ='brugermenu'");//Spørger efter ID
$forum_number = mysql_num_rows($fresultat);//Tæller antaller af resultater
if ($forum_number == 1)
{
echo"<a href='$side/forum/dine-indlaeg.php?menu=$menu&bruger=$brugerid'>Forumtråde af $brugernavn</a> | ";
}
//forum slut

if ($dagen_idag == "$dato-$maaned")
{
$flag="<img border='0' src='$side/billeder/flag.jpg'>";
}
else
{
$flag="";
}
echo"</p>";
echo"<br>";
echo"<table><tr><td>";
echo "$on<br><br>";
echo "<b>Oprettet:</b> $oprettet<br><br>";
echo "<b>Postnr:</b> $postnr<br>";
echo "<b>By:</b> $city<br>";
echo "<b>Alder:</b> $alder<br>";
echo "<b>Køn:</b> $kon <br>";
echo "<b>Web:</b> <a target='_blank' href='http://$web'>$web</a><br>";
echo "<b>Point:</b> $point<br>";
echo "<b>Interesser:</b> $interesser";

echo"</td><td>";
echo"$flag<br><br>";
if ($profil_billede != "0")
{
echo"$profil_billede";
}
else
{
echo"$brugernavn har ikke uploadet noget billede<br>";
}

echo"</td></tr></table>";
}
else
{
echo"$brugernavn har gjort sin profil privat<br>";

$vresultat = mysql_query("SELECT ven_ansogid FROM ven_ansog WHERE ven = '$bruger' AND bruger='$brugernavn'");//Spørger efter ID
$vnumber = mysql_num_rows($vresultat);//Tæller antaller af resultater

if($vnumber == 1)
{
echo"Du har ansøgt om at komme på venne listen klik her for at <a href='ven-ansog.php?menu=$menu&id=$brugerid'>ansøge igen</a><br>";
}
else
{
echo"<a href='ven-ansog.php?menu=$menu&id=$brugerid'>Ansøg om at komme på venneliste</a> og se brugerens profil<br>";
}
echo"<br><a href='venneliste.php?menu=$menu&id=$id'>Venneliste for $brugernavn</a>";
echo"<br><br>";
//er intern mail aktiv
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Post' AND aktiv = 'ja' AND admin ='brugermenu'");//Spørger efter ID
$mail_number = mysql_num_rows($resultat);//Tæller antaller af resultater
if ($mail_number == 1)
{
echo"<a href='$side/intern-mail/send-ny.php?menu=$menu&bruger=$brugerid'>Send intern mail til $brugernavn</a>";
}
//intern mail slut
}

}
else
{
echo"Brugeren du søger findes ikke";
} 
}
//Ellers skal vi hente id for den bruger der er logget ind
else 
{
$get = mysql_query("SELECT *, DATE_FORMAT(laston, '%d-%m-%Y') AS aktivdato, DATE_FORMAT(laston, '%H:%i:%s') AS aktivtid, DATE_FORMAT(oprettet, '%d-%m-%Y') AS oprettet, DATE_FORMAT(birth, '%d') AS dato, DATE_FORMAT(birth, '%m') AS maaned, DATE_FORMAT(birth, '%Y') AS aar FROM brugere WHERE brugernavn = '$bruger' LIMIT 1") or die(mysql_error()); // henter Brugernavn som er 1?
$show = mysql_fetch_array($get);
$nid = $show[Id];
$city = $show[city];
$point = $show[point];
$postnr = $show[postnr];
$kon = $show[kon];
$dato = $show[dato];
$maaned = $show[maaned];
$aar = $show[aar];
$birth = $dato-$maaned-$aar;
$web = $show[web];
$point = $show[point];
$interesser = $show[interesser];
$oprettet = $show[oprettet];
$adato = $show[aktivdato];
$atid = $show[aktivtid];
$profil_billede = $show[profil_billede];

if($profil_billede != 0)
{
$stor = list($bredde, $hojde) = getimagesize("$side/bruger/profil-billeder/$profil_billede");
$bredden = $stor[0];
$hojden = $stor[1];

if($bredde > $hojde)
{
if($bredde < 175 && $hojde < 150)
{
$profil_billede = "<img border='0' align='left' src='$side/bruger/profil-billeder/$profil_billede?cache=".microtime()."'>";
}
else
{
$profil_billede = "<img border='0' align='left' src='$side/bruger/profil-billeder/$profil_billede?cache=".microtime()."' width='175' height='150'>";
}
}
if($bredde < $hojde)
{
if($bredde < 150 && $hojde < 175)
{
$profil_billede = "<img border='0' align='left' src='$side/bruger/profil-billeder/$profil_billede?cache=".microtime()."'>";
}
else
{
$profil_billede = "<img border='0' align='left' src='$side/bruger/profil-billeder/$profil_billede?cache=".microtime()."' width='150' height='175'>";
}
}
}

$point = floor($point);

if ($aar-$maaned-$dato == 0000-00-00)
{
$alder = "Ikke oplyst";
}
else
{
$idag = date("Y") . date("m") . date("d");
$birth = $aar . $maaned . $dato;
$nalder = substr($idag - $birth,0,-4);
$alder = "$nalder år";
}
if ($dagen_idag == "$dato-$maaned")
{
$flag="<img border='0' src='$side/billeder/flag.jpg'>";
}
else
{
$flag="";
}


echo "<h1>Din Profil</h1><br>";
if ($_SESSION['aktiv_mail'] == 1)
{
echo"<b><div class='farvet'>Før ændringen af din email træder i kraft skal den først aktiveres. Klik på ";
echo"det link du har modtaget på den nye mail</div></b><br><br>";
}

echo"<table><tr><td>";
echo "<b>Du var sidst aktiv:</b><br>den $adato kl. $atid<br><br>";
echo "<b>Oprettet:</b> $oprettet<br>";
echo "<b>Brugernavn:</b> $bruger<br>";
echo "<b>Postnr:</b> $postnr<br>";
echo "<b>By:</b> $city<br>";
echo "<b>Alder:</b> $alder<br>";
echo "<b>Køn:</b> $kon<br>";
echo "<b>Web:</b> <a target='_blank' href='http://$web'>$web</a><br>";
echo "<b>Point:</b> $point<br>";
echo "<b>Interesser:</b> $interesser<br><br>";
echo"</td><td>";
echo"$flag<br><br>";
if ($profil_billede != "0")
{
echo"$profil_billede<br>";
}
else
{
echo"Du har ikke uploadet noget billede<br>";
}
echo"</td></tr></table>";
}
include 'gaestebog.php';

?>

<?php
include 'footer.php';
?>
</body>

</html>