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
<title>anmeld tråd</title>
<meta name="description" content="anmeld tråd">
<META name="keywords" content="">
<?php
include 'head.php';
?>
</head>

<body>

<?php
include 'header.php';
?>


<h1>Anmeld indlæg til admin</h1>
<?php
//Nu skal vi opfange det id som blev sendt fra brugerlisten
$get = mysql_query("SELECT * FROM brugere WHERE Brugernavn = '$bruger' LIMIT 1") or die(mysql_error()); // henter Brugernavn som er 1?
$show = mysql_fetch_array($get);
$til = $show['Brugernavn'];

$id = mysql_real_escape_string($_GET[id]);
$traad = mysql_real_escape_string($_GET[traad]);
$gr = mysql_real_escape_string($_GET[gr]);

if(!empty($id))
{
$trad = mysql_query("SELECT * FROM forumtraad WHERE traadID = '$id'") or die(mysql_error());
$a = mysql_fetch_array($trad);
$tekst = $a['tekst'];

echo"<div>Du har valgt at melde denne tråd til admin<br><br>";
echo"<b>$tekst</b><br><br>";
echo"hvis du fortsat ønsker at melde den skal du skrive grunden herunder og trykke på send.<br>";
echo"Når du trykke på send vil der blive sendt en mail til admin som herefter vil tage stilling til hvad der skal ske.<br><br>";
echo"<form name='kommentar' method='POST' ACTION='anmeldt.php?menu=$menu&id=$id&traad=$traad&gr=$gr'>";
echo"Kommentar:<br><textarea name='tekst' rows='4' cols='40'></textarea><br>";
echo"<input class='inputknap' type='submit' value='Meld til admin'>";
echo"<input class='inputknap' type='reset' value='Nulstil'>";
echo"</form>";

} 
else 
{
echo"<h2>Der er ikke valgt nogen tråd gå tilbage <a href='$side/forum-1/laesforum.php?menu=$menu&traad=$traad&gr=$gr'>her</a></h2>";
}
?>

</div>	

<?php
include 'footer.php';
?>

</body>

</html>