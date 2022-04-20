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
echo"Siden du forsøger at gå ind på er ikke tilgænglig.";
$_SESSION['ikke_aktiv_modul'] = "";
}
echo"</div>";
?>


<h1>Bruger system</h1>


Denne side er under konstruktion. Du kan allerede nu oprette dig som bruger og følge med i hvad der sker.
Hele det system som er på denne side kan downloades på <br><br>
<b><a href='http://download.magiske-michael.dk'>http://download.magiske-michael.dk/</a></b> <br><br>
Du kan løbende læse på denne side hvilke muligheder der er i systemet.<br>
På denne side kan du ikke komme ind i admin delen af systemet.<br>
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
		<li>Følg guiden og systemet opsætter sig selv.</li>
	</ul>
	</li>
	<li><b>Generelt</b><ul>
	<li><b>Brugere</b><ul>
		<li>Brugere skal aktivere sig via email før de kan logge ind.</li>
		<li>Vælge om de vil logges automatisk ind.</li>
		<li>Glemt password funktion.</li>
		<li>Rette egen profil.</li>
		<li>Se liste over alle brugere, hvem er online og offline.</li>
		<li>Brugere kan ikke se deaktiverede moduler, selvom de kender den præcise adresse.</li>
		<li>Kan gøre egen profil privat så kun brugere på vennelisten kan se profilen.</li>
		<li>Se andres vennelister.</li>
		<li>Gæstebog til hver profil.</li>
		<li>Søge efter brugere.</li>
	</ul></li>
	<li><b>Admin</b><ul>
	<li>Se brugere som ikke har aktiveret sig.</li>
	<li>Påminde brugere som ikke har aktiveret sig.</li>
	<li>Slette brugere som ikke har aktiveret sig.</li>
	<li>Deaktivere brugere så de ikke kan logge ind.</li>
	<li>Slette deaktiverede brugere.</li>
	<li>bestemme hvad der skal stå ved slettede brugere.</li>
	<li>Aktivere brugere så de igen kan logge ind.</li>
	<li>Bestemme hvor mange indlæg der skal vises pr. side. Dette bestemmes individuelt for hvert modul.</li>
	<li>indsætte smileys som kan bruges forskellige steder i systemet.</li>
	<li>Vælge hvilke ord som ikke må bruges i forum osv.</li>
	<li>Bestemme hvilke moduler som skal være aktive.</li>
	<li>Ændre rækkefølgen af menuen.</li>
	<li>Vælge hvor mange points brugere skal have når de opretter sig.</li>
	<li>Vælge hvor mange points brugere skal have for hver time de er online.<br></li>
	<li>Vælge rækkefølgen af alle menu punkter.</li>
	<li>Se alle profiler også selvom de er gjort private.</li>
	<li>Ændre layoutet nemt ved at udfylde en formular.<br><br></li>
</ul></li>
</ul></li>

	<li><b><a name="Forum_(modul)">Forum (modul)</a></b><ul>
	<li><b>Brugere</b><ul>
	<li>Oprette og svare på tråde.</li>
	<li>Formatere teksten med fed, kursiv og understreget.</li>
	<li>indsætte smileys i teksten.</li>
	<li>Citere andre indlæg.</li>
	<li>Anmelde indlæg.</li>
	<li>Se liste over egne forum tråde.</li>
	<li>Søge i forumtråde.</li>
</ul></li>
	<li><b>Admin</b><ul>
	<li>Oprette hoved og under grupper.</li>
	<li>Rette og slette grupper.</li>
	<li>Styre hvad der skal ske med anmeldte indlæg.</li>
	<li>Bestemme rækkefølgen af forum grupperne.<br><br></li>
</ul></li>
</ul></li>

