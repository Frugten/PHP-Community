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

//sletter event som er et år gamle samt sletter dem fra favorit listen
$nquery = mysql_query ("SELECT eventID FROM kalender WHERE DATE_ADD(til, INTERVAL 1 YEAR) < NOW()") or die(mysql_error());
while ( $a = mysql_fetch_array($nquery))
{
$eventid = $a[eventID];
mysql_query("DELETE FROM favorit WHERE id='$eventid' AND gruppe ='Kalender'") or die(mysql_error());

mysql_query("DELETE FROM kalender WHERE DATE_ADD(til, INTERVAL 1 YEAR) < NOW()") or die(mysql_error());
}
//slet slut

//tjek om modul er aktiv
if(empty ($_SESSION['aktiv_Kalender']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Kelnder' AND aktiv = 'nej' AND admin='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Kalender'] ="nej";
}
else{
$_SESSION['aktiv_Kalender'] ="ja";
}
}

if($_SESSION['aktiv_Kalender'] == "nej")
{
$_SESSION['ikke_aktiv_modul'] = "nej";
header("Location: $side");
}
//tjek slut

//Al din kode herunder

    setlocale( LC_TIME, 'da_DK' ); 

    $today = time(); 
    if ( !empty( $_GET['m'] ) && !empty( $_GET['y'] ) && is_numeric( $_GET['m'] ) && is_numeric( $_GET['y'] ) ) 
    { 
        $today = mktime( 0, 0, 0, $_GET['m'], 1, $_GET['y'] ); 
    } 

    $mname = ucfirst( strftime( '%B', $today ) ); 
    $thism = date( 'm', $today ); 
    $thisy = date( 'Y', $today ); 
    $startd = mktime( 0, 0, 0, $thism, 1, $thisy ); 
    $endd = mktime( 23, 59, 59, $thism + 1, 0, $thisy ); 
    $nodays = date( 'd', $endd ); 
    $startwd = strftime( '%u', $startd ); 
    $endwd = strftime( '%u', $endd ); 
    $startw = strftime( '%V', $startd ); 
    $endw = strftime( '%V', $endd ); 
    $startpad = ( $startwd == 1 ? 0 : $startwd - 1 ); 
    $realstart = mktime( 0, 0, 0, $thism, -$startpad + 1, $thisy ); 
    $startpadreal = date( 'd', $realstart ); 
    $endpad = ( $endwd == 7 ? 0 : 7 - $endwd ); 
    $realend = strtotime( '+' . $endpad . ' days', $endd ); 

    $lastm = date( 'm', strtotime( '-1 month', $today ) ); 
    $lastmy = date( 'Y', strtotime( '-1 month', $today ) ); 
    $nextm = date( 'm', strtotime( '+1 month', $today ) ); 
    $nextmy = date( 'Y', strtotime( '+1 month', $today ) ); 

    $days = array(); 
    $w = $startw; 
    $n = 0; 
    if ( $startpad > 0 ) 
    { 
        for ( $i = $startpadreal; $i < $startpadreal + $startpad; $i++ ) 
        { 
            $days[$w][] = $i; 
            $n++; 
        } 
    } 
    for ( $i = 1; $i <= $nodays; $i++ ) 
    { 
        $n %= 7; 
        if ( $n == 0 ) 
        { 
            $w = strftime( '%V', mktime( 0, 0, 0, $thism, $i, $thisy ) ); 
        } 
        $days[$w][] = $i; 
        $n++; 
    } 
    if ( $endpad > 0 ) 
    { 
        for ( $i = 1; $i <= $endpad; $i++ ) 
        { 
            $days[$endw][] = $i; 
        } 
    } 
   

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
        <h1><?= $mname . ', ' . $thisy ?></h1> 
<?
$de_maa =  date("m", $today);    
$nnquery = mysql_query ("SELECT eventID FROM kalender WHERE YEAR(fra) = '$thisy' AND MONTH(fra) = '$de_maa'") or die(mysql_error());
$antal_maa_event = mysql_num_rows($nnquery);//Tæller antaller af resultater
echo"<h2>Der er tilføjet $antal_maa_event events i $mname</h2>";
?>
        <table id="cal" width="760px" border="1" cellspacing="0" cellpadding="0"> 
            <tr> 
                <?
