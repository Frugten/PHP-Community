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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
<title>Redigere profil</title>
<meta name="description" content="Ret din profil">
<META name="keywords" content="">
<?php
include 'head.php';
?>
</head>
<body>
<?php
include 'header.php';
?>

		<h1>Upload profil billede</h1>
<ul>
	<li>Filen må maks fylde 100Kb</li>
	<li>Tilladte filtyper er (jpg, gif, png)</li>
</ul>
<div class='farvet'>
<?php
if ($_SESSION['ikke_uploadet'] == 1)
{
echo"Filen blev ikke uploadet";
$_SESSION['ikke_uploadet'] = 0;
}
if ($_SESSION['storrelse'] == 1)
{
echo"Filen Må maks fylde 100 Kb";
$_SESSION['storrelse'] = 0;
}
if ($_SESSION['fil_type'] == 1)
{
echo"Filtypen er ikke tilladt";
$_SESSION['fil_type'] = 0;
}
if ($_SESSION['ingen_fil'] == 1)
{
echo"Du skal vælge en fil";
$_SESSION['ingen_fil'] = 0;
}

echo"<form action='ret-foto.php?menu=$menu' method='post' enctype='multipart/form-data'>";
?>	
<table><tr>
<td valign="top"><input type="file" name="myFile"></td> 
</tr></table>
  <input type="submit" name="Submit" value="Upload">
 </form>
<br>
</div>		
<?php
include 'footer.php';
?>
</body>

</html>