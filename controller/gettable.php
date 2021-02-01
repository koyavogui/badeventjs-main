<?php
    // var_dump($_POST);
    include './../database.php';
    $sql = "SELECT dangertype.intitule AS danger, sexeResponsable as responsable, sexeVictime as victime, source, date as date FROM ((danger INNER JOIN dangertype ON idDangerType = dangertype.id) INNER JOIN acteurs on sexeVictime = acteurs.id )WHERE  idPays=:pays AND idVille=:ville ORDER BY  date DESC";
    $result = $db->prepare($sql);
    $result->bindParam(':pays', $_POST["pays"] );
    $result->bindParam(':ville', $_POST["ville"] );
    $result->execute();
    $check = $result->fetchAll(PDO::FETCH_OBJ);
    if(count($check) == 0){
        echo ' <p class="container-fluid d-flex align-item-center content-justify-center">Pas d\'information enregistrées</p>';
    }else {
        @$recuppays = $db->query("SELECT * FROM pays ORDER BY nom ASC")->fetchAll(PDO::FETCH_OBJ);
        foreach($recuppays as $p){  
            if ( $p->id == $_POST["pays"]) {
                echo 'selected';
                $pays = $p->nom;
            }
        }   
        @$recupvilles= $db->query("SELECT * FROM ville ORDER BY ville ASC")->fetchAll(PDO::FETCH_OBJ);
        foreach($recupvilles as $v){
            if ( $v->id == $_POST["ville"] ) {
                $ville = $v->ville;
            }
        } 
        echo '<caption data-label="Donnée '.$ville.', '.$pays.'"> 
                les resultats de  '.$ville.'  ,  '.$pays.' 
        </caption> 
        <thead>
            <tr>
                <th scope="col">Danger</th>
                <th scope="col">Responsable(s)</th>
                <th scope="col">Victime(s)</th>
                <th scope="col">Date</th>
                <th scope="col">Source</th>
            </tr>
        </thead>
        <tbody>';
             
                foreach ($check  as $d) {
             
                echo '<tr>
                    <td class="text-capitalize">   '.$d->danger.'  </td>
                    <td>';
                         
                                @$res= $db->query("SELECT intitule FROM acteurs WHERE id = $d->responsable LIMIT 1 ")->fetch(PDO::FETCH_OBJ);
                                echo @$res->intitule;
                         
                    echo '</td>
                    <td>' ;
                         
                                @$res= $db->query("SELECT intitule FROM acteurs WHERE id = $d->victime LIMIT 1 ")->fetch(PDO::FETCH_OBJ);
                                echo @$res->intitule;
                         
                    echo '</td>
                    <td>';
                    $date = date_create($d->date);
                    echo date_format($date, 'd M Y');
                     echo '</td>
                    <td><a href="'.$d->source.'" target="blank"><i class="bi bi-link-45deg"></i></a></td>
                </tr>';
             }  
        echo '</tbody>';
    }

?>