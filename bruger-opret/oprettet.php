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
<title>Opret bruger</title>
<meta name="Description" content="opret en bruger og f� ideer til din n�ste fest">
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
if($_SESSION['oprettet'] == 1)
{
echo"<h1>Du blev oprettet</h1>";
    echo "<h2>Du er nu oprettet som bruger, men f�r du kan logge ind skal du aktivere din profil.<br>";
    echo"Klikke p� det link der er i den email som er sendt til din email adresse<br>";
    echo"Hvis du ikke har modtaget en mail, kan du m�ske finde den i din spam mappe. Hvis du ikke modtager en mail ";
    echo"inden for et d�gn er du velkommen til at kontakte admin, som kan hj�lpe dig videre";

$_SESSION['oprettet'] ="";
}
else
{
echo"<h2>Du kan ikke se denne side</h2>";
}	
?>
			
<?
include 'footer.php';
?>
</body>

</html>