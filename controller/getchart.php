<?php
    include './../database.php';

      
         
            $sql='SELECT COUNT(idDangerType) as nombreDanger, idDangerType, intitule FROM (danger INNER JOIN dangertype ON idDangerType = dangertype.id ) WHERE idVille =:ville GROUP BY iddangertype' ; 
            $result = $db->prepare($sql);
            $result->bindParam(':ville', $_POST['ville']);
            $result->execute();
            $checks = $result->fetchAll(PDO::FETCH_OBJ);
            $dangertype = $stat = array();
            foreach ($checks as $d) {
                $dangertype[] = $d->intitule;
                $stat[] = $d->nombreDanger;
            }
         
        echo json_encode (array('label' =>$dangertype, 'data'=>$stat));
    
?>