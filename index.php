<?php
session_start();
chdir('layout/');
include 'settings/connect.php';
include 'settings/settings.php';

if(empty($_SESSION ['logget_in']) && empty($_SESSION ['ikke_logget_in']))
{
$_SESSION ['ikke_logget_in'] = "ja";
header("Location: $side/settings/auto-tjek.php");
}

$admin_ud = mysql_real_escape_string($_GET["admin_ud"]);
if($admin_ud == "ja")
{
$_SESSION['admin'] = "nej";
}

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

echo"<div class='farvet'>";
if ($_SESSION['ikke_aktiv_modul'] == "nej")
{
echo"Siden du fors�ger at g� ind p� er ikke tilg�nglig.";
$_SESSION['ikke_aktiv_modul'] = "";
}
echo"</div>";
?>


<h1>Bruger system</h1>


Denne side er under konstruktion. Du kan allerede nu oprette dig som bruger og f�lge med i hvad der sker.
Hele det system som er p� denne side kan downloades p� <br><br>
<b><a href='http://download.magiske-michael.dk'>http://download.magiske-michael.dk/</a></b> <br><br>
Du kan l�bende l�se p� denne side hvilke muligheder der er i systemet.<br>
P� denne side kan du ikke komme ind i admin delen af systemet.<br>
<br>
<h2>Features: (indtil videre)</h2>
| 
<a href='#Forum_(modul)'>Forum</a> | <a href='#Artikel_(modul)'>Artikel</a> | 
<a href='#Galleri_(modul)'>Galleri</a> | <a href='#Intern_mail_(modul)'>Intern mail</a> | 
<a href='#Kalender_(modul)'>Kalender</a> | <a href='#Afstemning_(modul)'>Afstemning</a> | 
<a href='#Topliste_(modul)'>Topliste</a> | <a href='#Konkurrence_(modul)'>Konkurrence</a> | 
<a href='#Tagwall_(modul)'>Tagwall</a> | 


<ul>
	<li><b>Nem installation og opgradering.</b><ul>
		<li>F�lg guiden og systemet ops�tter sig selv.</li>
	</ul>
	</li>
	<li><b>Generelt</b><ul>
	<li><b>Brugere</b><ul>
		<li>Brugere skal aktivere sig via email f�r de kan logge ind.</li>
		<li>V�lge om de vil logges automatisk ind.</li>
		<li>Glemt password funktion.</li>
		<li>Rette egen profil.</li>
		<li>Se liste over alle brugere, hvem er online og offline.</li>
		<li>Brugere kan ikke se deaktiverede moduler, selvom de kender den pr�cise adresse.</li>
		<li>Kan g�re egen profil privat s� kun brugere p� vennelisten kan se profilen.</li>
		<li>Se andres vennelister.</li>
		<li>G�stebog til hver profil.</li>
		<li>S�ge efter brugere.</li>
	</ul></li>
	<li><b>Admin</b><ul>
	<li>Se brugere som ikke har aktiveret sig.</li>
	<li>P�minde brugere som ikke har aktiveret sig.</li>
	<li>Slette brugere som ikke har aktiveret sig.</li>
	<li>Deaktivere brugere s� de ikke kan logge ind.</li>
	<li>Slette deaktiverede brugere.</li>
	<li>bestemme hvad der skal st� ved slettede brugere.</li>
	<li>Aktivere brugere s� de igen kan logge ind.</li>
	<li>Bestemme hvor mange indl�g der skal vises pr. side. Dette bestemmes individuelt for hvert modul.</li>
	<li>inds�tte smileys som kan bruges forskellige steder i systemet.</li>
	<li>V�lge hvilke ord som ikke m� bruges i forum osv.</li>
	<li>Bestemme hvilke moduler som skal v�re aktive.</li>
	<li>�ndre r�kkef�lgen af menuen.</li>
	<li>V�lge hvor mange points brugere skal have n�r de opretter sig.</li>
	<li>V�lge hvor mange points brugere skal have for hver time de er online.<br></li>
	<li>V�lge r�kkef�lgen af alle menu punkter.</li>
	<li>Se alle profiler ogs� selvom de er gjort private.</li>
	<li>�ndre layoutet nemt ved at udfylde en formular.<br><br></li>
</ul></li>
</ul></li>

	<li><b><a name="Forum_(modul)">Forum (modul)</a></b><ul>
	<li><b>Brugere</b><ul>
	<li>Oprette og svare p� tr�de.</li>
	<li>Formatere teksten med fed, kursiv og understreget.</li>
	<li>inds�tte smileys i teksten.</li>
	<li>Citere andre indl�g.</li>
	<li>Anmelde indl�g.</li>
	<li>Se liste over egne forum tr�de.</li>
	<li>S�ge i forumtr�de.</li>
</ul></li>
	<li><b>Admin</b><ul>
	<li>Oprette hoved og under grupper.</li>
	<li>Rette og slette grupper.</li>
	<li>Styre hvad der skal ske med anmeldte indl�g.</li>
	<li>Bestemme r�kkef�lgen af forum grupperne.<br><br></li>
</ul></li>
</ul></li>

