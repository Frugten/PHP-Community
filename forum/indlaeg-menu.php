<?php
if ($lukket =="nej")
{
echo"<a href='citer.php?menu=$menu&id=$id&traad=$traad&gr=$gr&visfra=$vis_fra'>Citer indlæg</a><br><br>";
}

$nanmeld = mysql_query("SELECT anmeldt FROM forumtraad WHERE traadID = '$id'") or die(mysql_error());
while ( $g = mysql_fetch_array($nanmeld))
{
$anmeldt = $g['anmeldt'];
if ($anmeldt == 0)
{
echo"<a href='anmeld.php?menu=$menu&id=$id&traad=$traad&gr=$gr'>Anmeld indlæg</a>";
}
else if ($anmeldt == 1)
{
echo "Tråd er anmeldt til admin";
}
else if ($anmeldt == 2)
{
echo "admin har markeret dette indlæg som ok";
}
}
echo"<br>";
?>