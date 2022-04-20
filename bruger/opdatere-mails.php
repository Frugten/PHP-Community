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
$_SESSION['menu'] = "bruger";
//Al din kode herunder

if (isset($_POST['mails'])) //Har brugeren sendt formularen? 
{ 
mysql_query("DELETE FROM mail_abb WHERE bruger = '$bruger'") or die(mysql_error());

    foreach($_POST['mails'] as $mails) //Kør igennem array'et 
    { 

    mysql_query("INSERT INTO mail_abb (bruger, gruppe)
    VALUES('$bruger', '$mails')") or die(mysql_error());


    } 
$_SESSION['opdate'] =1;
header("Location: modtag-mails.php?menu=$menu");
} 
else
{
$_SESSION['opdate'] =1;
mysql_query("DELETE FROM mail_abb WHERE bruger = '$bruger'") or die(mysql_error());
header("Location: modtag-mails.php?menu=$menu");
}

?>