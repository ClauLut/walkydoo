<?php
    include "functions.php";
    if (!isset($_SESSION['user']))
    {
        $pagetitle = "Login";
        include "header.php";
?>

<div class="container">
    <div class="login">
        <form action="login.php" method="post">
            <lable for="email">E-Mail</lable>
            <input type="mail" name="email" id="email" required/>
            <label for="passwd">Passwort:</label>  
            <input type="password" name="passwd" id="passwd" size="30" maxlength="64" required/>
            <input type="submit" class="button" name="login" value="Login"/>
        </form>
    </div>
    <div class="register">
        <p>Sie sind noch nicht registriert?</p>
        <a href="register.php">Jetzt registrieren</a>
    </div>
</div>


<?php
    if (isset($_POST["login"]))
    {
        $email = $_POST["email"];
        $pwd = $_POST["passwd"];

        $sth = $dbh->prepare("SELECT mail, passwort FROM profil WHERE mail LIKE ? LIMIT 1");
        $sth->execute(array($email));
        #$sth = $dbh->query("SELECT mail, passwort FROM profil WHERE mail LIKE '$email' LIMIT 1");
        $userdata =  $sth->fetch();

        if ($userdata == null)
        {
            echo "<p style='color: red;'>Login Fehlgeschlagen - Username und/oder Passwort falsch, versuchen Sie es erneut! </p>";
        }
        else if ($userdata->mail == $email)
        {
            $hash = $userdata->passwort;

            $options = [
                'cost' => COST
            ];

            if (password_verify($pwd . PEPPER, $hash))
            {
                if (password_needs_rehash($hash, PASSWORD_DEFAULT, $options))
                {
                    $new_hash = password_hash($pwd . PEPPER, PASSWORD_DEFAULT, $options);

                    $sth = $dbh->prepare("UPDATE profil SET passwort=? WHERE mail=?");
                    $sth->bind_param('ss', $new_hash, $email);
                    $sth->execute();
                }
                $_SESSION["user"] = $email;

                header("Location: profil.php");
                exit;
            }
        }
        else
        {
            echo "<p style='color: red;'>Login Fehlgeschlagen - Username und/oder Passwort falsch, versuchen Sie es erneut! </p>";
        }  
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