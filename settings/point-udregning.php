<?
//udregner hvor mange point brugeren skal have
$perio = mysql_query("SELECT * FROM settings WHERE tekst = 'periode-points'") or die(mysql_error());
$pe = mysql_fetch_array($perio);

$periodepoints = $pe[vaerdi];

$brug = mysql_query("SELECT ontid, logget_ind, point FROM brugere WHERE brugernavn ='$bruger'") or die(mysql_error());
$b = mysql_fetch_array($brug);

$logget_ind = $b[logget_ind];
$ontid = $b[ontid];
$point = $b[point];

$ontid = strtotime("$ontid"); 
$logget_ind = strtotime("$logget_ind"); 

$sekunder = $ontid - $logget_ind; 
$time = ($sekunder / 60) / 60;

$udregnet_point = $time * $periodepoints;
$points = round($udregnet_point, 2);
$points = $point + $points;

mysql_query("UPDATE brugere SET point ='$points' WHERE brugernavn='$bruger'") or die(mysql_error());

//udregning og opdatering er slut
?>
