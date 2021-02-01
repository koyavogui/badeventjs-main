<?php
    include './../database.php';

      
         
            $sql='SELECT  lat, lng FROM ville WHERE id=:ville' ; 
            $result = $db->prepare($sql);
            $result->bindParam(':ville', $_POST['ville']);
            $result->execute();
            $checks = $result->fetchAll(PDO::FETCH_OBJ);
            foreach ($checks as $d) {
                $map = array('lng' =>$d->lng, 'lat'=>$d->lat);
            }
         
        echo json_encode ($map);
    
?>