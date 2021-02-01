<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
    include "./includes/styles.php"; 
    include './database.php'; 
    if (empty($_POST['ville']) || empty($_POST['pays']) ) {
        # code...
        header('Location: ./');
        exit();
    }
    ?>
    <title>Bad-Event</title>
</head>
<body class="" style="background-color: #fff;">
    <main class="">
        <header class="container d-flex justify-content-around">
            <div class="mt-3">
                <img src="./images/search.png" alt="" srcset="" class="img-fluid  p-2" style="width: 5rem;">
            </div>
            <section class="shadow-sm   ml-3 mt-3 p-2 pt-3 container" style="background-color: #fffde7;">
                <form action="" method="post" class="d-flex" id="secondSearch">
                    <select name="pays" id="pays" class="form-select mx-3 " style=" ">
                        <option value="">Choisissez un pays</option>
                        <?php
                            @$recuppays = $db->query("SELECT * FROM pays ORDER BY nom ASC")->fetchAll(PDO::FETCH_OBJ);
                            $ville = $pays = "";
                            foreach($recuppays as $p){
                                echo '<option value="'.$p->id .'"' ;
                                ?>
                                <?php
                                if ( $p->id == $_POST["pays"]) {
                                    echo 'selected';
                                    $pays = $p->nom;
                                }
                                echo '>'.$p->nom  .'</option>';
                            }      
                        ?>
                    </select>
                    <select name="ville" id="ville" class="form-select mx-auto " style=" ">
                    <option value="">Choisissez un pays</option>
                            <?php
                                @$recupvilles= $db->query("SELECT * FROM ville ORDER BY ville ASC")->fetchAll(PDO::FETCH_OBJ);
                                foreach($recupvilles as $v){
                                    echo '<option value="'. $v->id  .'"';
                                    ?>
                                    <?php
                                    if ( $v->id == $_POST["ville"] ) {
                                        echo 'selected';
                                        $ville = $v->ville;
                                    }
                                    echo '>'.$v->ville.'</option>';
                                } 
                            ?>
                    </select>
                <button type="submit" class="btn btn-danger mx-3"> Rechercher </button>
             </form>
            </section>
        </header>
        <section class="container-fluid mt-5 py-3">
            <section id="menu" class=" d-flex nav-item ">
            <a href=" " class="link-dark" style="text-decoration: none;"><div class="px-3 py-2 border-bottom border-danger border-3  text-danger" id="all"><i class="bi bi-search"></i> Tous</div></a>
            <a href=" " class="link-dark" style="text-decoration: none;"><div class="px-3 py-2" id="tab"><i class="bi bi-table"></i> Tableau</div></a>
            <a href=" " class="link-dark" style="text-decoration: none;"><div class="px-3 py-2" id="chart"><i class="bi bi-graph-up"></i> Graphe</div></a>
            <a href=" " class="link-dark" style="text-decoration: none;"><div class="px-3 py-2" id="map"><i class="bi bi-compass"></i> Map</div></a>
            </section>
            <section id="corps" class="container-fluid border-top pb-3" style="border-color: #fbe9e7 !important;">
                    <section id="tab-body">
                        <div class="table-responsive mt-2 ">
                        <table class="table caption-top" id="result">
                        <caption data-label="Donnée <?php echo $ville; ?> , <?php echo $pays ?>"> 
                             les resultats de <?php echo $ville; ?> , <?php echo $pays ?> 
                        </caption>
                        <?php
                            $sql = "SELECT dangertype.intitule AS danger, sexeResponsable as responsable, sexeVictime as victime, source, danger.dateAjout as date FROM ((danger INNER JOIN dangertype ON idDangerType = dangertype.id) INNER JOIN acteurs on sexeVictime = acteurs.id )WHERE  idPays=:pays AND idVille=:ville";
                            $result = $db->prepare($sql);
                            $result->bindParam(':pays', $_POST["pays"] );
                            $result->bindParam(':ville', $_POST["ville"] );
                            $result->execute();
                            $check = $result->fetchAll(PDO::FETCH_OBJ);
                            // var_dump($check);
                        ?>
                                <thead>
                                    <tr>
                                        <th scope="col">Danger</th>
                                        <th scope="col">Responsable(s)</th>
                                        <th scope="col">Victime(s)</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Source</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        foreach ($check  as $d) {
                                    ?>
                                        <tr>
                                            <td class="text-capitalize"> <?php echo $d->danger ?></td>
                                            <td>
                                                <?php
                                                     @$res= $db->query("SELECT intitule FROM acteurs WHERE id = $d->responsable LIMIT 1 ")->fetch(PDO::FETCH_OBJ);
                                                     echo @$res->intitule;
                                                ?>
                                            </td>
                                            <td> 
                                                <?php
                                                     @$res= $db->query("SELECT intitule FROM acteurs WHERE id = $d->victime LIMIT 1 ")->fetch(PDO::FETCH_OBJ);
                                                     echo @$res->intitule;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                     $date = date_create($d->date);
                                                     echo date_format($date, 'd M Y'); 
                                                ?>
                                            </td>
                                            <td><a href="<?php echo $d->sources ?>" target="blank"><i class="bi bi-link-45deg"></i></a></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                    <section id="chart-body" class="mx-auto " style="position: relative; margin: auto;  height: 80vh;width: 80vw;">
                        <canvas id="mongraph" ></canvas>
                    </section>
                    <section id="map-body" class="mx-auto" style="width:100%;height:600px;">
                    </section>
                     
            </section>
        </section>
    </main>
    <?php include "./includes/script.php";?>
    <script>
        <?php
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
        ?>
             
                
                // var mymap = L.map('map-body').setView([5.36507,-4.02357], 15);

                var mymap = L.map('map-body', {
                    center: [5.36507,-4.02357],
                    zoom: 15
                });
                L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                    attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>',
                    maxZoom: 20,
                    id: 'mapbox/streets-v11',
                    tileSize: 512,
                    zoomOffset: -1
                }).addTo(mymap);
                var marker = L.marker([5.36507, -4.02357]).addTo(mymap);
                // map(5.36507, -4.02357);
                console.log(marker);
                var ctx = $('#mongraph');
                var monGraph = new Chart(ctx, {
                    type: 'bar',
                    data : {    
                            labels:  [<?php 
                             $length = count($dangertype);
                                    for ($i=0; $i <  $length ; $i++) { 
                                        if ($i !== ($length - 1)){
                                            echo '\''.$dangertype[$i].'\',';
                                        }else{
                                            echo '\''.$dangertype[$i].'\'';
                                        }
                                    }
                                ?> ],
                            datasets: [{
                                label: 'Resultat',
                                data:  [<?php 
                                    $length = count($stat);
                                    for ($i=0; $i < $length ; $i++) { 
                                        if ($i !== ($length - 1)){
                                            echo $stat[$i].',';
                                        }else{
                                            echo $stat[$i];
                                        }
                                    }
                                ?> ],
                                backgroundColor: 'rgba(255, 107, 122, 0.4)' ,
                                borderColor: "#ff6b7a",
                                borderWidth: 1,
                                barPercentage: 0.5,
                                
                            }]
                        },
                        options: {
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                },
                                // stacked: true,
                                gridLines: {
                                    display: true,
                                    color: "rgba(255,99,132,0.2)"
                                }
                            }]
                         }
                        }
                    });
                //  });
        $(document).ready(function () {

            $("#all").click(function (e) { 
                e.preventDefault();
                $("#all").addClass("border-bottom border-danger border-3  text-danger");
                $("#tab").removeClass("border-bottom border-3   text-danger");
                $("#map").removeClass("border-bottom border-3  text-danger");
                $("#chart").removeClass("border-bottom border-3  text-danger");
                $("#tab-body").show();
                $("#chart-body").show();
                $("#mongraph").show();
                $("#map-body").show();
            });
            $("#tab").click(function (e) { 
                e.preventDefault();
                $("#tab").addClass("border-bottom border-danger border-3  text-danger");
                $("#all").removeClass("border-bottom border-3  text-danger");
                $("#map").removeClass("border-bottom border-3  text-danger");
                $("#chart").removeClass("border-bottom border-3  text-danger");
                $("#tab-body").show();
                // $("#chart-body").hide();
                $("#mongraph").hide();
                $("#map-body").hide();
            });
            $("#chart").click(function (e) { 
                e.preventDefault();
                $("#all").removeClass("border-bottom border-3  text-danger");
                $("#tab").removeClass("border-bottom border-3  text-danger");
                $("#map").removeClass("border-bottom border-3  text-danger");
                $("#chart").addClass("border-bottom border-danger border-3  text-danger");
                $("#tab-body").hide();
                $("#chart-body").show();
                $("#mongraph").show();
                $("#map-body").hide();
            });
            $("#map").click(function (e) { 
                e.preventDefault();
                $("#all").removeClass("border-bottom border-3  text-danger");
                $("#tab").removeClass("border-bottom border-3  text-danger");
                $("#map").addClass("border-bottom border-danger border-3  text-danger");
                $("#chart").removeClass("border-bottom border-3  text-danger");
                $("#tab-body").hide();
                $("#chart-body").hide();
                $("#map-body").show();
            });

            
        });
    </script>
    <?php unset($_POST['ville']); unset($_POST['pays']) ?>
</body>
</html>