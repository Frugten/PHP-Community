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
$_SESSION['menu'] = "profil";
//Al din kode herunder

if(!empty($_POST["glpassword"]) && !empty($_POST["nypassword"]))
    {
$get = mysql_query("SELECT password FROM brugere WHERE brugernavn = '$bruger' LIMIT 1") or die(mysql_error()); // henter Brugernavn som er 1?
$show = mysql_fetch_array($get);
$bid = $show[Id];
$dbkode = $show[password];

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


    $glkode = mysql_real_escape_string( $_POST["glpassword"] );
    
    $nykode = mysql_real_escape_string( $_POST["nypassword"] );

$checkkode = generateHash($glkode,$dbkode);
$nysaltkode = generateHash($nykode);

if($dbkode == $checkkode)
{
mysql_query("UPDATE brugere SET password='$nysaltkode' WHERE brugernavn='$bruger'") or die(mysql_error());

header("Location: $side/settings/logud.php");

}
else
{
echo"Den gamle kode er ikke korrekt";
}
}
else
{
echo"Begge felter skal udfyldes";
}
?>