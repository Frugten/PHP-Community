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
<title>Aktiver profil</title>
<meta name="description" content="forum oversigt">
<META name="keywords" content="">
<?
include 'head.php';
?>
</head>
<body>
<?
include 'header.php';
?>

<h1>Aktiver ny email</h1>
<?
$bruger = $_GET[bruger];
$email = $_GET[email];
$aktivkode = $_GET[aktivkode];

if (!empty($bruger) && !empty($email) && !empty($aktivkode))
{
$_SESSION['aktiv_mail'] = 0;

mysql_query("UPDATE brugere SET mailkode = '0', email ='$email' WHERE brugernavn ='$bruger' AND mailkode ='$aktivkode'") or die(mysql_error());

echo"Hej $bruger <br>Du har nu aktiveret din email<br>";
}
else
{
echo"<h2>Enten har du allerede aktiveret din mail eller<br>";
echo"linket du har kopieret / klikket på er ikke korrekt prøv igen eller kontakt admin hvis problemet fortsætter.</h2>";
}
?>

<?
include 'footer.php';
?>
</body>

</html>