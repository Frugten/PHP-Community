<?
if (!empty($sog_ord))
{
//tjekker om der kan sendes interne mails rundt til brugere
if(empty ($_SESSION['aktiv_post']))
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Post' AND aktiv = 'ja' AND admin ='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_post'] ="ja";
}
else{
$_SESSION['aktiv_post'] ="nej";
}
}
$dagen_idag = date("d-m");

//hvor mange pr. side
if(!empty ($_SESSION['bruger_indlaeg']))
{
$pr_side = $_SESSION['bruger_indlaeg'];
}
else
{
$indlag = mysql_query ("SELECT indlaeg FROM indlaeg_settings WHERE titel ='bruger-indlaeg'") or die(mysql_error());
while ($vis = mysql_fetch_array($indlag)) 
{
$vaerdi = $vis[indlaeg];
$_SESSION['bruger_indlaeg'] = $vaerdi;
$pr_side = $_SESSION['bruger_indlaeg'];
}
}
//hvor mange pr. side slut


$nquery = mysql_query ("SELECT brugerid FROM brugere WHERE brugernavn LIKE '%$sog_ord%'") or die(mysql_error());
        $antal = mysql_num_rows($nquery);//Tæller antaller af resultater
if($antal != 0)
{        
echo"Der blev fundet $antal resultater<br><br>"; 
$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal) ? $_GET["visfra"] : 0;

echo"<table>";
$query = mysql_query ("SELECT brugerid, brugernavn, DATE_FORMAT(birth, '%d-%m') AS fodsel, online, point FROM brugere WHERE brugernavn LIKE '%$sog_ord%' limit $vis_fra, $pr_side") or die(mysql_error());
while ($vis = mysql_fetch_array($query)) 
{
$classen = ($classen=='overskrift' ? '' : 'overskrift');

$brugerid = $vis[brugerid];
$brugernavn = $vis[brugernavn];
$fodsel = $vis[fodsel];
$online = $vis[online];
$point = $vis[point];
$point = floor($point);

if ($online == 'ja')
{
$on ="<font class='online'>online</font>";
}
else
{
$on ="<font class='offline'>offline</font>";
}
if($vis_fra == 0 && $nr == 0)
{
$nr = 1;
}
else if ($nr >= $vis_fra)
{
$nr++;
}
else
{
$nr = $vis_fra;
}
if($vis_fra == $nr)
{
$nr = $nr + 1;
}
if ($brugernavn == $bruger)
{
$nbrugernavn ="<b>$brugernavn</b>";
}
else
{
$nbrugernavn ="<a href='$side/bruger/profil.php?menu=$menu&id=$brugerid'>$brugernavn</a>";
}
if ($dagen_idag == $fodsel)
{
$flag="<img border='0' width='50' src='$side/billeder/flag.jpg'>";
}
else
{
$flag="";
}

echo"<tr><td class='$classen'>";

echo"<table>";
echo "<tr><td class='$classen'>$nr</td><td class='$classen'>$nbrugernavn</td><td class='$classen'>$on</td></tr><tr>";
echo"<td class='$classen'></td><td class='$classen'>$point points</td>";
if($_SESSION['aktiv_post'] =="ja" && $brugernavn != $bruger)
{
echo"<td class='$classen'><a href='$side/intern-mail/send-ny.php?menu=$menu&bruger=$brugerid'>Send mail</a></td>";
}
else
{
echo"<td class='$classen'></td>";
}
echo"</tr></table>";
echo"</td><td class='$classen'>$flag</td></tr>";
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
}
else
{
echo"Der blev ikke fundet nogen resultater";
}
}
else
{
echo"Der blev ikke fundet nogen resultater";
}

?>