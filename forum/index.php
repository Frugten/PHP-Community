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
if(empty ($_SESSION['aktiv_Forum']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Forum' AND aktiv = 'nej' AND admin='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Forum'] ="nej";
}
else{
$_SESSION['aktiv_Forum'] ="ja";
}
}

if($_SESSION['aktiv_Forum'] == "nej")
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
?>


<h1>Oversigt</h1>
<div>
<table width='100%'>
<?php
$get = mysql_query("SELECT laston FROM brugere WHERE Brugernavn = '$bruger' LIMIT 1") or die(mysql_error()); // henter Brugernavn som er 1?
$show = mysql_fetch_array($get);
$laston = $show['laston'];
$laston = strtotime("$laston"); 

$grupper = mysql_query("SELECT grID, titel FROM forumgr WHERE gruppe='hoved' ORDER BY visning ASC") or die(mysql_error());
while ( $b = mysql_fetch_array($grupper))
{
$id = $b['grID'];
$titel = $b['titel'];
$titel =stripslashes($titel);

echo"<tr><td class='overskrift'><b>$titel</b></td><td class='overskrift'>Antal</td><td class='overskrift'>Ny</td></tr>";

$ugrupper = mysql_query("SELECT grID, titel, beskrivelse FROM forumgr WHERE gruppe='under' AND under='$id' ORDER BY visning ASC") or die(mysql_error());
while ( $c = mysql_fetch_array($ugrupper))
{
$uid = $c['grID'];
$utitel = $c['titel'];
$ubeskrivelse = $c['beskrivelse'];

$utitel =stripslashes($utitel);
$ubeskrivelse =stripslashes($ubeskrivelse);

$resultat = mysql_query("SELECT dato FROM forumtraad WHERE grid = '$uid' ORDER BY traadID DESC LIMIT 1");//Spørger efter ID
$show = mysql_fetch_array($resultat);
$godato = $show['dato'];

$tael = mysql_query("SELECT COUNT(*) AS antal FROM forumtraad WHERE grid= '$uid' AND spg ='0'") or die(mysql_error());
$row = mysql_fetch_array($tael);
$antallet = $row['antal'];

$odato = strtotime("$godato"); 

if ($antallet == 0)
{
$ny ="<td width='7%' class='kant'>&nbsp;</td>";
}
else if ($laston > $odato)
{
$ny ="<td width='7%' class='kant'>&nbsp;</td>";
}
else if ($laston < $odato)
{
$ny ="<td width='7%' class='overskrift'>&nbsp;</td>";
}
else
{
$ggrupper = mysql_query("SELECT dato FROM forumtraad WHERE grid='$id' AND parent > '0' ORDER BY traadID DESC LIMIT 1") or die(mysql_error());
while ( $q = mysql_fetch_array($ggrupper))
{
$tdato = $q['dato'];
if ($laston < $tdato)
{
$ny ="<td width='7%' class='overskrift'>&nbsp;</td>";
}
else
{
$ny ="<td width='7%' class='kant'>&nbsp;</td>";
}
}
}

echo"<tr><td class='kant'><a href='$side/forum/visforum.php?menu=$menu&gr=$uid'>$utitel</a>";
echo"<br>$ubeskrivelse</td><td class='kant'>$antallet</td>";
echo"$ny</tr>";
}



}
?>
</table>
</div>	

<?php
include 'footer.php';
?>

</body>

</html>