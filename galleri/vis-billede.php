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
//tjek om modul er aktiv
if(empty ($_SESSION['aktiv_Galleri']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Galleri' AND aktiv = 'nej' AND admin='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Galleri'] ="nej";
}
else{
$_SESSION['aktiv_Galleri'] ="ja";
}
}

if($_SESSION['aktiv_Galleri'] == "nej")
{
$_SESSION['ikke_aktiv_modul'] = "nej";
header("Location: $side");
}
//tjek slut

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
<?php
$idet = mysql_real_escape_string($_GET['id']);
$gr = mysql_real_escape_string($_GET['gr']);
if ($gruppe =="admin")
{
echo"| <a href='$side/admin/galleri/slet-billede.php?menu=$menu&gr=$gr&id=$idet'>Slet billede</a> | ";
echo" <a href='$side/admin/galleri/flyt-billede.php?menu=$menu&gr=$gr&id=$idet'>Flyt billede</a> | ";
echo"<hr>";
}
?>
<?php
$artikler = mysql_query("SELECT * ,DATE_FORMAT(dato, '%d-%m-%Y') AS datoen FROM galleri WHERE billed_id = '$idet'");
while ($d = mysql_fetch_assoc($artikler))
{
$id = $d[billed_id];
$billede = $d[billede];
$titel = $d[titel];
$info = $d[info];
$brugernavn = $d[bruger];
$dato = $d[datoen];

$titel = htmlentities($titel);
$titel =stripslashes($titel);

$bredde = $d[bredde];
$hojde = $d[hojde];

$info = htmlentities($info);
$info = nl2br("$info");
$info =stripslashes($info);

echo"<h1>$titel</h1>";
echo"<table width='100%'><tr><td>";
echo"Tilføjet af $brugernavn den. $dato<br>";
echo"Størrelsen på billedet er: $bredde x $hojde (bredde x højde)<br>";
include '../favorit/tjek-favoritliste.php';
echo"</td><td>";
include '../rate/stem.php';
echo"</td></tr></table>";

echo"<hr>";
echo "<table><tr><td>";
if($bredde > $hojde)
{
if($bredde < 500 && $hojde < 400)
{
echo"<img border='0' align='left' src='$side/galleri/billeder/$billede'>";
}
else
{
echo"<img border='0' align='left' src='$side/galleri/billeder/$billede' width='500' height='400'>";
}
}
if($bredde < $hojde)
{
if($bredde < 300 && $hojde < 400)
{
echo"<img border='0' align='left' src='$side/galleri/billeder/$billede'>";
}
else
{
echo"<img border='0' align='left' src='$side/galleri/billeder/$billede' width='300' height='400'>";
}
}


echo"</td></tr></table><br>";
echo"<hr>";
}

include '../kommentar/vis-kommentare.php';

include '../kommentar/form.php';

?>
</p>
<?php
include 'footer.php';
?>
</body>

</html>
