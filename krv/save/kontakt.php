<?php
define ('MAILTO', "info@krv-hvl.de"); // Empf�nger hier eintragen
define ('MAILFROM', "Kontaktformular"); // ggfls. Absender hier eintragen
define ('CHARSET', "utf-8"); // Zeichenkodierung ggfls. anpassen
$Pflichtfelder = array('Nachname','Strasse','PLZOrt','Email'); // ggfls. weitere Pflichtfelder angeben


$AddHeader = 'Content-Type: text/plain; charset='.CHARSET;
//if(MAILFROM) $AddHeader .= chr(13).chr(10).'From: '.MAILFROM;

if($Formular_abgeschickt = !empty($_POST)) {
  $Formular_leer = true;
  if(ini_get('magic_quotes_runtime')) ini_set('magic_quotes_runtime',0);
  $_POST = array_map('Formular_Daten', $_POST);
}
function Formular_Daten($val) {
  global $Formular_leer;
  if(is_array($val)) return array_map('Formular_Daten', $val);
  if(ini_get('magic_quotes_gpc')) $val = stripslashes($val);
  if($val = trim($val)) $Formular_leer = false;
  return $val;
}

function Formular_Pflichtfelder() {
  global $Pflichtfelder;
  $Fehler = '';
  foreach ($Pflichtfelder as $Feld) {
    $key = str_replace(' ','_',$Feld);
    if(!(isset($_POST[$key]) && trim($_POST[$key])!=='')) {
      if($Fehler) $Fehler .= '<br />';
      $Fehler .= 'Pflichtfeld "' . $Feld . '" nicht ausgef�llt.';
    }
  }
  return $Fehler;
}

function Formular_neu($log='.htPOSTdata.txt') {
  if(file_exists($log) && is_readable($log)
   && file_get_contents($log) == print_r($_POST,true))
  return false;
  if($handle=@fopen($log, 'w')) {
    fwrite($handle, print_r($_POST,true)); fclose($handle);
  }
  return true;
}

function Formular_Nachricht() {
  $msg=''; $vor=''; $nach=': '; // oder z.B. $vor=''; $nach=": ";
  foreach ($_POST as $key => $val) {
    $msg .= $vor.$key.$nach.$val.chr(13).chr(10);
  }
  return $msg;
}

function checkEmail($adr) {
  $regEx = '^([^\s@,:"<>]+)@([^\s@,:"<>]+\.[^\s@,:"<>.\d]{2,}|(\d{1,3}\.){3}\d{1,3})$';
  return (preg_match("/$regEx/",$adr,$part)) ? $part : false;
}


function Formular_Check() {
  global $Formular_leer;
  if($Formular_leer) $Fehler = 'Keine Daten eingetragen.';
  elseif(!$Fehler = Formular_Pflichtfelder()) {
	if(!checkEmail($_POST['Email'])) $Fehler = 'E-Mail fehlerhaft.';
    elseif(!Formular_neu()) $Fehler = 'Nachricht war bereits verschickt.';
  }
  return $Fehler;
}

function Formular_Eingabe($Feldname, $def='') {
  if(isset($_POST[$Feldname]) && $_POST[$Feldname]!=='')
    echo htmlspecialchars($_POST[$Feldname]);
  else echo $def;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Kreisreiterverband Havelland e.V</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="audience" content="All">
<meta name="author" content="">
<meta name="publisher" content="">
<meta name="copyright" content="">
<meta name="Robots" content="index,follow">
<meta name="Language" content="Deutsch">
<meta name="revisit-after" content="1 Month">
<meta name="Content-Language" content="de">
<style type="text/css">
<!--
@import url("css/style.css");
@import url("css/lightbox.css");
-->
</style>
</head>

<body>
<div id="container">
  <div id="header">
    <div id="header_nav"> <a href="index.html">HOME</a> <a href="kontakt.php">KONTAKT</a> <a href="impressum.html">IMPRESSUM</a> </div>
  </div>
  <div id="middle">
    <div id="navigation"> <a href="index.html" class="main">Wir über uns</a> <a href="vorstand.html" class="sub">Vorstand</a> <a href="aufgaben.html" class="main">Aufgaben & Ziele</a><a href="kreismeisterschaft.html" class="main">Kreismeisterschaft</a><a href="kreismeisterschaft-turniere.html" class="sub">Turniere</a><a href="ergebnisse.html" class="sub">Ergebnisse</a><a href="impressum.html" class="main">Impressum</a>    </div>
    <div id="content">
 <h1>Kontakt</h1>
<?php
$Formular_Anzeige = true;
if($Formular_abgeschickt) {
  if($Formular_Fehler = Formular_Check())
    echo '<p class="Meldung" id="Fehler">',$Formular_Fehler,'</p>';
  elseif(mail(MAILTO, "Anfrage über Kontaktformular Kreisreiterverband Havelland e.V.", Formular_Nachricht(), $AddHeader.chr(13).chr(10).'From: '.$_POST['Email']))
  { $Formular_Anzeige = false;
    echo '<h1>VIELEN DANK FÜR IHRE E-MAIL.</h1><p id="ok">Wir werden Ihre Anfrage so schnell wie möglich bearbeiten.</p>';
	}
  else echo '<p class="Meldung" id="Fehler">Server-Fehler !</p>';
}

if($Formular_Anzeige): ?>

<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post"
 enctype="multipart/form-data" accept-charset="<?php echo CHARSET; ?>">
        <label>Anrede:</label>
        <select name="Anrede" size="1">
          <option>Herr</option>
          <option>Frau</option>
        </select>
        <label>Vorname:</label>
        <input type="text" name="Vorname" class="textfield" value="<?php Formular_Eingabe('Vorname'); ?>" />
		<label>*Nachname:</label>
        <input name="Nachname" class="textfield" value="<?php Formular_Eingabe('Name'); ?>" />
        <label>*Stra&szlig;e:</label>
        <input type="text" name="Strasse" class="textfield" value="<?php Formular_Eingabe('Strasse'); ?>" />
		<label>*PLZ/Ort</label>
        <input type="text" name="PLZOrt" class="textfield" value="<?php Formular_Eingabe('PLZOrt'); ?>" />
 		<label>Telefon:</label>
		<input type="text" name="Phone" class="textfield" value="<?php Formular_Eingabe('Phone'); ?>" />
        <label>*E-Mail:</label>
        <input type="text" name="Email" class="textfield" value="<?php Formular_Eingabe('Email'); ?>" />
		<label>Mitteilung:</label>
        <textarea name="Nachricht" rows="4" class="textarea"><?php Formular_Eingabe('Nachricht'); ?></textarea>
        <label>&nbsp;</label>
        <input type="submit" name="Abschicken" value="Abschicken" id="senden">
</form>
<?php endif; ?>
    </div>
  </div>
  <div id="footer">
    <div id="footer_nav"> <a href="index.html">HOME</a> <a href="kontakt.php">KONTAKT</a> <a href="impressum.html">IMPRESSUM</a> </div>
  </div>
</div>
</body>
</html>
