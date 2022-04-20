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
if(empty ($_SESSION['aktiv_Artikler']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Artikler' AND aktiv = 'nej' AND admin='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Artikler'] ="nej";
}
else{
$_SESSION['aktiv_Artikler'] ="ja";
}
}

if($_SESSION['aktiv_Artikler'] == "nej")
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
<title>oversigt</title>
<meta name="description" content="forum oversigt">
<META name="keywords" content="">
<?php
include 'head.php';
?>
</head>

<body>

<?php
include 'header.php';

$handling = mysql_real_escape_string($_GET['handling']);
echo"<div class='farvet'>";
if ($handling =="slet")
{
echo"For at slette en artikel skal du finde den her i oversigten og trykke på linket som kommer frem her i toppen";
}
if ($handling =="flyt")
{
echo"For at flytte en artikel til en anden kategori skal du finde den her i oversigten og trykke på linket som kommer frem her i toppen";
}
echo"</div>";
?>

<h1>Oversigt</h1>
<div>
<table width='100%'>
<?php
$grupper = mysql_query("SELECT grID, titel, beskrivelse FROM artikelgr ORDER BY visning ASC") or die(mysql_error());
while ( $b = mysql_fetch_array($grupper))
{
$id = $b['grID'];
$titel = $b['titel'];
$beskrivelse = $b['beskrivelse'];

$titel =stripslashes($titel);
$beskrivelse =stripslashes($beskrivelse);

$tael = mysql_query("SELECT COUNT(*) AS antal FROM artikel WHERE grid= '$id' AND aktiv ='ja'") or die(mysql_error());
$row = mysql_fetch_array($tael);
$antallet = $row['antal'];

echo"<tr><td class='kant'><a href='vis-alle.php?menu=$menu&gr=$id'><b>$titel</b></a><br>";
echo"$beskrivelse</td><td class='kant'>$antallet</td></tr>";
}

?>
</table>
</div>	

<?php
include 'footer.php';
?>

</body>

</html>