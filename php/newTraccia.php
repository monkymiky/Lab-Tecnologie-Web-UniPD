<?php
require_once "DBAccess.php";
USE DB\DBAccess;

ini_set('display_errors',1); // per visualizzare gli errori
ini_set("display_startup_errors",1); // per visualizzare gli errori 
error_reporting(E_ALL);

setlocale(LC_ALL, 'it_IT');
$paginaHTML = file_get_contents("../html/aggiungiTracciaTemplate.html");

$messaggiForm ="";
$listaAlbum ="";
$albumStringa ="";

function pulisciInput($value){ // non sarebbe meglio quello consigliato dal w3c school??
	//elimina gli spazi
	$value = trim($value);
	//rimuove tag html (non è sempre una buona idea!)(se i lcodice non è valido, non trova tag di chiusura potrebbe cancellare l'intera pagina)
	$value = strip_tags($value);
	//converte i caratteri speciali in entità html (ex. &lt;)
	$value = htmlentities($value);
	return $value;
}

$connection = new DBAccess();
$connectionOK = $connection -> openDBConnection();

if($connectionOK){
	$resultListaAlbum = $connection->getListaAlbum();
	foreach ($resultListaAlbum as $album){
		if(isset($_POST['submit']) && isset($_POST['album']) == $album['ID']){ //l'album selezionato deve essere uguale
			$listaAlbum.="<option value=\"" . $album["ID"] . "\" selected>" . $album["Titolo"]. "</option>";
		}else{
			$listaAlbum.="<option value=\"" . $album["ID"] . "\">" . $album["Titolo"]. "</option>";
		}
	}
}
	
if(isset($_POST['submit'])){
	
	//controlli, sul titolo si potrebbe controllare la lunghezza della stringa
	//contenut esplcitito deve essere settato
	//data e url devono essere date e url
	if(isset($_POST['titolo'])){
		pulisciInput($_POST['titolo']);
		if(strlen($_POST['titolo'])<4){
			$messaggiForm .= "<p> il campo titolo deve essere lungo almeno 4 caratteri <p>";
			$_POST['titolo'] = "";
		}
	}else{
		$messaggiForm .= "<p> il campo titolo deve essere riempito <p>";
		$_POST['titolo'] = "";
	}

	if(isset($_POST['durata'])){
		pulisciInput($_POST['durata']);
		if(!preg_match("/[0-9][0-9]:[0-9][0-9]/", $_POST['durata'])){
			$messaggiForm .= "<p> il campo durata deve contenere 2 numeri seguiti da ':' e poi da altri 2 numeri";
			$_POST['durata'] = "";
		}
	}else{
		$messaggiForm .= "<p> il campo durata deve essere riempito <p>";
		$_POST['durata'] = "";
	}

	if(!isset($_POST['esplicito']) || filter_input(INPUT_POST,$_POST['esplicito'], FILTER_VALIDATE_BOOLEAN )){
		$messaggiForm .= "<p> il campo esplicito deve essere specificato <p>";
		$_POST['esplicito'] = "";
	}

	if(isset($_POST['data'])){
		pulisciInput($_POST['data']);
		if(!preg_match("/(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[1,2])\/(19|20)\d{2}/", $_POST['data'])){ // febbraio da 28 gg non è considerato
			$messaggiForm .= "<p> il campo data deve essere nel formato dd/mm/yyyy";
			$_POST['data'] = "";
		}
	}

	if(isset($_POST['urlvideo']) ){
		if(!filter_input(INPUT_POST,$_POST['urlvideo'], FILTER_VALIDATE_URL )){
			$messaggiForm .= "<p> il URL inserito non è in un formato valido <p>";
		}
	}

	if(isset($_POST['album'])){
		pulisciInput($_POST['album']);
	}else{
		$messaggiForm .= "<p> deve essere selezionato un album <p>";
	}
	
	if(isset($_POST['note'])){
		pulisciInput($_POST['note']);
	}

	// query di inserimento e replace valori inseriti se il form va male
}

$paginaHTML = str_replace("{listaAlbum}", $listaAlbum, $paginaHTML);
$paginaHTML = str_replace("{messaggiForm}", $messaggiForm, $paginaHTML);
echo $paginaHTML;