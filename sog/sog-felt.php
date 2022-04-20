<table><tr>
<?
echo"<form name='sog' method='post' ACTION='$side/sog/sogning.php?menu=$menu&sog=ja'>";
echo"<td>";
echo"<input size='10' value='Søg i $menu' type='text' name='sog_ord' onblur=\"if(this.value=='') this.value='Søg i $menu';\" onfocus=\"if(this.value=='Søg i $menu') this.value='';\"> ";
?>
<input class='inputknap' name="soge_nu" type='submit' value='søg'>
<?
if(!empty($_SESSION['sog_ord']))
{
echo"<a href='$side/sog/sogning.php".$_SESSION['soge_page']."'>sidste søgning<a/>";
}
?>
</td></tr>
</tr></table>
</form>
