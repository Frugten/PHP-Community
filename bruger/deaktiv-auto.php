<?
session_start();
include '../settings/connect.php';
include '../settings/settings.php';

$_SESSION['deaktiver_auto'] = 1;

header("Location: $side/settings/auto-tjek.php");
exit;
?>
