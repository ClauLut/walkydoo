<?php
    include "functions.php";
    $q = $_GET['q'];

    $sth = $dbh->prepare(
        "INSERT INTO routen
        VALUES ( DEFAULT, ? )"
    );

    $sth->execute(
        array(
            $q
        )
    );

    $routen_id = $dbh->lastInsertId();

    echo $routen_id;

    exit();
?>