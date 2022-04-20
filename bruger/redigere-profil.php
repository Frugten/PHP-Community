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
$_SESSION['menu'] = "profil";
//Al din kode herunder
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
//henter id for den bruger der er logget ind
$id = $_GET[id];
//Hvis $id er sat, skal vi hente fra brugere med det id
if(!empty($id))
{
echo "<h2>Du er ikke logget ind på denne bruger <a href='$side?menu=$menu'>klik her</a></h2>";
}
//Ellers skal vi hente id for den bruger der er logget ind
else 
{

$get = mysql_query("SELECT *, DATE_FORMAT(birth, '%d') AS dato, DATE_FORMAT(birth, '%m') AS maaned, DATE_FORMAT(birth, '%Y') AS aar FROM brugere WHERE brugernavn = '$bruger' LIMIT 1") or die(mysql_error()); // henter Brugernavn som er 1?
$show = mysql_fetch_array($get);

$bid = $show[Id];
$brugernavn = $show[Brugernavn];
$kunstnernavn = $show[kunstnernavn];
$tlf = $show[tlf];
$email = $show[email];
$city = $show[city];
$postnr = $show[postnr];
$kon = $show[kon];
$dato = $show[dato];
$maaned = $show[maaned];
$aar = $show[aar];
$birth = $dato-$maaned-$aar;
$web = $show[web];
$msn = $show[msn];
$interesser = $show[interesser];
$beskrivelse = $show[beskrivelse];
$billed = $show[billede];
$nyhed = $show[nyhed];
$profil_billede = $show[profil_billede];
$privat = $show[privat];

$idag = date("Y") . date("m") . date("d");
$birth = $aar . $maaned . $dato;
$alder = substr($idag - $birth,0,-4);


echo "<h1>Rediger din profil</h1><br>";
echo"<h2>Når du har rettet dit password bliver du logget ud og skal logge ind med det nye password</h2>";
echo"<div class='farvet'>";
if($_SESSION['gammel_ukorrekt'] == 1)
{
echo"Den gamle kode er ukkorrekt";
$_SESSION['gammel_ukorrekt'] = 0;
}
if($_SESSION['udfyld_felter'] == 1)
{
echo"Begge felter skal udfyldes";
$_SESSION['udfyld_felter'] = 0;
}
echo"</div>";

		echo "<form method='POST' name='kode' ACTION='ret-kode.php?menu=$menu'>";
        echo "<input type='hidden' name='spgid' value='$brugernavn'>";
        echo "<table><tr><td><p>Gammel password:</p></td>";
        echo " <td><input type='password' name='glpassword' size='45' maxlength='255'></td></tr><tr>";
        echo "<td>Ny password:</td><td><input type='password' name='nypassword' size='45' maxlength='255'></td>";
        echo "</tr></table><input class='inputknap' type='submit' value='Ændre password'></form>";

echo"<h2>Når du har rettet din email. modtager du en mail med et link du skal klikke på for at bekræfte din mail adresse</h2>";
 		echo "<form method='POST' name='mail' ACTION='ret-mail.php?menu=$menu'>";
        echo "<input type='hidden' name='spgid' value='$brugernavn'>";
        echo "<table><tr><td><p>Email:</p></td>";
        echo " <td><input type='text' name='email' value='$email' size='45' maxlength='255'></td></tr><tr>";
        echo "</tr></table><input class='inputknap' type='submit' value='Ændre email'></form>";
 
 		echo "<form method='POST' name='andet' ACTION='ret-profil.php?menu=$menu'>";
echo "<h2>Alle oplysninger herunder, er synlig for andre bruger. Du bestemmer selv, hvor mange oplysninger du vil give</h2>";
        echo "<table><tr>";
        echo "<td>Postnr:</td><td><input type='text' name='postnr' value='$postnr' size='6' maxlength='4'></td></tr><tr>";
echo"<td>By:</td><td><input type='text' name='city' value='$city' size='45' maxlength='255'></td></tr><tr>";
        echo "<td>Fødselsdag:</td><td><select name='dato'>
<option value='$dato'>$dato</option>
<option value='1'>1</option>
<option value='2'>2</option>
<option value='3'>3</option>
<option value='4'>4</option>
<option value='5'>5</option>
<option value='6'>6</option>
<option value='7'>7</option>
<option value='8'>8</option>
<option value='9'>9</option>
<option value='10'>10</option>
<option value='11'>11</option>
<option value='12'>12</option>
<option value='13'>13</option>
<option value='14'>14</option>
<option value='15'>15</option>
<option value='16'>16</option>
<option value='17'>17</option>
<option value='18'>18</option>
<option value='19'>19</option>
<option value='20'>20</option>
<option value='21'>21</option>
<option value='22'>22</option>
<option value='23'>23</option>
<option value='24'>24</option>
<option value='25'>25</option>
<option value='26'>26</option>
<option value='27'>27</option>
<option value='28'>28</option>
<option value='29'>29</option>
<option value='30'>30</option>
<option value='31'>31</option>
</select> ";
echo "<select name='maaned'>
<option value='$maaned'>$maaned</option>
<option value='01'>01</option>
<option value='02'>02</option>
<option value='03'>03</option>
<option value='04'>04</option>
<option value='05'>05</option>
<option value='06'>06</option>
<option value='07'>07</option>
<option value='08'>08</option>
<option value='09'>09</option>
<option value='10'>10</option>
<option value='11'>11</option>
<option value='12'>12</option>
</select> <input type='text' name='aar' value='$aar' size='6' maxlength='4'></td></tr><tr>";
echo "<td>Køn:</td><td><select name='kon'>";
if($kon == "Mand")
{
echo "<option value='$kon'>$kon</option>";
echo "<option value='Kvinde'>Kvinde</option>";
echo "<option value='Ikke oplyst'>Ikke oplyst</option>";
}
else if ($kon == "Kvinde")
{
echo "<option value='$kon'>$kon</option>";
echo "<option value='Mand'>Mand</option>";
echo "<option value='Ikke oplyst'>Ikke oplyst</option>";
}
else
{
echo "<option value='$kon'>$kon</option>";
echo "<option value='Mand'>Mand</option>";
echo "<option value='Kvinde'>Kvinde</option>";
}

echo "</select></td></tr><tr>";
echo "<td>Web: </td><td>http://<input type='text' name='web' value='$web' size='45' maxlength='255'></td></tr><tr>";
echo "<td>Interesser:</td><td><textarea name='interesser' rows='4' cols='30'>$interesser</textarea></td></tr><tr>";
echo "<td>Privat profil:</td><td>";
if($privat == "nej")
{
echo"<input type='checkbox' name='privat' value='ja'> ";
}
else
{
echo"<input type='checkbox' name='privat' value='ja' checked> ";
}
echo "Marker her hvis andre brugere skal ansøge om at se din profil</td></tr><tr>";
echo "<td></td><td><br><input class='inputknap' type='submit' value='Ændre oplysninger'></td>";
echo"</tr></table><br>";
}
?>
</form>
<?php
if ($profil_billede != "0")
{
echo"<img width='90' height='100' src='profil-billeder/$profil_billede'?cache=".microtime()."' alt='Profil Billede' /><br><a href='redigere-foto.php?menu=$menu'>Ret foto</a><br><br>";
echo"<a href='slet-foto.php?menu=$menu'>Fjern profil billede</a><br><br>";
}
else
{
echo"Du har ikke uploadet noget profil billede<br><a href='redigere-foto.php?menu=$menu'>Upload foto</a><br><br>";
}

?>
</div>
</p>					

<?php
include 'footer.php';
?>
</body>

</html>