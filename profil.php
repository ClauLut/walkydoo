<?php
    include "functions.php";

    if (isset($_SESSION["user"]))
    {
        $pagetitle = "Profil";
        $sUser = $_SESSION["user"];
        include "header.php";

        $sth = $dbh->prepare("SELECT username, profilbild, hundename, rasse, geburtsdatum, beschreibung  FROM profil WHERE mail=?");
        $sth->execute( array( $sUser ) );
        $userdata = $sth->fetch();

        $age = Age($userdata->geburtsdatum);
?>
<div class="container">
    <div class="profil">
        <h2><?php echo $userdata->username ?></h2>
        <img class = "profilbild" src = "profilbilder/<?php echo $userdata->profilbild ?>" alt = "profilbild">
        <p><?php echo $userdata->rasse ?> <?php echo $userdata->hundename ?></p>
        <p><?php echo $age ?> Jahre alt</p>
        <p><?php echo $userdata->beschreibung ?></p>

        <form action="profil_edit.php" method="post">
            <input type="submit" class="button" name="edit" value="Bearbeiten"/>
        </form>
        <button onclick=Delete() id="button" class= "button" name="delete">Löschen</button>
    </div>

    <div class="hidden_delete">
        <p>Dein Profil wird unwiderruflich gelöscht - bist du sicher, dass du das willst?
        <form action="profil_delete.php" method="post">
            <input type="submit" id="button" class="button" name="delete" value="Ja"/>
        </form>
        <form action="" method="post">
            <input type="submit" id="button" class="button" name="delete" value="Nein"/>
        </form>
    </div>
</div>

<script>
    function Delete()
    {
        // Buttons show/hide
        document.getElementsByClassName("button")[0].style.display = "none";
        document.getElementsByClassName("button")[1].style.display = "none";
        document.getElementsByClassName("hidden_delete")[0].style.display = "block";
    }
</script>

<?php
    include "footer.php";
    }
    else 
    {
        echo "<p> Sie müssen eingeloggt sein, um diese Seite zu besuchen!</p>";
        echo "<a href='login.php'>zum Login</a>";
    }  
?>