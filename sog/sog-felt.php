<table><tr>
<?
echo"<form name='sog' method='post' ACTION='$side/sog/sogning.php?menu=$menu&sog=ja'>";
echo"<td>";
echo"<input size='10' value='S�g i $menu' type='text' name='sog_ord' onblur=\"if(this.value=='') this.value='S�g i $menu';\" onfocus=\"if(this.value=='S�g i $menu') this.value='';\"> ";
?>
<input class='inputknap' name="soge_nu" type='submit' value='s�g'>
<?
if(!empty($_SESSION['sog_ord']))
{
echo"<a href='$side/sog/sogning.php".$_SESSION['soge_page']."'>sidste s�gning<a/>";
}
?>
</td></tr>
</tr></table>
</form>
