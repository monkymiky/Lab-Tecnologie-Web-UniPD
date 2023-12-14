<?php
    require_once "DBAccess.php";
    use DB\DBAccess;
    ini_set('display_errors',1); // per visualizzare gli errori
    ini_set("display_startup_errors",1); // per visualizzare gli errori 

    setlocale(LC_ALL, 'it_IT');

    $paginaHTML = file_get_contents("../html/discografia.html");

    $stringaAlbum = "";
    $listaAlbum = "";

    $connection = new DBAccess();
    $connectionOK = $connection->openDBConnection();

    if($connectionOK){
        $listaAlbum = $connection->getListaAlbum();
        if($listaAlbum != null){
            foreach($listaAlbum as $album){
                $stringaAlbum .= "<li><a class=\"FuoriDallHype\" id=\"".$album["idCss"]."\" href=\"../php/album.php?id=".$album["ID"]. "\">".$album["Titolo"]."</a></li>";
            }
        }
        $connection -> closeConnection();
    } else {
        $stringaAlbum = "<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio</p>";
    }

    $paginaHTML = str_replace("{album}", $stringaAlbum, $paginaHTML);
    echo $paginaHTML;
?>