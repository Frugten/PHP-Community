<?
session_start();
include 'connect.php';
$bruger = $_SESSION['brugernavn'];

mysql_query("UPDATE brugere SET online = 'nej', laston = NOW(), ontid = NOW() WHERE brugernavn ='$bruger'") or die(mysql_error());


$_SESSION = array();
session_destroy();
header("Location: $side");
exit;
?>