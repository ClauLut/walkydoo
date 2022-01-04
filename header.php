<!DOCTYPE html>
<html lang="DE">
<head>
  <meta charset="UTF-8">
  <title>Walkydoo - <?php echo $pagetitle ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav>
    <img class="logo" src="img/logo.png" alt="walkydoo logo">
    <ul>
      <li><a href="index.php">HOME</a></li>
      <?php
        if (isset($_SESSION['user']))
        {
      ?>
      <li><a href="routen_edit.php">ROUTEN</a></li>
      <li><a href="profil.php">PROFIL</a></li>
      <li><a href="logout.php">LOGOUT</a></li>
      <?php
        }
        else
        {
      ?>
      <li><a href="routen.php">ROUTEN</a></li>
      <li><a href="login.php">LOGIN</a></li>
      <?php
        }
      ?>
    </ul>
  </nav>
  <main>
