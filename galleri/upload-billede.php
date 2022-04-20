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



if ($gruppe == 'admin')
{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
<title>opret gruppe</title>
<meta name="description" content="Ret din profil">
<META name="keywords" content="">
<?php
include 'head.php';
?>
</head>

<body>
<?php
include 'header.php';
?>

<?php
$tael = mysql_query("SELECT vaerdi FROM settings WHERE tekst = 'bruger upload'") or die(mysql_error());
$row = mysql_fetch_array($tael);
$vaerdi = $row['vaerdi'];

if ($vaerdi == 'ja')
{
$gr = mysql_real_escape_string($_GET['gr']);

if(!empty ($gr))
{
$tael = mysql_query("SELECT titel FROM gallerigr WHERE grID= '$gr'") or die(mysql_error());
$row = mysql_fetch_array($tael);
$grtitel = $row['titel'];

echo"<h1>Upload billede til kategorien<br>$grtitel</h1>";

echo"<div class='farvet'>";
if (isset($_SESSION['ja']) && $_SESSION['ja'] == 1)
{
echo"Billedet er nu tilføjet<br>";
$_SESSION['ja'] = 0;
}

if (isset($_SESSION['ikke']) && $_SESSION['ikke'] == 1)
{
echo"Billedet er ikke tilføjet<br>";
$_SESSION['ikke'] = 0;
}

if (isset($_SESSION['fil_ikke']) && $_SESSION['fil_ikke'] == 1)
{
echo"Filen kunne ikke uploades<br>";
$_SESSION['fil_ikke'] = 0;
}
if (isset($_SESSION['filtype']) && $_SESSION['filtype'] == 1)
{
echo"Filtypen er ikke tilladt. Tilladte filtyper er (jpg, gif, png)<br>";
$_SESSION['filtype'] = 0;
}
if (isset($_SESSION['kan_ikke']) && $_SESSION['kan_ikke'] == 1)
{
echo"Et eller flere felter er ikke udfyldt.<br>";
$_SESSION['kan_ikke'] = 0;
}
echo"</div>";

echo"<form name='spg' method='POST' ACTION='upload.php?menu=$menu&gr=$gr' enctype='multipart/form-data'>";
?>
<table><tr>
<tr><td>*Titel:</td><td><input type='text' name='titel' size='40' maxlength='255'></td></tr>
<tr><td>Info:</td><td> 
<textarea name='info' rows='10' cols='50'></textarea></td></tr>
<tr><td>Billede:</td><td>(jpg, gif, png) <input type="file" name="myFile"><br /></td></tr>
<tr><td></td><td><input class='inputknap' type='submit' value='Send'>
<input class='inputknap' type='reset' value='Nulstil'></td></tr>
</table>
</form>
</div>
<?php
}
else
{
echo"<h1>Vælg kategori</h1>";
echo"Klik på den kategori du vil uploade billeder i<br><br>";
echo"<table width='100%'>";
$grupper = mysql_query("SELECT grID, titel, beskrivelse FROM gallerigr ORDER BY visning ASC") or die(mysql_error());
while ( $b = mysql_fetch_array($grupper))
{
$id = $b['grID'];
$titel = $b['titel'];
$beskrivelse = $b['beskrivelse'];

$tael = mysql_query("SELECT COUNT(*) AS antal FROM galleri WHERE gruppe= '$id'") or die(mysql_error());
$row = mysql_fetch_array($tael);
$antallet = $row['antal'];

echo"<tr><td class='kant'><a href='?menu=$menu&gr=$id'><b>$titel</b></a><br>";
echo"$beskrivelse</td><td class='kant'>$antallet</td></tr>";
}
echo"</table>";
}
}
else
{
echo"Du har ikke adgang til denne side klik <a href='$side?menu=$menu'>her</a>";
}
include 'footer.php';
?>

</body>

</html>
<?php
}
else
{
echo"Det er kun admins der har adgang til denne side<br> <a href='$side?menu=$menu'>klik her</a>";
}
?>