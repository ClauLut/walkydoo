<?php
include "functions.php";
    if (isset($_SESSION['user']))
    {
        $pagetitle = "Löschen";
        $sUser = $_SESSION["user"];
        include "header.php";
?>

<?php
    $sth = $dbh->prepare("SELECT profilbild FROM profil WHERE mail=?");
    $sth->execute(array($sUser));
    $filename =  $sth->fetch();
    echo("<script>console.log('PHP: " .$filename->profilbild ."');</script>");
    delete_File($filename->profilbild);
    
    $sth = $dbh->prepare("DELETE FROM profil WHERE mail=?");
    $sth->execute(array($sUser));

    $_SESSION = array();

    if (isset($_COOKIE[session_name()])) 
    {
    setcookie(
        session_name(),
        '',
        time()-42000,
        '/'
        );
    }
    session_destroy();
    
    header("Location: index.php");
    exit;
?>

<?php
    include "footer.php";
    }
    else 
    {
        echo "<p> Sie müssen angemeldet sein, um diese Seite zu besuchen!</p>";
    }  
?>