<li><b><a name="Artikel_(modul)">Artikel (modul)</a></b><ul>
	<li><b>Brugere</b><ul>
	<li>Oprette artikler.</li>
	<li>Bestemme om en artikel skal koste point at se.</li>
	<li>Formatere teksten med fed, kursiv og understreget.</li>
	<li>inds�tte smileys i teksten.</li>
	<li>Bestemme hvorn�r egen artikler skal g�res synlig for andre.</li>
	<li>Rette egne artikler.</li>
	<li>V�lge favorit artikler.</li>
	<li>Stemme p� alle artikler.</li>
	<li>Skrive kommentar til alle artikler.</li>
	<li>S�ge blandt alle artikler.</li>
	<li>F�rst se artikler som koster point n�r man har k�bt adgang.</li>
	<li>Bruger som har oprettet artiklen f�r prisen lagt til point hver gang en anden bruger k�ber artiklen.</li>
</ul></li>
	<li><b>Admin</b><ul>
	<li>Oprette kategorier.</li>
	<li>Rette og slette kategorier.</li>
	<li>Slette artikler.</li>
	<li>Flytte artikler til andre kategorier.</li>
	<li>Slette kommentare.<br><br></li>
</ul></li>
</ul></li>

<li><b><a name="Galleri_(modul)">Galleri (modul)</a></b><ul>
	<li><b>Brugere</b><ul>
	<li>Uploade billeder hvis admin har tilladt dette.</li>
	<li>V�lge favorit billeder.</li>
	<li>Stemme p� alle billeder.</li>
	<li>Skrive kommentar til alle billeder.</li>
	<li>s�ge blandt billederne</li>
</ul></li>
	<li><b>Admin</b><ul>
	<li>Oprette kategorier.</li>
	<li>Rette og slette kategorier.</li>
	<li>Uploade billeder.</li>
	<li>V�lge om brugere skal kunne uploade billeder.</li>
	<li>Slette billeder.</li>
	<li>Flytte billeder til andre kategorier.</li>
	<li>Slette kommentare.</li>
</ul></li>
</ul></li>

<li><a name="Intern_mail_(modul)"><b>Intern mail</b></a><ul>
	<li><b>Brugere</b><ul>
	<li>Sende mails til hinanden.</li>
	<li>se modtagne og sendte mails.</li>
	<li>Slette modtagne og sendte mails.</li>
	<li>Markere modtagne mails som l�st eller ul�st.</li>
</ul></li>
	<li><b>Admin</b><ul>
	<li>Der er i �jeblikket ingen admin funktioner til dette modul.</li>
</ul></li>
</ul></li>

</ul>

<ul>

<li><a name="Kalender_(modul)0"><b>Kalender</b></a><ul>
	<li><b>Brugere</b><ul>
	<li>Oprette events i kalenderen.</li>
	<li>L�se alle events.</li>
	<li>Rette og slette egne events</li>
	<li>S�ge blandt events</li>
	<li>Tilf�je events som favorit</li>
</ul></li>
	<li><b>Admin</b><ul>
	<li>Slette events.</li>
	<li>Alle events som er over et �r gamle slettes automatisk.</li>
</ul>
	<p></li>
</ul></li>

<li><a name="Afstemning_(modul)"><b>Afstemning</b></a><ul>

<li><b>Brugere</b><ul>
	<li>Stemme en gang pr. afstemning.</li>
	<li>Se alle afstemninger.</li>
</ul></li>
<li><b>Admin</b><ul>
	<li>Oprette afstemninger med X antal svar muligheder.</li>
	<li>Rette afstemninger som ikke er aktive.</li>
	<li>Bestemme hvorn�r en afstemning skal v�re aktiv.</li>
	<li>Bestemme om en afstemning automatisk skal udl�be p� en dato.</li>
	<li>Slette afstemninger.</li>
</ul></li>

</ul>

</li>

<li><a name="Topliste_(modul)"><b>Afstemning</b></a><ul>

<li><b>Brugere</b><ul>

<li>Tilf�je sider til listen</li>
<li>inds�tte link p� egen side for at optjene kliks p� listen</li>
<li>Slette egne sider</li>
<li>Stemme p� egen og andres sider</li>
<li>N�r flere indg�ende kliks jo h�jre ligger siden p� listen</li>
<li>En brugers klik t�lles kun med 1 gang pr. m�ned</li>
</ul></li>

</ul>
<ul>
	<li><b>Admin</b><ul>
		<li>Slette alle sider</li>
	</ul></li>
</ul></li>
<li><a name="Konkurrence_(modul)"><b>Konkurrence</b></a><ul>

<li><b>Brugere</b><ul>

<li>Roulette<ul>

<li>Maks. spille 3 gange om dagen</li>
<li>V�lge hvor mange point man vil satse pr. gang</li>
<li>Maks. v�lge 10 tal og spille p� pr. gang</li>
</ul></li>
<li></li>
</ul></li>

</ul>
<ul>
	<li><b>Admin</b><ul>
	<li>Der er i �jeblikket ingen admin funktioner til dette modul.</li>
</ul>
	</li>
</ul>
</li>

<li><a name="Tagwall_(modul)0"><b>Tagwall</b></a><ul>

<li><b>Brugere</b><ul>

<li>Skrive i tagwall</li>
<li>Smide points til andre brugere</li>
<li>Tage points som er smidt</li>
<li>Alle meddelelser gemmes i en uge f�r de slettes</li>
<li>Bruge koder som admin har tilf�jet</li>
</ul></li>

</ul>
<ul>
	<li><b>Admin</b><ul>
		<li>Kan tilf�je koder som brugere kan bruge i tagwallen</li>
	</ul></li>
</ul></li>
<li>
<p>&nbsp;</li>

</ul>

<h2>Jeg har ikke planlagt at kode flere moduler, men har du et �snke er du velkommen til at 
skrive det i forummet, s� vil jeg kigge p� det.</h2>
<?php
include 'footer.php';
?>
</body>

</html>