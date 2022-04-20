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
//Tjekker om modulet er aktivt
if(empty ($_SESSION['aktiv_Post']) && $gruppe != "admin")
{
$resultat = mysql_query("SELECT menuid FROM menu WHERE titel = 'Post' AND aktiv = 'nej' AND admin='brugermenu'");//Spørger efter ID
$number = mysql_num_rows($resultat);//Tæller antaller af resultater
if($number == 1){
$_SESSION['aktiv_Post'] ="nej";
}
else{
$_SESSION['aktiv_Post'] ="ja";
}
}

if($_SESSION['aktiv_Post'] == "nej")
{
$_SESSION['ikke_aktiv_modul'] = "nej";
header("Location: $side");
}
//aktiv modul tjek slut

//Al din kode herunder
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
<script language = "JavaScript" type = "text/javascript">
<!--
// bbCode control by
// subBlue design
// www.subBlue.com
// adapted for Joomlaboard by the Two Shoes Module Factory (www.tsmf.net)
// Startup variables
var imageTag = false;
var theSelection = false;
// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version
var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_moz = 0;
var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac")!=-1);
// Define the bbCode tags
bbcode = new Array();
bbtags = new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[boks]','[/boks]','[code]','[/code]','[ul]','[/ul]','[ol]','[/ol]','[img size=150]','[/img]','[url]','[/url]','[li]','[/li]');
imageTag = false;
// Shows the help messages in the helpline window
function helpline(help) {
   document.postform.helpbox.value = eval(help + "_help");
}
// Replacement for arrayname.length property
function getarraysize(thearray) {
   for (i = 0; i < thearray.length; i++) {
      if ((thearray[i] == "undefined") || (thearray[i] == "") || (thearray[i] == null))
         return i;
      }
   return thearray.length;
}
// Replacement for arrayname.push(value) not implemented in IE until version 5.5
// Appends element to the array
function arraypush(thearray,value) {
   thearray[ getarraysize(thearray) ] = value;
}
// Replacement for arrayname.pop() not implemented in IE until version 5.5
// Removes and returns the last element of an array
function arraypop(thearray) {
   thearraysize = getarraysize(thearray);
   retval = thearray[thearraysize - 1];
   delete thearray[thearraysize - 1];
   return retval;
}
function bbstyle(bbnumber) {
   var txtarea = document.postform.message;
   txtarea.focus();
   donotinsert = false;
   theSelection = false;
   bblast = 0;
   if (bbnumber == -1) { // Close all open tags & default button names
      while (bbcode[0]) {
         butnumber = arraypop(bbcode) - 1;
         txtarea.value += bbtags[butnumber + 1];
         buttext = eval('document.postform.addbbcode' + butnumber + '.value');
         eval('document.postform.addbbcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
      }
      imageTag = false; // All tags are closed including image tags :D
      txtarea.focus();
      return;
   }
   if ((clientVer >= 4) && is_ie && is_win)
   {
      theSelection = document.selection.createRange().text; // Get text selection
      if (theSelection) {
         // Add tags around selection
         document.selection.createRange().text = bbtags[bbnumber] + theSelection + bbtags[bbnumber+1];
         txtarea.focus();
         theSelection = '';
         return;
      }
      else {
        txtarea.focus();
        document.selection.createRange().text = bbtags[bbnumber] + bbtags[bbnumber + 1];
        return;
      }
   }
   else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
   {
      mozWrap(txtarea, bbtags[bbnumber], bbtags[bbnumber+1]);
      return;
   }
   else //if (txtarea.selectionEnd == txtarea.selectionStart) // don't know if we need it... it works even if commented out. ;)
   {
        txtarea.value = txtarea.value.substring(0, txtarea.selectionStart) + bbtags[bbnumber] + bbtags[bbnumber + 1] + txtarea.value.substring(txtarea.selectionEnd, txtarea.value.length);
        return;
   }
   // Find last occurance of an open tag the same as the one just clicked
   for (i = 0; i < bbcode.length; i++) {
      if (bbcode[i] == bbnumber+1) {
         bblast = i;
         donotinsert = true;
      }
   }
   if (donotinsert) {      // Close all open tags up to the one just clicked & default button names
      while (bbcode[bblast]) {
            butnumber = arraypop(bbcode) - 1;
            txtarea.value += bbtags[butnumber + 1];
            buttext = eval('document.postform.addbbcode' + butnumber + '.value');
            eval('document.postform.addbbcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
            imageTag = false;
         }
         txtarea.focus();
         return;
   } else { // Open tags
      if (imageTag && (bbnumber != 14)) {    // Close image tag before adding another
         txtarea.value += bbtags[15];
         lastValue = arraypop(bbcode) - 1;   // Remove the close image tag from the list
         document.postform.addbbcode14.value = "Img";  // Return button back to normal state
         imageTag = false;
      }
      // Open tag
      txtarea.value += bbtags[bbnumber];
      if ((bbnumber == 14) && (imageTag == false)) imageTag = 1; // Check to stop additional tags after an unclosed image tag
      arraypush(bbcode,bbnumber+1);
      eval('document.postform.addbbcode'+bbnumber+'.value += "*"');
      txtarea.focus();
      return;
   }
   storeCaret(txtarea);
}
// From http://www.massless.org/mozedit/
function mozWrap(txtarea, open, close)
{
   var selLength = txtarea.textLength;
   var selStart = txtarea.selectionStart;
   var selEnd = txtarea.selectionEnd;
   if (selEnd == 1 || selEnd == 2)
      selEnd = selLength;
   var s1 = (txtarea.value).substring(0,selStart);
   var s2 = (txtarea.value).substring(selStart, selEnd)
   var s3 = (txtarea.value).substring(selEnd, selLength);
   txtarea.value = s1 + open + s2 + close + s3;
   return;
}
// Insert at Claret position. Code from
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function storeCaret(textEl) {
   if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}
function bbfontstyle(bbopen, bbclose) {
   var txtarea = document.postform.message;
   if ((clientVer >= 4) && is_ie && is_win) {
      theSelection = document.selection.createRange().text;
      txtarea.focus();
      if (!theSelection) {
         document.selection.createRange().text = bbopen + bbclose;
      }
      else {
         document.selection.createRange().text = bbopen + theSelection + bbclose;
      }
      txtarea.focus();
      return;
   }
   else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
   {
      mozWrap(txtarea, bbopen, bbclose);
      return;
   }
   else
   {
      txtarea.value = txtarea.value.substring(0, txtarea.selectionStart) + bbopen + bbclose + txtarea.value.substring(txtarea.selectionEnd, txtarea.value.length);
      txtarea.focus();
   }
   storeCaret(txtarea);
}
//#######################################################
//code used in My Profile (userprofile.php)
function textCounter(field, countfield, maxlimit) {
   if(field.value.length > maxlimit){
      field.value = field.value.substring(0, maxlimit);
   }
   else{
      countfield.value = maxlimit - field.value.length;
   }
}
//*********************************************************
// Insert emoticons
function emo($e)
{
    var textfield = document.postform.message;
    // Support for IE
    if (document.selection)
    {
        textfield.focus();
        var sel = document.selection.createRange();
        sel.text = $e;
    }
    // Support for Mozilla
    else if (textfield.selectionStart || textfield.selectionStart == '0')
    {
        var start = textfield.selectionStart;
        var end = textfield.selectionEnd;
        textfield.value = textfield.value.substring(0, start) + $e + textfield.value.substring(end, textfield.value.length);
    }
    else
    {
        textfield.value = textfield.value + $e;
    }
    textfield.focus();
}
function submitForm() {
 submitme=1;
 formname=document.postform.fb_authorname.value;
 if ((formname.length<1)) {
    alert("You forgot to enter your name");
    submitme=0;
 }
 formmail=document.postform.email.value;
 if ((formmail.length<1)) {
    alert("You forgot to enter your email");
    submitme=0;
  }
  formsubject=document.postform.subject.value;
  if ((formsubject.length<1)) {
    alert("You forgot to enter a subject");
    submitme=0;
  }
  if (submitme>0) {
//  var message = document.postform.message.value;
//  message = message.replace(/</g,"&lt;");
//  message = message.replace(/>/g,"&gt;");
//  document.postform.message.value = message;
  //change the following line to true to submit form
    return true;
  }else{
    return false;
  }
}
function cancelForm() {
   document.forms['postform'].action.value = "cancel";
   return true;
}
//**********************************************
// Helpline messages
b_help = "Fed tekst: [b]text[/b] ";
i_help = "Kursiv tekst: [i]text[/i]";
u_help = "Understreget tekst: [u]tekst[/u]";
boks_help = "Teskt i en boks: [boks]tekst[/boks]";
//**************************************************
// Open the preview window (with some post parsing)
function Preview(stylesheet, sbs, template, disemoticons) {
//changed to fix the preview problem in IE with mod_login
//message=document.getElementById('message');
var message = document.postform.message;
//these gotta be in both... I don't knwo why, but it works...
messageString = message.innertext;
messageString = message.value;
messageString = messageString.replace(/<(.*?)>/g,"&lt;$1&gt;");
messageString = messageString.replace(/</g,"&lt;");
messageString = messageString.replace(/\n/g,"<br>");
messageString = messageString.replace(/\[n\]/g,"<b>");
messageString = messageString.replace(/\[\/b\]/g,"</b>");
messageString = messageString.replace(/\[i\]/g,"<i>");
messageString = messageString.replace(/\[\/i\]/g,"</i>");
messageString = messageString.replace(/\[u\]/g,"<u>");
messageString = messageString.replace(/\[\/u\]/g,"</u>");
messageString = messageString.replace(/\[quote\]/g,'<div class="fb_review_quote">');
messageString = messageString.replace(/\[\/quote\]/g,'</div>');
if (disemoticons == 0) {
messageString = messageString.replace(/B\)/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/cool.png" border="0" />');messageString = messageString.replace(/;-\)/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/wink.png" border="0" />');messageString = messageString.replace(/;\)/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/wink.png" border="0" />');messageString = messageString.replace(/:y32b4:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/silly.png" border="0" />');messageString = messageString.replace(/:x/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/sick.png" border="0" />');messageString = messageString.replace(/:woohoo:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/w00t.png" border="0" />');messageString = messageString.replace(/:whistle:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/whistling.png" border="0" />');messageString = messageString.replace(/:unsure:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/unsure.png" border="0" />');messageString = messageString.replace(/:silly:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/silly.png" border="0" />');messageString = messageString.replace(/:side:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/sideways.png" border="0" />');messageString = messageString.replace(/:sick:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/sick.png" border="0" />');messageString = messageString.replace(/:s/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/dizzy.png" border="0" />');messageString = messageString.replace(/:rolleyes:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/blink.png" border="0" />');messageString = messageString.replace(/:pinch:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/pinch.png" border="0" />');messageString = messageString.replace(/:p/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/tongue.png" border="0" />');messageString = messageString.replace(/:ohmy:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/shocked.png" border="0" />');messageString = messageString.replace(/:mad:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/angry.png" border="0" />');messageString = messageString.replace(/:lol:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/grin.png" border="0" />');messageString = messageString.replace(/:laugh:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/laughing.png" border="0" />');messageString = messageString.replace(/:kiss:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/kissing.png" border="0" />');messageString = messageString.replace(/:huh:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/wassat.png" border="0" />');messageString = messageString.replace(/:evil:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/devil.png" border="0" />');messageString = messageString.replace(/:ermm:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/ermm.png" border="0" />');messageString = messageString.replace(/:dry:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/ermm.png" border="0" />');messageString = messageString.replace(/:cheer:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/cheerful.png" border="0" />');messageString = messageString.replace(/:blush:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/blush.png" border="0" />');messageString = messageString.replace(/:blink:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/blink.png" border="0" />');messageString = messageString.replace(/:angry:/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/angry.png" border="0" />');messageString = messageString.replace(/:X/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/sick.png" border="0" />');messageString = messageString.replace(/:S/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/dizzy.png" border="0" />');messageString = messageString.replace(/:P/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/tongue.png" border="0" />');messageString = messageString.replace(/:D/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/laughing.png" border="0" />');messageString = messageString.replace(/:-\)/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/smile.png" border="0" />');messageString = messageString.replace(/:-\(/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/sad.png" border="0" />');messageString = messageString.replace(/:\)/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/smile.png" border="0" />');messageString = messageString.replace(/:\(/g,'<img src="http://www.morselejr.dk/components/com_fireboardhttp://www.morselejr.dk/components/com_fireboard/template/dorona_brown/images/danish/emoticons/sad.png" border="0" />');}
messageString = messageString.replace(/\[img size=([1-4][0-9][0-9])\](.*?)\[\/img\]/g,"<img src=\"$2\" border\"0\" width=\"$1\">");
messageString = messageString.replace(/\[img\](.*?)\[\/img\]/g,"<img src=\"$1\" border\"0\">");
messageString = messageString.replace(/(\[url\])(.*?)(\[\/url\])/g,"<a href=$2 target=\"_blank\">$2</a>");
messageString = messageString.replace(/\[url=(.*?)\](.*?)\[\/url\]/g,"<a href=\"$1\" target=\"_blank\">$2</a>");
messageString = messageString.replace(/\[size=([1-7])\](.+?)\[\/size\]/g,"<font size=\"$1\">$2</font>");
messageString = messageString.replace(/\[color=(.*?)\](.*?)\[\/color\]/g,"<span style=\"color: $1\">$2</span>");
messageString = messageString.replace(/\[file name=(.*?) size=(.*?)\](.*?)\[\/file\]/g,"<div class=\"fb_file_attachment\"><span class=\"contentheading\">File Attachment:</span><br>File name: <a href=\"$3\">$1</a><br>File size:$2 bytes</div>");
//and finally open the window for displaying the lot
win = window.open('','Preview','width=640, height=480, toolbar = no, status = no, resizable, scrollbars');
win.document.write("<link media=\"all\" type=\"text/css\" href=\""+ stylesheet + "\" rel=\"stylesheet\">");
win.document.write("<link media=\"all\" type=\"text/css\" href=\""+ sbs + "/template/" + template + "/forum.css\" rel=\"stylesheet\">");
win.document.write("<div class=\"sectiontableentry1\" style=\"margin: 10px 10px 10px 10px; padding: 10px 10px 10px 10px;\">");
win.document.write('' + messageString + '');
win.document.write("</div><div style=\"text-align: center; \">");
win.document.write("<a href=\"javascript:window.close()\"> Luk dette vindue </a> ");
win.document.write("</div>");
}
/**
* Pops up a new window in the middle of the screen
*/
function popupWindow(mypage, myname, w, h, scroll) {
   var winl = (screen.width - w) / 2;
   var wint = (screen.height - h) / 2;
   winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',resizable'
   win = window.open(mypage, myname, winprops);
   if (win.opener == null) win.opener = self;
   if (parseInt(navigator.appVersion) >= 4) { win.window.focus(); }
}
function popUp(URL) {
    eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=no,location=0,statusbar=0,menubar=0,resizable=0,width=300,height=250,left = 262,top = 184');");
}
//-->
</script>

<?
include 'header.php';
?>
<h1>Send ny mail</h1>
<h2>Når du trykke på vis prøve bliver din besked vist nederst på siden som modtageren vil se den. 
Herefter kan du vælge at sende mailen eller rette den</h2>
<?
$brugeren = mysql_real_escape_string($_GET['bruger']);

if (!empty($brugeren))
{
$brug = mysql_query("SELECT brugernavn FROM brugere WHERE brugerid = '$brugeren'") or die(mysql_error());
while ($ts = mysql_fetch_array($brug))
{
$brugernavn = $ts[brugernavn];

$_SESSION['til'] = $brugernavn;
}
}

if ($_SESSION['sendt'] == 1)
{
echo"<b><div class='farvet'>Din mail er nu sendt</div></b>";
$_SESSION['sendt'] = 0;
}
if ($_SESSION['ikke_sendt'] == 1)
{
echo"<b><div class='farvet'>Alle felter skal udfyldes</div></b>";
$_SESSION['ikke_Sendt'] = 0;
}
?>
<table><tr>
<td>
<table><tr>
<?
echo"<form name='postform' method='POST' ACTION='send.php?menu=$menu&bruger=$brugeren'>";
?>
<td>Til:</td>
<td><select name='til'>
<?
if (!empty($_SESSION['til']))
{
echo"<option value='".$_SESSION['til']."'>".$_SESSION['til']."</option>";
}
else
{
echo"<option value=''>Vælg modtager</option>";
}
$smil = mysql_query("SELECT brugernavn FROM brugere WHERE brugernavn != '$bruger' AND brugernavn != '".$_SESSION['til']."' ORDER BY brugernavn ASC") or die(mysql_error());
while ($s = mysql_fetch_array($smil))
{
$brugernavn = $s[brugernavn];

$mail = $_SESSION['mail'];
$mail = stripslashes($mail);

if(!empty($_SESSION['emne']))
{
$emne = $_SESSION['emne'];
}

$emne = stripslashes($_SESSION['emne']);

echo"<option value='$brugernavn'>$brugernavn</option>";
}
echo"</select></td></tr>";
echo"<tr><td>Emne:</td><td><input value='$emne' type='text' name='emne' size='35' maxlength='255'></td></tr>";
echo"<tr><td>Mail:</td><td>";
echo"<input type = \"button\" class = \"fb_button\" accesskey = \"b\" name = \"addbbcode0\" value = \" B \" style = \"font-weight:bold; \" onclick = \"bbstyle(0)\" onmouseover = \"helpline('b')\"/>";
echo"<input type = \"button\" class = \"fb_button\" accesskey = \"i\" name = \"addbbcode2\" value = \" i \" style = \"font-style:italic; \" onclick = \"bbstyle(2)\" onmouseover = \"helpline('i')\"/>";
echo"<input type = \"button\" class = \"fb_button\" accesskey = \"u\" name = \"addbbcode4\" value = \" u \" style = \"text-decoration: underline;\" onclick = \"bbstyle(4)\" onmouseover = \"helpline('u')\"/>";
echo"<input type = \"button\" class = \"fb_button\" accesskey = \"q\" name = \"addbbcode6\" value = \"boks\" onclick = \"bbstyle(6)\" onmouseover = \"helpline('boks')\"/>";
echo"<input type = \"text\" name = \"helpbox\" size = \"40\" class = \"fb_inputbox\" maxlength = \"100\" value = \"Knapper kan bruges på markeret tekst!\"/><br>";
echo"<textarea class = 'fb_txtarea' name='message' id='message' rows='10' cols='60'>";
echo"$mail</textarea></td></tr>";
?>
<tr><td></td><td>
<input class='inputknap' name="send" type='submit' value='Send'>
<input class='inputknap' name="prove" type='submit' value='Vis prøve'>
<input class='inputknap' type='reset' value='Nulstil'>
</td></tr></table>
</form>
</div>	
</td>
<td>
<table>
<?
$tael = mysql_query("SELECT COUNT(*) AS antal FROM smiley") or die(mysql_error());
$row = mysql_fetch_array($tael);
$antals = $row[antal];
if ($antals >0)
{
echo"<p><b>smiley's</b><br>(Klik på den du vil indsætte)</p>";
$smil = mysql_query("SELECT tekst, billede FROM smiley") or die(mysql_error());
echo "<table>";
$i=0;
$antal = 5;//Det antal billeder som skal vises i hver række
while ($s = mysql_fetch_array($smil))
{
$tekst = $s[tekst];
$billede = $s[billede];

    if($i%$antal == 0){
        echo "<tr><td>";
    }
echo"<img class=\"btnImage\" src=\"$side/smiley/$billede\" alt=\"B)\" /onclick=\"javascript:emo('$tekst ');\" style=\"cursor:pointer\"> ";
    
    $i++;
}
echo "</td></tr></table>";
}
else
{
}
?>
</tr>
</table>
<hr>
<?
if ($_SESSION['prove'] == 1)
{
//funktion der fager links i teksten
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

$til = $_SESSION['til'];
$emne = $_SESSION['emne'];
$mail = $_SESSION['mail'];

$mail = htmlentities($mail);
$emne = htmlentities($emne);

//replace [b] og [/b] til <b> og </b> i $text
$ord_der_skal_erstattes = array("[b]","[/b]","[i]","[/i]","[u]","[/u]","[boks]","[/boks]"); 
$erstat_ord_med = array("<b>","</b>","<i>","</i>","<u>","</u>","<div class='boks'>","</div>"); 
$mail = str_replace($ord_der_skal_erstattes, $erstat_ord_med, $mail); 
//erstat slut

$mail = stripslashes($mail);
$emne = stripslashes($emne);

//tjekker hvilke ord der ikke er tilladte og erstatter med andre
$ban = mysql_query("SELECT p_ord, g_ord FROM ban ORDER BY banid");
while($rs = mysql_fetch_array($ban))
{
    $bad[]= "/" . preg_quote( $rs['g_ord'], "/" ) . "/i";
    $good[] = $rs['p_ord'];
}
$emne = preg_replace( $bad, $good, $emne );
$mail = preg_replace( $bad, $good, $mail );
//tjek slut

$mail = nl2br("$mail");
$mail = url2link($mail);

//smileys insættes
$smil = mysql_query("SELECT tekst, billede FROM smiley ORDER BY smilid");
while($smi = mysql_fetch_array($smil))
{
  $mail = str_replace($smi['tekst'], "<img border='0' src='$side/smiley/".$smi['billede']."'>", $mail);

}
//smiley slut


echo"<a name='eksempel'><h1>Eksempel</h1></a>";
echo"<h2>Du kan rette og sende beskeden øverst på siden.</h2>";
echo"<b>Til:</b> $til<br>";
echo"<b>Emne:</b> $emne<br>";
echo"<b>Mail:</b><br>$mail<br><br>";
}

else
{
echo"<h2>Her vises eksemplet af din mail</h2>";
}
?>
<?
include 'footer.php';
?>
</body>

</html>