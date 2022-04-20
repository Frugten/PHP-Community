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
//tjek om modul er aktiv
if(empty ($_SESSION['aktiv_Post']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Post' AND aktiv = 'nej' AND admin='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Post'] ="nej";
}
else{
$_SESSION['aktiv_Post'] ="ja";
}
}

if($_SESSION['aktiv_Post'] == "nej")
{
$_SESSION['ikke_aktiv_modul'] = "nej";
header("Location: $side");
}
//tjek slut
//Al din kode herunder

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title></title>
<meta name="Description" content="">
<meta name="Keywords" content="">
<?
include 'head.php';
?>
</head>
<body>
<?
include 'header.php';
?>
<h1>Indbakke</h1>
<?
//hvor mange pr. side
if(!empty ($_SESSION['post_indlaeg']))
{
$pr_side = $_SESSION['post_indlaeg'];
}
else
{
$indlag = mysql_query ("SELECT indlaeg FROM indlaeg_settings WHERE titel ='post-indlaeg'") or die(mysql_error());
while ($vis = mysql_fetch_array($indlag)) 
{
$vaerdi = $vis[indlaeg];
$_SESSION['post_indlaeg'] = $vaerdi;
$pr_side = $_SESSION['post_indlaeg'];
}
}
//hvor mange pr. side slut


if ($_SESSION['besvaret'] == 1)
{
echo"<b><div class='farvet'>Din mail er nu sendt</div></b>";
$_SESSION['besvaret'] = 0;
}

$nquery = mysql_query ("SELECT * FROM mail_ind WHERE til = '$bruger'") or die(mysql_error());
        $antal = mysql_num_rows($nquery);//Tæller antaller af resultater

$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal) ? $_GET["visfra"] : 0;

echo"<table width='100%'><tr>";
echo"<td>";
$vis = $_GET["visfra"];
if(!empty($vis))
{
$vise = $vis+1;
}
else if ($antal > 0)
{
$vise = $vis+1;
}
else
{
$vise = 0;
}

if ($vis+$pr_side > $antal)
{
$plus = $antal;
}
else
{
$plus = $vis+$pr_side;
}
echo"<h2>Viser mail $vise - $plus af $antal</h2>";
echo"</td>";
echo"<td>";
echo"</td>";
echo"</tr></table>";

echo"<table>";
echo"<tr><td class='kant'><b>Emne</b></td>";
echo"<td class='kant'><b>Fra</b></td>";
echo"<td class='kant'><b>Modtaget</b></td>";
echo"<td class='kant'><b>(U)læst</b></td>";
echo"<td class='kant'><b>Slet</b></td></tr>";


$query = mysql_query ("SELECT *, DATE_FORMAT(sendt, '%d-%m-%Y') AS dato, DATE_FORMAT(sendt, '%H:%i:%s') AS tid FROM mail_ind WHERE til = '$bruger' ORDER BY sendt DESC limit $vis_fra, $pr_side") or die(mysql_error());
while ($vis = mysql_fetch_array($query)) 
{
$id = $vis[indid];
$fra = $vis[fra];
$emne = $vis[emne];
$dato = $vis[dato];
$tid = $vis[tid];
$laest = $vis[laest];

if ($laest == 'nej')
{
$nfra ="<b>$fra</b>";
$nemne ="<b>$emne</b>";
$ndato ="<b>$dato</b>";
$ntid ="<b>$tid</b>";
$laese ="<b><a href='ulaest.php?menu=$menu&id=$id'>ULæst</a></b>";
$slet ="<b><a href='slet-ind.php?menu=$menu&id=$id'>Slet</a></b>";
}
else
{
$nfra ="$fra";
$nemne ="$emne";
$ndato ="$dato";
$ntid ="$tid";
$laese ="<a href='ulaest.php?menu=$menu&id=$id'>Læst</a>";
$slet ="<a href='slet-ind.php?menu=$menu&id=$id'>Slet</a>";
}

echo"<tr><td class='kant'><a href='laes-mail.php?menu=$menu&id=$id'>$nemne</a></td>";
echo"<td class='kant'>$nfra</td>";
echo"<td class='kant'>$ndato $ntid</td>";
echo"<td class='kant'>$laese</td>";
echo"<td class='kant'>$slet</td></tr>";

}
echo"</table>";
echo "<hr />";

if ($vis_fra > 0) {
$back= $vis_fra - $pr_side;
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&visfra=$back'>Forrige</a> ";
}
$page = 1;

for ($start = 0; $antal > $start; $start = $start + $pr_side) {
if($vis_fra != $page * $pr_side - $pr_side) {
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&visfra=$start'>$page</a> ";
} else {
echo $page." ";
}
$page++;
}

if ($vis_fra < $antal - $pr_side) {
$next = $vis_fra + $pr_side;
echo " <a href='$_SERVER[PHP_SELF]?menu=$menu&visfra=$next'>Næste</a>";
}

?>

<?
include 'footer.php';
?>
</body>

</html>