<?php
    // var_dump($_POST);
    include './../database.php';

    $sql = " SELECT * FROM ville WHERE pays =:idpays ORDER BY ville ASC";
    $result = $db->prepare($sql);
    $result->bindParam(':idpays', $_POST['selectedCountry']);
    $result->execute();
    $check = $result->fetchAll(PDO::FETCH_OBJ);
    echo' <option value=" "> -- Choisir la ville -- </option>';
    foreach ($check as $ville) {
        echo '<option value="'.$ville->id.'">'.ucwords($ville->ville).'</option>';
    }
?>