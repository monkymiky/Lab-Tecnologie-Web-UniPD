<?php
    namespace DB;
    class DBAccess {
        private const HOST_DB = "localhost";
        private const DATABASE_NAME = "mnesler";
        private const USERNAME = "mnesler";
        private const PASSWORD = "Feif1eeng2Ea7nei";

        private $connection;

        public function openDBConnection(){
            $this -> connection = mysqli_connect(self::HOST_DB, self::USERNAME, self::PASSWORD, self::DATABASE_NAME);
            return mysqli_connect_errno()==0;
        }

        public function closeConnection(){
            mysqli_close($this -> connection);
        }

        public function getListaAlbum(){
            $query = "SELECT ID, Titolo, Copertina, idCss FROM Album ORDER BY DataPubblicazione DESC";
            $queryResult = mysqli_query($this -> connection, $query) or die("Errore in DBAccess ".mysqli_error($this->connection));
            if(mysqli_num_rows($queryResult)!=0){
                $result = array();
                while($row = mysqli_fetch_assoc($queryResult)){
                    $result[] = $row;
                }
                $queryResult -> free();
                return $result;
            } else {
                return null;
            }
        }

        public function getAlbum($id){
			$query = "SELECT Album.ID,
                    Album.Titolo,
                    Copertina,
                    DataPubblicazione,
                    SEC_TO_TIME(SUM(TIME_TO_SEC(Traccia.Durata))) as DurataAlbum
                    FROM Album
                    JOIN Traccia ON Album.ID=Traccia.Album
                    WHERE Album.ID=$id";
			$queryResult = mysqli_query($this -> connection, $query) 
											or die("Errore in DBAccess" .mysqli_error($this -> connection));
			if (mysqli_num_rows($queryResult) != 0){
				$row=mysqli_fetch_assoc($queryResult); //So già che mi restituisce una riga sola
				return array($row["Titolo"], $row["Copertina"], $row["DataPubblicazione"],
											$row["DurataAlbum"]);
			}else{
				return null;
			}
		}

        public function getTracceAlbum($id){
            $query = "SELECT Traccia.Titolo, 
                        Traccia.Esplicito,
                        Traccia.Durata,
                        Traccia.DataRadio,
                        Traccia.URLVideo
                        FROM Traccia  
                        JOIN  Album ON Traccia.Album = Album.ID
                        WHERE Album.ID = $id";
            
            $queryResult = mysqli_query($this -> connection, $query) or die("Errore in DBAccess" .mysqli_error($this -> connection));
            if (mysqli_num_rows($queryResult) != 0){
                $result = array();
                while($row = mysqli_fetch_assoc($queryResult)){
                    $result[] = $row;
                }
                $queryResult -> free();
                return $result;
            }else{
                return null;
            }
        }

        public function insertNewTrack($titolo, $durata, $esplicito, $dataRadio, $urlVideo, $album, $note){ //NULLIF($variabile, "stringa") inserisce NULL se la variabile è uguale alla stringa 
            $queryInsert = "INSERT INTO Traccia (Titolo, Durata, Esplicito,URLVideo, DataRadio, Album, Note)
                            VALUES (
                            \"$titolo\",
                            \"$durata\",
                            \"$esplicito\",
                            NULLIF(\"$urlVideo\", \"\"), 
                            NULLIF(\"$dataRadio\",\"\"),
                            $album,
                            NULLIF(\"$note\",\"\")
                            );";
            try{mysqli_query($this->connection, $queryInsert);}
            catch(Exception $ex){
                echo "Exception:" . $exception->getMessage(). "mysqli error : ". mysqli_error($this->connection); // forse è una ripetizione?
            }
            return mysqli_affected_rows($this->connection) >0;
        }
    }
?>