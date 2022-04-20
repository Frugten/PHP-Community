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
<title>Glemt kode</title>
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

				<h1>Glemt Kode</h1>
				<p>Skriv din email i feltet herunder og tryk på knappen "send kode" så vil du 
				kort tid efter modtage en email. Denne mail vil indeholde dit brugernavn samt et nyt password
				Når du er logget ind kan du ændre dette password til et andet.<br>
				</p>
				<div align="center">
<form action="glemt-kode.php" method="POST">				
<table border="0">
		<tr>
			
<td valign="top">
<p>Email:</p>
</td>
			<td>
			<input type="text" name="email" size="45"><br>
<input type="submit" value="Send kode">
</td>
		</tr>
	</table>
	</form>

<?
if(isset($_POST["email"])) {
    $email = $_POST["email"];
    $email = strip_tags($email);
    $email = mysql_real_escape_string($email);
  
$tael = mysql_query("SELECT * FROM brugere WHERE email = '$email'") or die(mysql_error());
while($vis = mysql_fetch_array($tael))
{
$brugernavn = $vis[brugernavn];
//ny kode laves
$tal = rand(0, 1000000);
$kode = md5($tal);
$start = 0; //Hvor ønsker vi at starte? 0 er fra begyndelsen af strengen 
$langde = 8; //Vi tager 8 tegn frem 
$password = substr($kode, $start, $langde); //Udfør funktionen, og gem resultatet i en variabel 


//salt kode
//Venligst udlånt af www.phpsec.org//http:
//phpsec.org/articles/2005/password-hashing.html
//hvor lang en streng skal min salt være, maks I dette eksempel er 32
define('SALT_LENGTH', 9);
function generateHash($plainText, $salt = null)
{
if ($salt === null)
{
//Hvis der ikke er defineret noget salt, så skal der udregnes en tilfældig salt-værdi
$salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
}
else
{
//hvis $salt er udfyldt, så udtrækkes saltet fra adgangskoden
$salt = substr($salt, 0, SALT_LENGTH);
}
//Der returneres en 41 karakter lang streng der består af
// salt & md5(salt . kode)
return $salt . md5($salt . $plainText);
}
//salt kode slut
$nykode = generateHash($password);

$tilemail = $vis[email];
mysql_query("UPDATE brugere SET password='$nykode' WHERE brugernavn='$brugernavn'") or die(mysql_error());
}
    if($tilemail != "") {

	$header  = "From: $fra"; 
    $emne    = "kode til $side";
    $besked  = "BEMÆRK DENNE MAIL ER SENDT AUTOMATISK OG KAN IKKE BESVARES\r\n";
    $besked .= "Du har bedt om at få tilsendt dit brugernavn og kode til siden $side\r\n";
    $besked .= "BEMÆRK: du har fået tildelt en ny kode og du opfordres til at ændre denne når du igen er logget ind.\r\n";
    $besked .= "Brugernavn: $brugernavn\r\nPassword: $password\r\n\r\n";
    $besked .= "du kan logge ind her $side";

    mail($tilemail, $emne, $besked, $header);
echo "<h2>Der er nu sendt en email med dit brugernavn og password</h2>";

  
    } else {
              echo "<h2>Emailen er ikke blevet sendt.</h2>";
        echo "<p>Enten er feltet ikke udfyldt<br>";
        echo "ellers findes email adressen ikke i databasen";
    }
}
?>
</p>

<?
include 'footer.php';
?>
</body>

</html>