<li><b><a name="Artikel_(modul)">Artikel (modul)</a></b><ul>
	<li><b>Brugere</b><ul>
	<li>Oprette artikler.</li>
	<li>Bestemme om en artikel skal koste point at se.</li>
	<li>Formatere teksten med fed, kursiv og understreget.</li>
	<li>indsætte smileys i teksten.</li>
	<li>Bestemme hvornår egen artikler skal gøres synlig for andre.</li>
	<li>Rette egne artikler.</li>
	<li>Vælge favorit artikler.</li>
	<li>Stemme på alle artikler.</li>
	<li>Skrive kommentar til alle artikler.</li>
	<li>Søge blandt alle artikler.</li>
	<li>Først se artikler som koster point når man har købt adgang.</li>
	<li>Bruger som har oprettet artiklen får prisen lagt til point hver gang en anden bruger køber artiklen.</li>
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
	<li>Vælge favorit billeder.</li>
	<li>Stemme på alle billeder.</li>
	<li>Skrive kommentar til alle billeder.</li>
	<li>søge blandt billederne</li>
</ul></li>
	<li><b>Admin</b><ul>
	<li>Oprette kategorier.</li>
	<li>Rette og slette kategorier.</li>
	<li>Uploade billeder.</li>
	<li>Vælge om brugere skal kunne uploade billeder.</li>
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
	<li>Markere modtagne mails som læst eller ulæst.</li>
</ul></li>
	<li><b>Admin</b><ul>
	<li>Der er i øjeblikket ingen admin funktioner til dette modul.</li>
</ul></li>
</ul></li>

</ul>

<ul>

<li><a name="Kalender_(modul)0"><b>Kalender</b></a><ul>
	<li><b>Brugere</b><ul>
	<li>Oprette events i kalenderen.</li>
	<li>Læse alle events.</li>
	<li>Rette og slette egne events</li>
	<li>Søge blandt events</li>
	<li>Tilføje events som favorit</li>
</ul></li>
	<li><b>Admin</b><ul>
	<li>Slette events.</li>
	<li>Alle events som er over et år gamle slettes automatisk.</li>
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
	<li>Bestemme hvornår en afstemning skal være aktiv.</li>
	<li>Bestemme om en afstemning automatisk skal udløbe på en dato.</li>
	<li>Slette afstemninger.</li>
</ul></li>

</ul>

</li>

<li><a name="Topliste_(modul)"><b>Afstemning</b></a><ul>

<li><b>Brugere</b><ul>

<li>Tilføje sider til listen</li>
<li>indsætte link på egen side for at optjene kliks på listen</li>
<li>Slette egne sider</li>
<li>Stemme på egen og andres sider</li>
<li>Når flere indgående kliks jo højre ligger siden på listen</li>
<li>En brugers klik tælles kun med 1 gang pr. måned</li>
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
<li>Vælge hvor mange point man vil satse pr. gang</li>
<li>Maks. vælge 10 tal og spille på pr. gang</li>
</ul></li>
<li></li>
</ul></li>

</ul>
<ul>
	<li><b>Admin</b><ul>
	<li>Der er i øjeblikket ingen admin funktioner til dette modul.</li>
</ul>
	</li>
</ul>
</li>

<li><a name="Tagwall_(modul)0"><b>Tagwall</b></a><ul>

<li><b>Brugere</b><ul>

<li>Skrive i tagwall</li>
<li>Smide points til andre brugere</li>
<li>Tage points som er smidt</li>
<li>Alle meddelelser gemmes i en uge før de slettes</li>
<li>Bruge koder som admin har tilføjet</li>
</ul></li>

</ul>
<ul>
	<li><b>Admin</b><ul>
		<li>Kan tilføje koder som brugere kan bruge i tagwallen</li>
	</ul></li>
</ul></li>
<li>
<p>&nbsp;</li>

</ul>

<h2>Jeg har ikke planlagt at kode flere moduler, men har du et øsnke er du velkommen til at 
skrive det i forummet, så vil jeg kigge på det.</h2>
<?php
include 'footer.php';
?>
</body>

</html>