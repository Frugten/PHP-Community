<?
session_start();
chdir('../layout/');
include '../settings/connect.php';
include '../settings/settings.php';

//Al din kode
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Aktiver bruger</title>
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

<h1>Aktiver bruger</h1>
<?
$aktivid = $_GET[aktivid];
$aktivkode = $_GET[aktivkode];

if (!empty($aktivid) && !empty($aktivkode))
{
$bresultat = mysql_query("SELECT vaerdi FROM settings WHERE tekst = 'startpoints'");//Spørger efter ID
while ( $b = mysql_fetch_array($bresultat))
{
$point = $b[vaerdi];
}

$resultat = mysql_query("SELECT * FROM aktiver WHERE aktiverid = '$aktivid' AND aktiv = '$aktivkode'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1)
{
while ( $a = mysql_fetch_array($resultat))
{
$brugernavn = $a[brugernavn];
$password = $a[password];
$fornavn = $a[fornavn];
$efternavn = $a[efternavn];
$email = $a[email];
$dato = $a[dato];
$nyhedsbrev = $a[nyhedsbrev]; 

if ($nyhedsbrev == "ja")
{
mysql_query("INSERT INTO mail_abb (bruger, gruppe)
VALUES('$brugernavn', 'nyhedsbrev')") or die(mysql_error());
}
   mysql_query("INSERT INTO brugere (brugernavn, password, email, fornavn, efternavn, gruppe, oprettet, point, laston, ontid, logget_ind)
    VALUES('$brugernavn', '$password', '$email', '$fornavn', '$efternavn', 'bruger', NOW(), '$point', NOW(), NOW(), NOW())") or die(mysql_error());

mysql_query("DELETE FROM aktiver WHERE aktiverid = '$aktivid' AND aktiv = '$aktivkode'") or die(mysql_error());
}
echo"<h2>Hej $fornavn $efternavn<br><br>";
echo"Du har nu aktiveret din profil og du kan logge ind<br>";
}
else
{
echo"<h2>Du er allerede aktiveret og kan logge ind <br><br>eller <br><br>";
echo"Linket du har kopieret / klikket på er ikke korrekt prøv igen eller kontakt admin hvis problemet fortsætter.</h2>";
}

}
else
{
echo"<h2>Linket du har kopieret / klikket på er ikke korrekt prøv igen eller kontakt admin hvis problemet fortsætter.</h2>";
}

?>

<?
include 'footer.php';
?>
</body>

</html>