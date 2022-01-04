<?php
    include "functions.php";
    
    if (isset($_SESSION['user']))
    {
        include "header.php";
        $pagetitle = "Logout";
?>

<div class="container">
    <form action="logout.php" method="post">
        <input type="submit" value="Logout"/>
    </form>
</div>

<?php
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
?>

<?php
    include "footer.php";
    }
    else 
    {
        echo "<p> Sie müssen angemeldet sein, um diese Seite zu besuchen!</p>";
    }  
?>
