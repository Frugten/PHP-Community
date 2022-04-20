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
		
				<h1>Opret bruger</h1>
				<h2>Alle felter skal udfyldes. <br>Felter med * bliver ikke synlig af andre brugere.</h2>

<?
if(isset($_POST['brugernavn'])) {
    $fornavn = $_POST['fornavn'];
    $fornavn = strip_tags($fornavn);
    $fornavn = mysql_real_escape_string($fornavn);

    $efternavn = $_POST['efternavn'];
    $efternavn = strip_tags($efternavn);
    $efternavn = mysql_real_escape_string($efternavn);

    $brugernavn = $_POST['brugernavn'];
    $brugernavn = strip_tags($brugernavn);
    $brugernavn = mysql_real_escape_string($brugernavn);
    
    $password = $_POST['password'];
    $password = strip_tags($password);
    $password = mysql_real_escape_string($password);
    
    $email = $_POST['email'];
    $email = strip_tags($email);
    $email = mysql_real_escape_string($email);

    $nyhedsbrev = $_POST['nyhedsbrev'];
    $nyhedsbrev= strip_tags($nyhedsbrev);
    $nyhedsbrev = mysql_real_escape_string($nyhedsbrev);


$_SESSION['fornavn'] = $fornavn;
$_SESSION['efternavn'] = $efternavn;
$_SESSION['brugernavn'] = $brugernavn;
$_SESSION['password'] = $password;
$_SESSION['email'] = $email;
    
    $tael = mysql_query("SELECT COUNT(*) AS antal FROM brugere WHERE brugernavn = '$brugernavn'") or die(mysql_error());
$row = mysql_fetch_array($tael);
$antal = $row[antal];

    $htael = mysql_query("SELECT COUNT(*) AS antal FROM brugere WHERE email = '$email'") or die(mysql_error());
$hrow = mysql_fetch_array($htael);
$hantal = $hrow[antal];

    $ntael = mysql_query("SELECT COUNT(*) AS antal FROM aktiver WHERE brugernavn = '$brugernavn'") or die(mysql_error());
$nrow = mysql_fetch_array($ntael);
$nantal = $nrow[antal];

$gtael = mysql_query("SELECT COUNT(*) AS antal FROM aktiver WHERE email = '$email'") or die(mysql_error());
$grow = mysql_fetch_array($gtael);
$gantal = $grow[antal];

$ggtael = mysql_query("SELECT vaerdi FROM settings WHERE tekst = 'slettet_brugernavn'") or die(mysql_error());
$ggrow = mysql_fetch_array($ggtael);
$slettet_brugernavn = $ggrow[vaerdi];

echo"<div class='farvet'>";

    if(!ereg("^[-a-zA-Z0-9������]*$", $brugernavn)) 
{
$_SESSION['brugernavn'] = "";
echo"- Brugernavn m� kun indholde bogstaver (a-�) og tal<br>";
}
    if(!ereg("^[-a-zA-Z0-9������]*$", $password)) 
{
$_SESSION['password'] = "";
echo"- Password m� kun indholde bogstaver (a-�) og tal<br>";
}

if($antal > 0 or $nantal > 0 OR $slettet_brugernavn == $brugernavn)
{
$_SESSION['brugernavn'] = "";
echo"- Brugernavnet er optaget<br>";
}
if($hantal > 0 or $gantal > 0)
{
$_SESSION['email'] = "";
echo"- Emailen er brugt ved en anden bruger<br>";
}
if(empty($fornavn))
{
echo"- Fornavn er ikke udfyldt<br>";
}
if(empty($efternavn))
{
echo"- Efternavn er ikke udfyldt<br>";
}
if(empty($brugernavn))
{
echo"- Bugernavn er ikke udfyldt<br>";
}
if(empty($password))
{
echo"- Password er ikke udfyldt<br>";
}
if(empty($email))
{
echo"- Email er ikke udfyldt<br>";
}

echo"</div>";

   if(ereg("^[-a-zA-Z0-9������]*$", $brugernavn) && ereg("^[-a-zA-Z0-9������]*$", $password) && $antal == 0 && $nantal == 0 && $hantal == 0 && $gantal == 0 && !empty($fornavn) && !empty($efternavn) && !empty($brugernavn) && !empty($password) && !empty($email)) 
   {
    $tal = rand(0, 1000000);
$ntal = md5($tal);
$nej = "nej";

//salt kode
//Venligst udl�nt af www.phpsec.org//http:
//phpsec.org/articles/2005/password-hashing.html
//hvor lang en streng skal min salt v�re, maks I dette eksempel er 32
define('SALT_LENGTH', 9);
function generateHash($plainText, $salt = null)
{
if ($salt === null)
{
//Hvis der ikke er defineret noget salt, s� skal der udregnes en tilf�ldig salt-v�rdi
$salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
}
else
{
//hvis $salt er udfyldt, s� udtr�kkes saltet fra adgangskoden
$salt = substr($salt, 0, SALT_LENGTH);
}
//Der returneres en 41 karakter lang streng der best�r af
// salt & md5(salt . kode)
return $salt . md5($salt . $plainText);
}
//salt kode slut
$nykode = generateHash($password);

    mysql_query("INSERT INTO aktiver (aktiverid, brugernavn, password, fornavn, efternavn, email, aktiv, dato, nyhedsbrev)
    VALUES(0, '$brugernavn', '$nykode', '$fornavn', '$efternavn', '$email', '$ntal', NOW(), '$nyhedsbrev')") or die(mysql_error());
 $aktivid = mysql_result(mysql_query("SELECT LAST_INSERT_ID()"),0); 

require("../phpmailer/class.phpmailer.php");


    $html_mail  = "BEM�RK DENNE MAIL ER SENDT AUTOMATISK OG KAN IKKE BESVARES<br>";
    $html_mail .= "Du eller en anden har oprettet en bruger p� $side.<br>";
    $html_mail .= "F�r du kan logge ind skal du klikke p� linket herunder<br>";
    $html_mail .= "<a href='$side/bruger-opret/bruger-aktiver.php?aktivid=$aktivid&aktivkode=$ntal'>Aktiver profil</a><br><br>";
    $html_mail .= "Virker linket ikke kan du kopiere denne og inds�tte den i din browser<br>";
    $html_mail .= "$side/bruger-opret/bruger-aktiver.php?aktivid=$aktivid&aktivkode=$ntal<br><br>";
    $html_mail .= "hilsen webmaster";

    $alm_mail  = "BEM�RK DENNE MAIL ER SENDT AUTOMATISK OG KAN IKKE BESVARES\r\n";
    $alm_mail .= "Du eller en anden har oprettet en bruger p� $side.\r\n";
    $alm_mail .= "F�r du kan logge ind skal du kopiere dette link og inds�tte det i din browser\r\n\r\n";
    $alm_mail .= "$side/bruger-opret/bruger-aktiver.php?aktivid=$aktivid&aktivkode=$ntal\r\n\r\n";
    $alm_mail .= "hilsen webmaster";

$mail = new PHPMailer();
$mail->From     = "$fra";
$mail->FromName = "$side";

$mail->Subject  =  "Aktiver din profil p� $side";
$mail->Body    = $html_mail;
$mail->AltBody = $alm_mail;
$mail->AddAddress($email, $email);

echo"<div class='farvet'>";
if ( ($mail->Send()) )
{
$_SESSION['oprettet'] = 1;


$_SESSION['fornavn'] = "";
$_SESSION['efternavn'] = "";
$_SESSION['brugernavn'] = "";
$_SESSION['password'] = "";
$_SESSION['email'] = "";

$_SESSION['oprettet'] = 1;

?>
<script type="text/javascript">
<?
echo"window.location.href='$side/bruger-opret/oprettet.php';";
echo"</script>";
} 
else
{
echo"Der skete en fejl, pr�v venligst igen";
}  
echo"</div>";  
 }   
    else 
    {
    }
}

echo"<form action='opret.php' method='POST'>";				
echo"<table border='0' align='center'>";
echo"<tr>";
echo"<td>*Fornavn:</td><td><input type='text' name='fornavn' value='".$_SESSION['fornavn']."' maxlength='255'></td></tr><tr>";
echo"<td>*Efternavn:</td><td><input type='text' name='efternavn' value='".$_SESSION['efternavn']."' maxlength='255'></td></tr><tr>";
echo"<td>Brugernavn:</td><td><input type='text' name='brugernavn' value='".$_SESSION['brugernavn']."' maxlength='255'></td></tr><tr>";
echo"<td>*Passsword:</td><td><input type='password' name='password' value='".$_SESSION['password']."' maxlength='255'></td></tr><tr>";
echo"<td >*Email:</td><td><input type='text' name='email' value='".$_SESSION['email']."' maxlength='255'></td></tr>";
echo"<tr><td></td><td>Jeg �nsker du at modtage nyhedsbrevet fra denne side?";
echo"<input type='checkbox' name='nyhedsbrev' value='ja' checked><br>Dette kan du altid afmelde igen<br>";
echo"</td></tr>";
echo"<tr><td></td><td>N�r du klikker p� opret acceptere du ogs� ";
echo"<a target='_blank' href='$side/betingelser.php'>Betingelserne</a> for at v�re bruger p� denne side<br>";
echo"<input type='submit' value='Opret'></td></tr>";
echo"</form>";
echo"</table>";


$_SESSION['fornavn'] = "";
$_SESSION['efternavn'] = "";
$_SESSION['brugernavn'] = "";
$_SESSION['password'] = "";
$_SESSION['email'] = "";

?>

<p>N�r du trykker p� opret vil du modtage en mail med et link du skal trykke p� for at aktivere din mail<br>
Herefter vil du kunne logge ind</p>
				
				
<?
include 'footer.php';
?>
</body>

</html>