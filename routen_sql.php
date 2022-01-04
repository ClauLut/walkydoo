<?php
    include "functions.php";
    
    $sth = $dbh->query("SELECT routen_id, meter, lat, lng FROM routen as r INNER JOIN koordinaten as k ON r.id = k.routen_id ORDER BY r.id ASC, k.idx ASC");

    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        echo $row['routen_id'], '$', $row['meter'], '$', $row['lat'], '$', $row['lng'],'%';
    }

    echo '0$0$0$0%';

    exit();
?>