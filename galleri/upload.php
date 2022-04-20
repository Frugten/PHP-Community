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

$tael = mysql_query("SELECT vaerdi FROM settings WHERE tekst = 'bruger upload'") or die(mysql_error());
$row = mysql_fetch_array($tael);
$vaerdi = $row['vaerdi'];

if ($vaerdi == 'ja')
{
$gr = mysql_real_escape_string($_GET['gr']);

	$titel = mysql_real_escape_string($_REQUEST["titel"]);
	$info = mysql_real_escape_string($_REQUEST["info"]);

if (!empty($titel))
{
$uploadDir = '../galleri/billeder/'; //Bibliotek hvor filer uploades til - husk chmod 777
$allowedFileTypes = array('jpg','gif','png'); //Hvilke filer vil vi acceptere bliver uploadet

if(is_uploaded_file($_FILES['myFile']['tmp_name']))  
{    
//Filendelse    
$extension = strtolower(pathinfo($_FILES['myFile']['name'],PATHINFO_EXTENSION));
//Er filen en af de filer vi gerne vil have?    
if(in_array($extension,$allowedFileTypes))    
{      
//Vi flytter filen fra tmp til vores UPLOADDIR      
$dags_dato = date("d"); 
$dags_maaned = date("m"); 
$dags_aar = date("Y"); 
$dags_time = date("G"); 
$dags_minut = date("i"); 
$dags_sekund = date("s"); 
$start_fil ="$dags_dato$dags_maaned$dags_aar$dags_time$dags_minut$dags_sekund";

$filenn = "$start_fil-$bruger";

$filen = $filenn . "." . array_pop( explode( '.', $_FILES['myFile']['name'] ) );
if(move_uploaded_file($_FILES['myFile']['tmp_name'],$uploadDir . $filen))      
{        
     $_SESSION['fil_uploadet'] = "1";
}      
else 
     {        
     $_SESSION['fil_ikke'] = "1";
     header("Location: upload-billede.php?menu=$menu&gr=$gr");
	}    
     }    
     else    
     {      
     $_SESSION['filtype'] = "1";
     header("Location: upload-billede.php?menu=$menu&gr=$gr");
     }  
     }  
     else  
     {    
     }


if (isset($_SESSION['fil_uploadet']) && $_SESSION['fil_uploadet'] == 1)
{

$stor = list($bredde, $hojde) = getimagesize("$side/galleri/billeder/$filen");
$bredden = $stor[0];
$hojden = $stor[1];

//indsæt data i databasen
    mysql_query("INSERT INTO galleri (gruppe, billede, bredde, hojde, titel, info, bruger, dato)
    values('$gr', '$filen', '$bredden', '$hojden', '$titel', '$info', '$bruger', NOW())") or die(mysql_error());
 
$billedid = mysql_result(mysql_query("SELECT LAST_INSERT_ID()"),0);

    mysql_query("INSERT INTO rate (id, gruppe, samlet, rate, antal)
    values('$billedid', '$menu', '0', '0', '0')") or die(mysql_error());
    
 $_SESSION['ja'] = 1;
     $_SESSION['fil_uploadet'] = "0";
     header("Location: upload-billede.php?menu=$menu&gr=$gr");
}
}
else
{
     $_SESSION['kan_ikke'] = "1";
     header("Location: upload-billede.php?menu=$menu&gr=$gr");
}
}
else
{
echo"Du har ikke adgang til denne side klik <a href='$side?menu=$menu'>her</a>";

}

?>