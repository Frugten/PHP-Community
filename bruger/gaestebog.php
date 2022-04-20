<?php

function url2link($txt)
    {
        $username = "[a-z0-9_\-]+";
        $password = "[a-z0-9_\-!#]+";
        $host = "[a-z0-9\-]+\.[a-z0-9\-\.]+";
        $port = "\d{1,5}";
        $path = "\/[a-z0-9\/\-_\.\(\)\%#]*";
        $querystr = "\?[a-z0-9&=\-_\.%\(\)#]+";
        $proto = "(https?|ftp):\/\/";
        $url = "$host(:$port)?($path($querystr)?)?";
        $protodomain = "/$proto(($username(:$password)?@)?$url)/i";
        $domain = "/(^| )(www\.$url)/im";
        $protomail = "/mailto:($username@$host)/i";
        $mail = "/(^| )($username@$host)/im";
        $replacements = array(
                $protodomain => "<a href=\"\\0\" target=\"_blank\">\\2</a>"
                ,$domain => "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>"
                ,$protomail => "<a href=\"\\0\">\\1</a>"
                ,$mail => "\\1<a href=\"mailto:\\2\">\\2</a>"
            );
        return preg_replace( array_keys( $replacements ), array_values( $replacements ), $txt );
    }
//funktion slut    


$id = mysql_real_escape_string($_GET["id"]);

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


