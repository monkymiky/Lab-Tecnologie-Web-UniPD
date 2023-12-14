<?php
    require_once("DBAccess.php");
    use DB\DBAccess;
    ini_set('display_errors',1);
    ini_set("display_startup_errors",1);

    setlocale(LC_ALL, 'it_IT');
    
    $paginaHTML = file_get_contents("../html/albumTemplate.html");

    $stringaAlbum = "";
    $titoloAlbum = "";

    $idAlbum = $_GET["id"];

    $connection = new DBAccess();
    $connectionOK = $connection -> openDBConnection();

    if($connectionOK){
        $infoAlbum = $connection -> getAlbum($idAlbum);
        if($infoAlbum[0] != null){
            $stringaAlbum .= "<img src=\"../".$infoAlbum[1]."\" id=\"albumCover\"><dl id=\"albumInfo\"><dt>Durata</dt><dd>".$infoAlbum[3]."
            </dd><dt>Data di uscita: </dt><dd><time datetime=\"".$infoAlbum[2]."\">". date("j F Y", strtotime($infoAlbum[2])). "</time></dd>";
            $tracce = $connection ->getTracceAlbum($idAlbum);
            if($tracce!=null){
                $stringaAlbum .= "<dl>";
                foreach($tracce as $traccia){
                    $stringaAlbum .= "<dh>".$traccia['Titolo']."</dh><dd>".$traccia['Esplicito']."</dd><dd>".$traccia['Durata']."</dd><dd>".$traccia['DataRadio']."</dd><dd>".$traccia['URLVideo']."</dd>";
                }
                $stringaAlbum .= "</dl>";
            } else {
                $stringaAlbum = "<p>Non sono presenti brani per questo album</p>";
            }
        } else {
            $stringaAlbum = "<p>Album non esistente</p>";
        }
        $connection -> closeConnection();
    } else {
        $stringaAlbum = "<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio</p>";
    }

    $paginaHTML = str_replace("{NomeAlbum}", $titoloAlbum, $paginaHTML);
    $paginaHTML = str_replace("{album}", $stringaAlbum, $paginaHTML);
    echo $paginaHTML;
?>