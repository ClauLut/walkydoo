<?php
    include "functions.php";
    $c = $_GET['c'];
    $routenid = $_GET['routenid'];
    
    $date = new DateTime();
    // kommentar einfügen
    if($c == 1) {

        $comment = $_GET['comment'];
        $userid = $_GET['userid'];

        $sth = $dbh->prepare(
            "INSERT INTO kommentare
            VALUES ( DEFAULT, ?, ?, ?, ? )"
        );
    
        $sth->execute(
            array(
                $date, $comment, $userid, $routenid
            )
        );
    }
    // Kommentare laden
    else {
        $sth = $dbh->prepare(
            "SELECT k.datum, k.kommentar, p.username, p.profilbild FROM kommentare AS k INNER JOIN profil AS p ON k.username = p.username"
        );
    
        $sth->execute(
            array(
                $routenid
            )
        );

        while ($commentdata = $sth->fetch(PDO::FETCH_ASSOC)) {
            echo '', $commentdata['datum'], '-', $commentdata['kommentar'], '-', $commentdata['username'], '-', $commentdata['profilbild'],'%';
        }
    }

    exit();
?>