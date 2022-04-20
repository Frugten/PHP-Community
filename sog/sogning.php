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

$sog = mysql_real_escape_string($_GET[sog]);

if($sog == "ja")
{
header("Location: $side/sog/sogning.php?menu=$menu");
$soge_ord = mysql_real_escape_string($_REQUEST["sog_ord"]);
$_SESSION['sog_ord'] = $soge_ord;
}

$sideinfo = $_SERVER["QUERY_STRING"];

if($_SESSION['soge_page'] != "?$sideinfo")
{
$_SESSION['soge_page'] = "?$sideinfo";
}


//Al din kode herunder

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
<title>Søgning</title>
<meta name="description" content="søgning">
<META name="keywords" content="">
<?
include 'head.php';
?>
</head>
<body>
<?
include 'header.php';
?>
<? 
$sog_ord = $_SESSION['sog_ord'];

echo"<h1>Søge resultat</h1>";
if($menu == "Bruger") 
{
include '../sog/bruger-sog.php';
}
if($menu == "Forum" )
{
include '../sog/forum-sog.php';
}
if($menu == "Artikler") 
{
include '../sog/artikel-sog.php';
}
if($menu == "Kalender") 
{
include '../sog/kalender-sog.php';
}
if($menu == "Galleri") 
{
include '../sog/galleri-sog.php';
}

?>

<?
include 'footer.php';
?>
</body>

</html>