<?php
    include "functions.php";
    $pagetitle = "Home";
    include "header.php";

    $sth = $dbh->query("SELECT * FROM profil ORDER BY RANDOM() LIMIT 3");
    $userdata = $sth->fetchAll();
?>

<div class="container">
    <p>Entdecke <a href="routen.php">Gassi-Routen</a> in deiner Nähe!</p>
    <p>Oder zeig uns deine eigenen Lieblings-Routen!</p>
</div>
<!-- 3 random Profile anzeigen -->
<?php if (!isset($_SESSION['user'])){ ?>
<div class="container">
    <p><a href="register.php">Registriere</a> dich jetzt und erstelle ein Profil für dich und deinen Hund!</p>
    
    <div class="show_profiles">
    <?php
    if ($userdata != null) 
    { 
        foreach ($userdata as $ud) 
        { 
    ?>
    <div class="profiles">
        <h2><?php echo $ud->username ?></h2>
        <img class = "profilbild" src = "profilbilder/<?php echo $ud->profilbild ?>" alt = "profilbild">
        <p><?php echo $ud->rasse ?> <?php echo $ud->hundename ?></p>
        <p><?php echo Age($ud->geburtsdatum) ?> Jahre alt</p>
        <p><?php echo $ud->beschreibung ?></p>
    </div>
    <?php 
        }
    } 
    ?>
    </div>
</div> 
<?php } ?>

<div class="container">
    <p>Alleine Gassi-Gehen ist dir zu langweilig? Verabrede dich jetzt auf ein <a href="https://users.multimediatechnology.at/~fhs45901/dogdate">Dog-Date</a>!</p>
</div>

<?php
    include "footer.php";
?>

