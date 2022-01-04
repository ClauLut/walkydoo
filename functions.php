<?php
    ini_set('display_errors', true);

    $pagetitle = "no pagetitle set";

    require "config.php";

    session_start();

    if ( ! $DB_NAME ) die ('please create config.php, define $DB_NAME, $DSN, $DB_USER, $DB_PASS there. See config_sample.php');

    try {
        $dbh = new PDO($DSN, $DB_USER, $DB_PASS);
        $dbh->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    } catch (Exception $e) {
        die ("Problem connecting to database $DB_NAME as $DB_USER: " . $e->getMessage() );
    }

    function UploadFile()
    {
        $uploaddir = dirname( $_SERVER["SCRIPT_FILENAME"] ) . "/profilbilder/";
    
        $filename = basename($_FILES['bild']['name']);
        $ext = substr($filename, -4);
        
        if( $ext != '.jpg' && $ext != '.svg' && $ext != '.png') {
        die("Bitte nur jpg-, svg- oder png-Dateien hochladen, nicht " . $ext );
        }

        $uploadfile = $uploaddir . $filename;

        if (move_uploaded_file($_FILES['bild']['tmp_name'], $uploadfile)) {
        echo "Datei erfolgreich gespeichert.\n";
        } else {
        echo "Problem beim Speichern der Datei.\n";
        }

        echo '<pre> debugging info:';
        print_r($_FILES);
        print '</pre>';

        return $filename;
    }

    function IsMailUnique($email, $mailArray)
    {
        foreach ($mailArray as $m)
        {
            if ($email == $m->mail)
            {
                return false;
            }
        }
        return true;
    }

    function Pwd_Hash($pwd)
    {
        $hash = password_hash($pwd . PEPPER, PASSWORD_BCRYPT, [
            'cost' => COST
        ]);

        if (!$hash) {
            echo "<p style='color: red;'>Registrierung Fehlgeschlagen, versuchen Sie es später erneut! </p>";
            exit;
        }

        return $hash;
    }

    function delete_File($filename)
    {
        $uploaddir = dirname( $_SERVER["SCRIPT_FILENAME"] ) . "/profilbilder/";
        $uploadfile = $uploaddir . $filename;
        // echo("<script>console.log('PHP: " .$uploadfile ."');</script>");
        if (unlink($uploadfile)) {
            echo "Datei erfolgreich gelöscht.\n";
            } else {
            echo "Problem beim Löschen der Datei.\n";
            }
    }

    function Age ($date)
    {
        // berechne das Alter, Datumsformat ist YYYY-MM-DD
        $birthDate = explode("-", $date);
        $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[2], $birthDate[0]))) > date("md")
            ? ((date("Y") - $birthDate[0]) - 1)
            : (date("Y") - $birthDate[0]));
        return $age;
    }
?>