//Hvis $id er sat, skal vi hente fra brugere med det id
if(!empty($id))
{
$resultat = mysql_query("SELECT brugerid FROM brugere WHERE brugerid = '$id'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater

$hent = mysql_query("SELECT brugernavn, privat FROM brugere WHERE brugerid = '$id'") or die(mysql_error());
$show = mysql_fetch_array($hent);
$brugernavn = $show[brugernavn];
$privat = $show[privat];

$gresultat = mysql_query("SELECT venid FROM vennelister WHERE bruger = '$brugernavn' AND ven='$bruger'");//Spørger efter ID
$gnumber = mysql_num_rows($gresultat);//Tæller antaller af resultater

if($gruppe == "admin"){
$vis_profil = "ja";
}
else if ($privat == "nej"){
$vis_profil = "ja";
}
else if ($privat == "ja" && $gnumber == 1){
$vis_profil = "ja";
}
else{
$vis_profil = "nej";
}

if ($number == 1 && $vis_profil =="ja")
{
echo"<hr><h1><a name='svar'>Gæstebog for $brugernavn</a></h1>";

if ($number == 1 && $vis_profil =="ja")
{
//bruges når formularen skal sendes
$brugerid = $id;

include 'skriv-gaeste.php';
}


$nquery = mysql_query ("SELECT * FROM gaestbog WHERE brugerbog = '$brugernavn'") or die(mysql_error());
        $antal = mysql_num_rows($nquery);//Tæller antaller af resultater
$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal) ? $_GET["visfra"] : 0;
echo"<table width='100%'>";
$query = mysql_query ("SELECT bruger,DATE_FORMAT(dato, '%d-%m-%Y') AS datoen, DATE_FORMAT(dato, '%H:%i:%s') AS tiden, besked FROM gaestbog WHERE brugerbog = '$brugernavn' ORDER BY gaestbogid DESC limit $vis_fra, $pr_side") or die(mysql_error());
while ($vis = mysql_fetch_array($query)) 
{
$classen = ($classen=='overskrift' ? '' : 'overskrift');

$brugeren = $vis[bruger];
$datoen = $vis[datoen];
$tiden = $vis[tiden];
$besked = $vis[besked];

$besked = htmlentities($besked);

//replace [b] og [/b] til <b> og </b> i $text
$ord_der_skal_erstattes = array("[b]","[/b]","[i]","[/i]","[u]","[/u]","[boks]","[/boks]"); 
$erstat_ord_med = array("<b>","</b>","<i>","</i>","<u>","</u>","<div class='boks_fed'>","</div>"); 
$besked = str_replace($ord_der_skal_erstattes, $erstat_ord_med, $besked); 
//erstat slut

$besked = nl2br("$besked");
$besked = url2link($besked);
$besked =stripslashes($besked);

//tjekker hvilke ord der ikke er tilladte og erstatter med andre
$ban = mysql_query("SELECT p_ord, g_ord FROM ban ORDER BY banid");
while($rs = mysql_fetch_array($ban))
{
  $bad[]= "/" . preg_quote( $rs['g_ord'], "/" ) . "/i";
  $good[] = $rs['p_ord'];
}
$besked = preg_replace( $bad, $good, $besked );
//tjek slut

//smileys insættes
$smil = mysql_query("SELECT tekst, billede FROM smiley ORDER BY smilid");
while($smi = mysql_fetch_array($smil))
{
  $besked = str_replace($smi['tekst'], "<img border='0' src='$side/smiley/".$smi['billede']."'>", $besked);

}
//smiley slut

$get = mysql_query("SELECT brugerid, profil_billede FROM brugere WHERE brugernavn = '$brugeren' LIMIT 1") or die(mysql_error()); // henter Brugernavn som er 1?
$show = mysql_fetch_array($get);
$brugerid = $show[brugerid];
$gb_billede = $show[profil_billede];

if ($gb_billede != "0")
{
$bille ="<img width='80' height='100' src='profil-billeder/$gb_billede' alt='Profil Billede'>";
}
else
{
$bille ="&nbsp;";
}

if ($brugeren == $bruger)
{
$brugeren ="<a href='profil.php?menu=$menu'>$brugeren</a>";
}
else
{
$brugeren ="<a href='profil.php?menu=$menu&id=$brugerid'>$brugeren</a>";
}
echo"<tr><td class='$classen'>";
echo"<table><tr><td>$bille";
echo"</td><td><b>$brugeren ";
echo"$datoen $tiden</b><br>$besked";
echo"</td></tr></table>";
echo"</td></tr>";
}
echo"</table>";


}
else
{
echo"$brugernavn har gjort sin profil privat<br>";

$vresultat = mysql_query("SELECT ven_ansogid FROM ven_ansog WHERE ven = '$bruger' AND bruger='$brugernavn'");//Spørger efter ID
$vnumber = mysql_num_rows($vresultat);//Tæller antaller af resultater
if($vnumber == 1)
{
echo"Du har ansøgt om at komme på vennelisten.<br>";
}
else
{
echo"<a href='ven-ansog.php?menu=$menu&id=$brugerid'>Ansøg om at komme på venneliste</a> og se brugerens profil<br>";
}
echo"<br><a href='venneliste.php?menu=$menu&id=$id'>Venneliste for $brugernavn</a>";
echo"<br><br>";
//er intern mail aktiv
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Post' AND aktiv = 'ja' AND admin ='brugermenu'");//Spørger efter ID
$mail_number = mysql_num_rows($resultat);//Tæller antaller af resultater
if ($mail_number == 1)
{
echo"<a href='$side/intern-mail/send-ny.php?menu=$menu&bruger=$brugerid'>Send intern mail til $brugernavn</a>";
}
}
}
else
{
echo"<h1>Din gæstebog</h1>";

$nquery = mysql_query ("SELECT * FROM gaestbog WHERE brugerbog = '$bruger'") or die(mysql_error());
        $antal = mysql_num_rows($nquery);//Tæller antaller af resultater
$vis_fra = (isset($_GET["visfra"]) && is_numeric($_GET["visfra"]) && $_GET["visfra"] < $antal) ? $_GET["visfra"] : 0;
echo"<table width='100%'>";
$query = mysql_query ("SELECT gaestbogid, bruger, DATE_FORMAT(dato, '%d-%m-%Y') AS datoen, DATE_FORMAT(dato, '%H:%i:%s') AS tiden, besked FROM gaestbog WHERE brugerbog = '$bruger' ORDER BY gaestbogid DESC limit $vis_fra, $pr_side") or die(mysql_error());
while ($vis = mysql_fetch_array($query)) 
{
$classen = ($classen=='overskrift' ? '' : 'overskrift');

$gid = $vis[gaestbogid];
$brugeren = $vis[bruger];
$datoen = $vis[datoen];
$tiden = $vis[tiden];
$besked = $vis[besked];
$besked = stripslashes($besked);


$get = mysql_query("SELECT brugerid, profil_billede FROM brugere WHERE brugernavn = '$brugeren' LIMIT 1") or die(mysql_error()); // henter Brugernavn som er 1?
$show = mysql_fetch_array($get);
$brugerid = $show[brugerid];
$gb_billede = $show[profil_billede];

if ($gb_billede != "0")
{
$bille ="<img width='80' height='100' src='profil-billeder/$gb_billede' alt='Profil Billede'>";
}
else
{
$bille ="&nbsp;";
}
if ($brugeren == $bruger)
{
$brugeren ="<a href='profil.php?menu=$menu'>$brugeren</a>";
}
else
{
$brugeren ="<a href='profil.php?menu=$menu&id=$brugerid'>$brugeren</a>";
}

echo"<tr><td class='$classen'>";
echo"<table><tr><td>$bille";
echo"</td><td><b><a href='profil.php?menu=$menu&id=$brugerid'>$brugeren</a> ";
echo"$datoen $tiden</b><br>$besked<br>";
echo"<a href='slet-gbog-besked.php?menu=$menu&id=$gid&visfra=$vis_fra'>Slet besked</a>";
echo"</td></tr></table>";
echo"</td></tr>";
}
echo"</table>";
}
echo"<hr>";
if ($vis_fra > 0) {
$back= $vis_fra - $pr_side;
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&visfra=$back&id=$id'>Forrige</a> ";
}
$page = 1;

for ($start = 0; $antal > $start; $start = $start + $pr_side) {
if($vis_fra != $page * $pr_side - $pr_side) {
echo "<a href='$_SERVER[PHP_SELF]?menu=$menu&visfra=$start&id=$id'>$page</a> ";
} else {
echo $page." ";
}
$page++;
}

if ($vis_fra < $antal - $pr_side) {
$next = $vis_fra + $pr_side;
echo " <a href='$_SERVER[PHP_SELF]?menu=$menu&visfra=$next&id=$id'>Næste</a>";
}

?>
