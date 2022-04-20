<?php
session_start();
include '../settings/connect.php';

if(!isset($_POST["submit"])) { //Tester om brugeren kom fra  login.html
    echo "Du skal komme fra <a href='../index.php'>index.html</a>";
    }
else
    {


        $postbruger = $_POST["brugernavn"];
 $_SESSION['brugernavn'] =$postbruger;
        $postpass = $_POST["password"]; //For læservenligheden overføres post variablerne til normale variabler

        $resultat = mysql_query("SELECT password, deaktiver FROM brugere WHERE brugernavn = '$postbruger'");//Spørger efter ID
        $show = mysql_fetch_array($resultat);
$deaktiver = $show[deaktiver];
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
$checkkode = generateHash($postpass,$dbkode);
        
        $resultat = mysql_query("SELECT brugerid,gruppe FROM brugere WHERE brugernavn = '$postbruger' AND password = '$checkkode'");//Spørger efter ID
        $number = mysql_num_rows($resultat);//Tæller antaller af resultater
        $show = mysql_fetch_array($resultat);

if($number == 1)
{ 
if ($deaktiver != '0')        
{       
echo"Hej $postbruger<br>";
echo"Admin har deaktiveret dig med følgende grund<br><br>";
echo"<b>$deaktiver</b><br><br>";
echo"Kontakt admin og hør om dine muligheder for igen at blive aktiveret";
}
else
{

 //vil brugeren logge automatisk ind
if (isset($_POST["huskmig"])) 
{
setcookie("huskbrugernavn",$_POST["brugernavn"],time()+(60*60*24*30));
setcookie("huskpassword",$checkkode,time()+(60*60*24*30));
        $_SESSION['auto_login'] = "ja";
}

       //Hvis der kun er et resultat, bliver brugeren logget ind
        $_SESSION['logget_in'] = 1;
        $_SESSION['brugernavn'] = $postbruger;
        $_SESSION['password'] = $checkkode; //Sætter session variablerne
        $_SESSION['gruppe'] = $show[gruppe];
        


$bruger = $_SESSION['brugernavn'];
//udføre diverse handlinger

//udregner points
include 'point-udregning.php';
//point udregning slut

mysql_query("UPDATE brugere SET ontid= NOW(), online = 'ja', logget_ind = NOW() WHERE brugernavn='$bruger'") or die(mysql_error());

//diverse handlinger
mysql_query("DELETE FROM tagwall WHERE DATE_ADD(tid, INTERVAL 1 WEEK) < NOW()") or die(mysql_error());
//handlinger slut



$goto_page = $_SESSION['page'];
if(!empty($goto_page)) 
{
header("Location: $goto_page");
}
else
{
header("Location: $side/bruger/profil.php?menu=Bruger");
}
}
}
else 
{
$_SESSION['ikke_inde'] = 1;
header("Location: $side/index.php?menu=Bruger");
}
}
?>