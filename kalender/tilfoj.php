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

$ret = mysql_real_escape_string($_GET['ret']);
$event = mysql_real_escape_string($_GET['event']);
$dag = mysql_real_escape_string($_GET['dag']);
if($ret == "ja" && !empty($event))
{
$get = mysql_query("SELECT *, DATE_FORMAT(fra, '%d-%m-%Y') AS fra_dato, DATE_FORMAT(fra, '%H:%i:%s') AS fra_kl, DATE_FORMAT(til, '%d-%m-%Y') AS til_dato, DATE_FORMAT(til, '%H:%i:%s') AS til_kl FROM kalender WHERE eventID = '$event'") or die(mysql_error());
$show = mysql_fetch_array($get);

$point = $show[point];

$_SESSION['fra_dato'] =	$show[fra_dato];
$_SESSION['fra_kl'] = $show[fra_kl];
$_SESSION['til_dato'] =	$show[til_dato];
$_SESSION['til_kl'] = $show[til_kl];
$_SESSION['titel'] = $show[titel];
$_SESSION['postby'] = $show[postby];
$_SESSION['pris'] =	$show[pris];
$_SESSION['obs'] =	$show[oplysninger];

}
else
{
$_SESSION['fra_dato'] =	$dag;
$_SESSION['til_dato'] =	$dag;
}

if (!empty ($dag))
{
if($ret != "ja")
{
echo"<h1>Tilføj til kalender</h1>";
}
else
{
echo"<h1>Ret event</h1>";
}
echo"<h2>- Dato skal skrives i formatet DD-MM-ÅÅÅÅ <br>";
echo"- Tiden skal skrives i formatet TT:MM <br>";
echo"- Hvis eventen vare flere dage skal start tiden ligge den første dag og slut tiden den sidste dag</h2>";

if ($_SESSION['tilfojet'] == 1)
{
echo"<div class='farvet'>Event er tilføjet</div>";
$_SESSION['tilfojet'] = 0;
}
if ($_SESSION['ikke'] == 1)
{
echo"<div class='farvet'>Event er ikke tilføjet, da et eller flere felter ikke er korrekt udfyldt</div>";
$_SESSION['ikke'] = 0;
}
$obs = $_SESSION['obs'];
$_SESSION['obs'] =stripslashes($obs);

echo"<form name='spg' method='POST' ACTION='tilfoj-ny.php?menu=$menu&ret=$ret&event=$event'>";
echo"<table>";
echo"<tr><td>*Fra dato.:</td><td><input value='".$_SESSION['fra_dato']."' type='text' name='fra_dato' size='10' maxlength='10'> ";
echo"*Kl.:<input type='text' name='fra_kl' size='6' value='".$_SESSION['fra_kl']."' maxlength='5'></td></tr> ";
echo"<tr><td>*Til dato.:</td><td><input value='".$_SESSION['til_dato']."' type='text' name='til_dato' size='10' maxlength='10'> ";
echo"*Kl.:<input type='text' name='til_kl' size='6' value='".$_SESSION['til_kl']."' maxlength='5'></td></tr>";
echo"<tr><td>*Titel:</td><td><input type='text' value='".$_SESSION['titel']."' name='titel' size='45' maxlength='255'></td></tr>";
echo"<tr><td>*Postnr og by:</td><td><input type='text' value='".$_SESSION['postby']."' name='postby' size='25' maxlength='255'></td></tr>";
echo"<tr><td>*Pris:</td><td><input type='text' value='".$_SESSION['pris']."' name='pris' size='5' maxlength='10'></td></tr>";
echo"<tr><td>*Yderlige oplysninger</td><td>";
echo"(hvor tilmelder man sig, hvor kan man læse om arrangementet osv.):<br>";
echo"<textarea name='obs' rows='4' cols='40'>".$_SESSION['obs']."</textarea>";
echo"</td></tr>";
echo"<tr><td>Markeret:</td><td><input type='checkbox' name='markeret' value='ja'> ";
echo"Sæt kryds her hvis eventen skal markeres i kalenderen</td></tr>";
?>
<tr><td></td><td>
<input class='inputknap' type='submit' value='Send'>
<input class='inputknap' type='reset' value='Nulstil'>
</td></tr>

</table>

</form>

<?
$_SESSION['fra_dato'] =	"";
$_SESSION['fra_kl'] = "";
$_SESSION['til_dato'] =	"";
$_SESSION['til_kl'] = "";
$_SESSION['titel'] = "";
$_SESSION['postby'] = "";
$_SESSION['pris'] =	"";
$_SESSION['obs'] =	"";

}
else
{
echo"Du skal vælge en dato i <a href='kalender.php'>kalenderen</a>";
}
?>
<?
include 'footer.php';
?>
</body>

</html>
