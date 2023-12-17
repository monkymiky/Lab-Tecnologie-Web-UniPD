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


$titolo = "";
$durata = "";
$urlvideo = "";
$data = "";
$albumStringa ="";
$note = "";
$checkedYes ="";
$checkedNo ="";

function pulisciInput($value){ // non sarebbe meglio quello consigliato dal w3c school??
	//elimina gli spazi
	$value = trim($value);
	//rimuove tag html (non è sempre una buona idea!)(se i lcodice non è valido, non trova tag di chiusura potrebbe cancellare l'intera pagina)
	$value = strip_tags($value);
	//converte i caratteri speciali in entità html (ex. &lt;)
	$value = htmlentities($value);
	return $value;
}

	
if(isset($_POST['submit'])){
	
	if(isset($_POST['titolo'])){
		pulisciInput($_POST['titolo']);
		if(strlen($_POST['titolo'])<4){
			$messaggiForm .= "<p> il campo titolo deve essere lungo almeno 4 caratteri <p>";
		}else{
			$titolo = $_POST['titolo'];
		}
	}else{
		$messaggiForm .= "<p> il campo titolo deve essere riempito <p>";
	}

	if(isset($_POST['durata'])){
		pulisciInput($_POST['durata']);
		if(!preg_match("/[0-9][0-9]:[0-9][0-9]/", $_POST['durata'])){
			$messaggiForm .= "<p> il campo durata deve contenere 2 numeri seguiti da ':' e poi da altri 2 numeri";
		}else{
			$durata=$_POST['durata'];
		}
	}else{
		$messaggiForm .= "<p> il campo durata deve essere riempito <p>";
	}

	if(!isset($_POST['esplicito']) || filter_input(INPUT_POST,$_POST['esplicito'], FILTER_VALIDATE_BOOLEAN )){
		$messaggiForm .= "<p> il campo esplicito deve essere specificato <p>";
	}else{
		if($_POST['esplicito'] == "1"){
			$checkedYes = "checked";
		}else{
			$checkedNo = "checked";
		}
	}

	if(isset($_POST['dataRadio']) && $_POST['dataRadio'] =! 1){ // non so perche esca 1 come valore se non impostato... forse value="" viene sostituito con 1 ?
		pulisciInput($_POST['dataRadio']);
		if(!preg_match("/(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[1,2])\/(19|20)\d{2}/", $_POST['dataRadio'])){ // febbraio da 28 gg non è considerato
			$messaggiForm .= "<p> il campo data deve essere nel formato gg/mm/aaaa   ->".$_POST['dataRadio'];
		}else{
			$data = $_POST['dataRadio'];
		}
	}else{
		$_POST['dataRadio'] = NULL;
	}

	if(isset($_POST['urlVideo']) && $_POST['urlVideo'] =! 1 ){ // non so perche esca 1 come valore se non impostato... forse value="" viene sostituito con 1 ?
		if(!filter_input(INPUT_POST,$_POST['urlVideo'], FILTER_VALIDATE_URL )){
			$messaggiForm .= "<p>L'URL inserito non è in un formato valido <p> ->".$_POST['urlVideo'];
		}else{
			$urlvideo = $_POST['urlVideo'];
		}
	}else{
		$_POST['urlVideo'] = NULL;
	}

	if(isset($_POST['album'])){
		pulisciInput($_POST['album']);
		$albumStringa = $_POST['album'];
	}else{
		$messaggiForm .= "<p> deve essere selezionato un album <p>";
	}
	
	if(isset($_POST['note'])){
		pulisciInput($_POST['note']);
		$note = $_POST['note'];
	}
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
	if(isset($_POST['submit'])){ 
		if($messaggiForm != ""){ //replace valori inseriti
			if(isset($_POST['reset'])){
				$titolo = "";
				$durata = "";
				$urlvideo = "";
				$data = "";
				$albumStringa = "";
				$note = "";
				$checkedYes ="";
				$checkedNo ="";
			}
		}// else query di inserimento dopo l' apertura della connessione (non fatta all'inizio per migliorare le prestazioni)
		else{
			$connection->insertNewTrack($_POST['titolo'], $_POST['durata'],$_POST['esplicito'],$_POST['urlVideo'],$_POST['dataRadio'],$_POST['album'],$_POST['note']);
			$titolo = "";
			$durata = "";
			$urlvideo = "";
			$data = "";
			$albumStringa = "";
			$note = "";
			$checkedYes ="";
			$checkedNo ="";
			$messaggiForm .= "<p>INSERIMENTO AVVENUTO CON SUCCESSO<p><p>è possibile inserire un'ulteriore nuova traccia<p> ";
		}
	}
}
$paginaHTML = str_replace("{note}", $note, $paginaHTML);
$paginaHTML = str_replace("{album}", $albumStringa, $paginaHTML);
$paginaHTML = str_replace("{urlvideo}", $urlvideo, $paginaHTML);
$paginaHTML = str_replace("{data}", $data, $paginaHTML);
$paginaHTML = str_replace("{checkedYes}", $checkedYes, $paginaHTML);
$paginaHTML = str_replace("{checkedNo}", $checkedNo, $paginaHTML);
$paginaHTML = str_replace("{durata}", $durata, $paginaHTML);
$paginaHTML = str_replace("{titolo}", $titolo, $paginaHTML);

$paginaHTML = str_replace("{messaggiForm}", $messaggiForm, $paginaHTML);
$paginaHTML = str_replace("{listaAlbum}", $listaAlbum, $paginaHTML);

echo $paginaHTML;