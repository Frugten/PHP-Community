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
exit;//S�rger for at resten af koden, ikke bliver udf�rt
}
$_SESSION['menu'] = "bruger";
//Al din kode herunder


$_SESSION['sogning'] = "brugerliste";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title></title>
<meta name="Description" content="">
<meta name="Keywords" content="">
<?php
include 'head.php';
?>
</head>
<body>
<?php
include 'header.php';
?>
<h1>Mails</h1>
<h2>Her kan du v�lge hvilke mails du vil modtage. Udfyld formularen og tryk p� opdater</h2>

<?php
if($_SESSION['opdate'] == 1)
{
echo"<div class='farvet'>Dine informationer er nu opdateret</div>";
$_SESSION['opdate'] =0;
}
echo"<form name='moduler' method='POST' ACTION='opdatere-mails.php?menu=$menu'>";

$resultat = mysql_query("SELECT abb FROM mail_abb WHERE bruger = '$bruger' AND gruppe = 'nyhedsbrev'");//Sp�rger efter ID
$number = mysql_num_rows($resultat);//T�ller antaller af resultater
if($number == 1)
{
echo "<input type='checkbox' name='mails[]' checked value='nyhedsbrev'> ";
}
else
{
echo "<input type='checkbox' name='mails[]' value='nyhedsbrev'> ";
}
echo"Nyhedsbrev (Vil du modtage nyhedsbrevet fra denne side)<br>";

$hmenu = mysql_query("SELECT titel FROM menu WHERE parent='0' AND aktiv='ja' AND admin='brugermenu'") or die(mysql_error());
while ( $menupunkt = mysql_fetch_array($hmenu))
{
$titel = $menupunkt[titel];

if ($titel == 'Forum')
{
$resultat = mysql_query("SELECT abb FROM mail_abb WHERE bruger = '$bruger' AND gruppe = 'forum'");//Sp�rger efter ID
$number = mysql_num_rows($resultat);//T�ller antaller af resultater
if($number == 1)
{
echo "<input type='checkbox' name='mails[]' checked value='forum'> ";
}
else
{
echo "<input type='checkbox' name='mails[]' value='forum'> ";
}
echo"Forum (Vil du som standard tilmeldes forum tr�de du opretter eller svare p�)<br>";
}

if ($titel == 'Post')
{
$resultat = mysql_query("SELECT abb FROM mail_abb WHERE bruger = '$bruger' AND gruppe = 'intern_mail'");//Sp�rger efter ID
$number = mysql_num_rows($resultat);//T�ller antaller af resultater
if($number == 1)
{
echo "<input type='checkbox' name='mails[]' checked value='intern_mail'> ";
}
else
{
echo "<input type='checkbox' name='mails[]' value='intern_mail'> ";
}
echo"Intern mail (Vil du modtage en mail n�r en bruger sender en intern mail til dig)<br>";
}
}

$gresultat = mysql_query("SELECT abb FROM mail_abb WHERE bruger = '$bruger' AND gruppe = 'gaestebog'");//Sp�rger efter ID
$gnumber = mysql_num_rows($gresultat);//T�ller antaller af resultater
if($gnumber == 1)
{
echo "<input type='checkbox' name='mails[]' checked value='gaestebog'> ";
}
else
{
echo "<input type='checkbox' name='mails[]' value='gaestebog'> ";
}
echo"G�stebog (Vil du modtage en mail n�r en bruger skriver en besked i dine g�stebog)<br>";

?>
<br>
<input class='inputknap' name="send" type='submit' value='Opdater'>
</form>

<?php
include 'footer.php';
?>
</body>

</html>