<?php
    include "functions.php";
    if (!isset($_SESSION['user']))
    {
        $pagetitle = "Registrieren";
        include "header.php";
?>

<div class="container">
    <form action="register.php" method="post" enctype = "multipart/form-data">
        <p>Profilbild hinzufügen</p>
        <input type = "file" name = "bild">
        <p>Persönliche Daten</p><br>
        <lable for="firstname">Vorname</lable>
        <input type="text" name="firstname" id="firstname" required/>
        <lable for="lastname">Nachname</lable>
        <input type="text" name="lastname" id="lastname" required/>
        <lable for="email">E-Mail</lable>
        <input type="mail" name="email" id="email" required/>
        <lable for="user">Username</lable>
        <input type="text" name="user" id="user" required/>
        <label for="passwd">Passwort</label>  
        <input type="password" name="passwd" id="passwd" size="30" minlength="8" maxlength="64" required/>
        <label for="passwd">Passwort wiederholen</label> 
        <input type="password" name="passwd_again" id="passwd_again" size="30" minlength="8" maxlength="64" required/>
        <p>Mein Hund</p><br>
        <lable for="dogname">Name</lable>
        <input type="text" name="dogname" id="dogname" required/>
        <lable for="race">Rasse</lable>
        <input type="text" name="race" id="race" required/>
        <lable for="age">Geburtstag</lable>
        <input type="date" name="age" id="age" max=<?php echo date('Y-m-d');?> required/>
        <label for="text">Beschreibung</label>
        <textarea id="text" name="text" cols="35" rows="4"></textarea> 	
        <input type="submit" class="button" name="register" value="Registrieren"/>
    </form>
</div>
<?php


    if (isset($_POST["register"]))
    {
        //prüfen, ob mail einzigartig ist (primary key)
        $email = $_POST["email"];

        $sth = $dbh->query("SELECT mail FROM profil");
        $mailArray =  $sth->fetchAll();
        
        $unique = IsMailUnique($email, $mailArray);
        
        if ($unique == true) // wenn unique
        {
            $_SESSION["user"] = $email; // sessionUser setzen
        }
        else
        {
            echo "<p style='color: red;'>Es existiert bereits ein Profil für diese Mail-Adresse, bitte verwenden Sie eine andere Mail-Adresse! </p>";
            exit;
        }
        
        //Passwort hash 
        $pwd = $_POST["passwd"];
        $pwd2 = $_POST["passwd_again"];

        if ($pwd != $pwd2)
        {
            //Pwd stimmen nicht überein
            echo "<p style='color: red;'>Passwörter stimmen nicht überein!</p>";
            exit;
        }

        $hash = Pwd_Hash($pwd);

        //Profilbild hochladen
        if (!empty($_FILES["bild"]["name"]))
        {
            $filename = UploadFile();
        }
        else
        {
            $filename = "standard_profil.jpg";
        }

        //Insert in Tabelle profil
        $user = $_POST["user"];
        $vname = $_POST["firstname"];
        $nname = $_POST["lastname"];
        $dogname = $_POST["dogname"];
        $race = $_POST["race"];
        $age = $_POST["age"];
        $text = $_POST["text"];
 
        $sth = $dbh->prepare(
            "INSERT INTO profil
            VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $sth->execute(
            array(
                $email, $hash, $user, $vname, $nname, $dogname, $race, $age, $text, $filename
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
        echo "<p>Sie sind bereits eingeloggt!</p>";
        echo "<a href='index.php'>zurück zur Startseite</a>";
    } 
?>