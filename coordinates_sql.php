<?php
    include "functions.php";
    $lat = $_GET['lat'];
    $lng = $_GET['lng'];
    $routen_id = $_GET['routen_id'];
    $index = $_GET['index'];

    $sth = $dbh->prepare(
        "INSERT INTO koordinaten
        VALUES ( DEFAULT, ?, ?, ?, ?)"
    );

    $sth->execute(
        array(
            $lat, $lng, $routen_id, $index
        )
    );
    
    exit();
?>