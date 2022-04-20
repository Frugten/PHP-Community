<?php
session_start();
$_SESSION['page'] = $_SERVER['REQUEST_URI'];
include '../settings/connect.php';
include '../settings/settings.php';

if(!$_SESSION['logget_in'] == 1 OR $_SESSION['logget_in'] == "ikke") 
{
$_SESSION['ikke_log'] = 1;
header("Location: $side");//Sender brugeren videre
exit;//Sørger for at resten af koden, ikke bliver udført
}
$_SESSION['menu'] = "bruger";
//Al din kode herunder

$uploadDir = 'profil-billeder/'; //Bibliotek hvor filer uploades til - husk chmod 777
$allowedFileTypes = array('jpg','JPG','gif','GIF','png'); //Hvilke filer vil vi acceptere bliver uploadet?
//Er der trykket på Upload file knappen?
if(isset($_POST['Submit'])){  //Har vi en fil?  
if(is_uploaded_file($_FILES['myFile']['tmp_name']))  {    //Filendelse    
$extension = strtolower(pathinfo($_FILES['myFile']['name'],PATHINFO_EXTENSION));        
//Er filen en af de filer vi gerne vil have?    
if(in_array($extension,$allowedFileTypes))    {      
//Vi flytter filen fra tmp til vores UPLOADDIR      

$uploadfile = $uploadDir . $bruger . "." . array_pop( explode( '.', $_FILES['myFile']['name'] ) );
if ($_FILES['myFile']['size'] < 102400 )
{

if (move_uploaded_file($_FILES['myFile']['tmp_name'], $uploadfile)) 
{     
$fil = $bruger . "." . array_pop( explode( '.', $_FILES['myFile']['name'] ) );
   
     mysql_query("UPDATE brugere SET profil_billede='$fil' WHERE brugernavn='$bruger'") or die(mysql_error());

	header("Location: profil.php?menu=$menu");

}      
else      
{ 
$_SESSION['ikke_uploadet'] = 1;	
	header("Location: redigere-foto.php?menu=$menu");

}  
}
else
{
$_SESSION['storrelse'] = 1;	
	header("Location: redigere-foto.php?menu=$menu");
}
}    
else    
{      
$_SESSION['fil_type'] = 1;	
	header("Location: redigere-foto.php?menu=$menu");
}  
}  
else  
{    
$_SESSION['ingen_fil'] = 1;	
	header("Location: redigere-foto.php?menu=$menu");
}}























?>
