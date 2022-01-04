<?php
    include "functions.php";
    if (isset($_SESSION['user']))
    {
        $pagetitle = "Bearbeiten";
        $sUser = $_SESSION["user"];
        include "header.php";

        $sth = $dbh->prepare("SELECT * FROM profil WHERE mail=?");
        $sth->execute( array( $sUser ) );
        $userdata = $sth->fetch();
?>
<div class="container">
    <h2>Profil anpassen</h2>
    <div class="anpassen">
        <form action="profil_edit.php" method="post" enctype = "multipart/form-data">
            <p>Profilbild hinzufügen</p>
            <input type = "file" name = "bild">
            <p>Persönliche Daten</p><br>
            <lable for="user">Username</lable>
            <input type="text" name="user" id="user" value="<?php echo htmlspecialchars($userdata->username) ?>" required/>
            <lable for="firstname">Vorname</lable>
            <input type="text" name="firstname" id="firstname" value="<?php echo htmlspecialchars($userdata->vorname) ?>" required/>
            <lable for="lastname">Nachname</lable>
            <input type="text" name="lastname" id="lastname" value="<?php echo htmlspecialchars($userdata->nachname) ?>" required/>
            <lable for="email">E-Mail</lable>
            <input type="mail" name="email" id="email" value="<?php echo htmlspecialchars($userdata->mail) ?>" required/>
            <label for="passwd">Passwort</label>  
            <input type="password" name="passwd" id="passwd" size="30" minlength="8" maxlength="64"/>
            <p>Mein Hund</p><br>
            <lable for="dogname">Name</lable>
            <input type="text" name="dogname" id="dogname" value="<?php echo htmlspecialchars($userdata->hundename) ?>" required/>
            <lable for="race">Rasse</lable>
            <input type="text" name="race" id="race" value="<?php echo htmlspecialchars($userdata->rasse) ?>" required/>
            <lable for="age">Geburtstag</lable>
            <input type="date" name="age" id="age" value="<?php echo htmlspecialchars($userdata->geburtsdatum) ?>"required/>
            <label for="text">Beschreibung</label>
            <textarea id="text" name="text" cols="35" rows="4" value="<?php echo htmlspecialchars($userdata->beschreibung) ?>"><?php echo htmlspecialchars($userdata->beschreibung) ?></textarea> 	
            <input type="submit" class="button" name="safe" value="Speichern"/>
            <input type="submit" class="button" name="back" value="Abbrechen"/>
        </form>
    </div>
</div>
<?php
    // Abbrechen
    if (isset($_POST["back"]))
    {
        header("Location: profil.php");
        exit;
    }

    // Profil in DB updaten
    if (isset($_POST["safe"]))
    {
        //Profilbild hochladen oder altes ersetzen
        if (!empty($_FILES["bild"]["name"]))
        {
            $filename = UploadFile();

            $sth = $dbh->prepare(
                "UPDATE profil SET profilbild=? WHERE mail=?"
            );
    
            $sth->execute(
                array( $filename, $sUser )
            );
        }

        // Email prüfen
        $email = $_POST["email"];

        if ($email != $sUser) // wenn geändert
        {
            $sth = $dbh->query("SELECT mail FROM profil");
            $mailArray =  $sth->fetchAll();
            
            $unique = IsMailUnique($email, $mailArray);

            if ($unique == true) //prüfen ob email unique
            {
                $sth = $dbh->prepare( //dann update
                    "UPDATE profil
                    SET mail=?
                    WHERE mail=?"
                );
        
                $sth->execute(
                    array(
                        $email, $sUser 
                    )
                );

                $_SESSION["user"] = $email; //sessionUser neu setzen
            }
            else
            {
                echo "<p style='color: red;'>Es existiert bereits ein Profil für diese Mail-Adresse, bitte verwenden Sie eine andere Mail-Adresse! </p>";
                exit;
            }
        }

        if (!empty($_POST["passwd"])) {

            //Passwort hash 
            $pwd = $_POST["passwd"];

            $hash = Pwd_Hash($pwd);

            $sth = $dbh->prepare(
                "UPDATE profil
                    SET passwort=?
                    WHERE mail=?"
            );
    
            $sth->execute(
                array(
                    $hash, $_SESSION["user"]
                )
            );

        }
        
        $user = $_POST["user"];
        $vname = $_POST["firstname"];
        $nname = $_POST["lastname"];
        $dogname = $_POST["dogname"];
        $race = $_POST["race"];
        $age = $_POST["age"];
        $text = $_POST["text"];

        // Datenbank updaten
        $sth = $dbh->prepare(
            "UPDATE profil
            SET username=?, vorname=?, nachname=?, hundename=?, rasse=?, geburtsdatum=?, beschreibung=?
            WHERE mail=?"
        );

        $sth->execute(
            array(
                $user, $vname, $nname, $dogname, $race, $age, $text, $email 
            )
        );

        header("Location: profil.php");
        exit;
    }
?>
<?php
    include "footer.php";
    }
    else 
    {
        echo "<p> Sie müssen angemeldet sein, um diese Seite zu besuchen!</p>";
        echo "<a href='login.php'>zum Login</a>";
    }  
?>