$denne_side = $_SERVER['PHP_SELF'];
$naeste_aar = $lastmy + 1;
$forrige_aar = $lastmy - 1;
echo"<td class='overskrift'>";
echo"<a title='Forrige år' href='$denne_side?menu=$menu&y=$forrige_aar&m=$thism'>$forrige_aar</a>";
echo"</td><td class='overskrift' colspan='6'><p align='center'>";
echo"| <a title='Januar' href='$denne_side?menu=$menu&y=$lastmy&m=01'>Jan</a> | ";
echo"<a title='Februar' href='$denne_side?menu=$menu&y=$lastmy&m=02'>Feb</a> | ";
echo"<a title='Marts' href='$denne_side?menu=$menu&y=$lastmy&m=03'>Mar</a> | ";
echo"<a title='April' href='$denne_side?menu=$menu&y=$lastmy&m=04'>Apr</a> | ";
echo"<a title='Maj' href='$denne_side?menu=$menu&y=$lastmy&m=05'>Maj</a> | ";
echo"<a title='Juni' href='$denne_side?menu=$menu&y=$lastmy&m=06'>Jun</a> | ";
echo"<a title='Juli' href='$denne_side?menu=$menu&y=$lastmy&m=07'>Jul</a> | ";
echo"<a title='August' href='$denne_side?menu=$menu&y=$lastmy&m=08'>Aug</a> | ";
echo"<a title='September' href='$denne_side?menu=$menu&y=$lastmy&m=09'>Sep</a> | ";
echo"<a title='Oktober' href='$denne_side?menu=$menu&y=$lastmy&m=10'>Okt</a> | ";
echo"<a title='November' href='$denne_side?menu=$menu&y=$lastmy&m=11'>Nov</a> | ";
echo"<a title='December' href='$denne_side?menu=$menu&y=$lastmy&m=12'>Dec</a> | ";
echo"</p>";
echo"</td><td class='overskrift'>";
echo"<a title='Næste år' href='$denne_side?menu=$menu&y=$naeste_aar&m=$thism'>$naeste_aar</a>";
?>
                </td> 
            </tr> 
            <tr> 
                <th width="5%">Uge</th> 
                <th width="12%">Man</th> 
                <th width="12%">Tir</th> 
                <th width="12%">Ons</th> 
                <th width="12%">Tor</th> 
                <th width="12%">Fre</th> 
                <th width="12%">Lør</th> 
                <th width="12%">Søn</th> 
            </tr> 
<?php 

    foreach ( $days as $wk => $dayar ) 
    { 
        echo "<tr><td class=\"wkn\">$wk</td>"; 
        for ( $d = 0; $d < 7; $d++ ) 
        { 
            $c = ''; 
            if ( $d == 5 ) 
            { 
                $c .= ' satd'; 
            } 
            elseif ( $d == 6 ) 
            { 
                $c .= ' sund'; 
            } 
            $tm = $thism; 
            $ty = $thisy; 
            if ( ( $wk == $startw && $dayar[$d] > 10 ) || 
                 ( $wk == $endw && $dayar[$d] < 10 ) ) 
            { 
                $c .= ' notnow'; 
                if ( $dayar[$d] > 10 ) 
                { 
                    $tm = $lastm; 
                } 
                else 
                { 
                    $tm = $nextm; 
                } 
                if ( $thism == 1 && $dayar[$d] > 10 ) 
                { 
                    $ty = $thisy - 1; 
                } 
                else if ( $thism == 12 && $dayar[$d] < 10 ) 
                { 
                    $ty = $thisy + 1; 
                } 
            } 
            $tstamp = mktime( 0, 0, 0, $tm, $dayar[$d], $ty ); 
            if ( $c != '' ) 
            { 
                $c = ' class="' . $c . '"'; 
            } 
            $td = sprintf( '%02d', $dayar[$d] ); 
            $typ = ( $d > 4 ? 'dwecnt' : 'dcnt' ); 
            $cb = ''; 
            printf( "<td$c><div class='%s'><div class='date'>%d%s</div>", $typ, $td, $cb ); 
            $dagen = date("d-m-Y", $tstamp); 
			$for_dato = date("d", $tstamp); 
			$for_maaned = date("m", $tstamp); 
			$for_aar = date("Y", $tstamp); 
$nquery = mysql_query ("SELECT eventID FROM kalender WHERE YEAR(fra) = '$for_aar' AND MONTH(fra) = '$for_maaned' AND DAY(fra) = '$for_dato'") or die(mysql_error());
$antal_event = mysql_num_rows($nquery);//Tæller antaller af resultater

$lige_nu = date("Y/m/d");
$ligenu_sek = strtotime("$lige_nu"); 

if($ligenu_sek <= $tstamp)
{
            echo"<a href='tilfoj.php?menu=$menu&dag=$dagen'>Tilføj</a><br>";
            echo"<a href='vis.php?menu=$menu&dag=$tstamp'>Vis ($antal_event)</a>";
}
else
{
            echo"<a href='vis.php?menu=$menu&dag=$tstamp'>Vis ($antal_event)</a>";
}
            echo '</div></td>'; 
        } 
        echo "</tr>\n"; 
    } 
     
?> 
        </table> 
<?
include 'footer.php';
?>
</body>

</html>