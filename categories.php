<?php

session_start();

if ($_SESSION['logged'] == true)
{
    require_once 'db.php';

    $sth = $pdo->prepare('SELECT * FROM `categories`', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute();
    $rows = $sth->fetchAll(PDO::FETCH_NUM);
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="main.css" rel="stylesheet">
        <link rel="manifest" href="/manifest.json">
    <title>Kategorie</title>
</head>

<body class='black'>
    <style>


     h2,div{
         color:white;
     }
    </style>
<ul class="sidenav grey darken-4 white-text" id="mobile-links">
        <li><a  style='color:white'href="account.php">Witaj, <?php echo $_SESSION['username']; ?>!</a></li>
        <li><a  style='color:white'href="index.php">Wyzwania</a></li>
        <li><a  style='color:white'href="categories.php">Kategorie</a></li>
        <li><a  style='color:white'href="createChallenge.php">Dodaj swoje wyzwanie</a></li>
        <li><a  style='color:white'href="contact.php">Kontakt</a></li>
        <li><a  style='color:white'href="/index.php?cat=active">Aktywne</a></li>
        <li><a  style='color:white'href="logout.php">Wyloguj</a></li>
    </ul>
    <nav class="nav-wraper grey darken-4 white-text hide-on-small-only">
        <div class="container">
        <ul class="left">
        <li><a  style='color:white'href="index.php">Wyzwania</a></li>
        <li><a  style='color:white'href="categories.php">Kategorie</a></li>
        <li><a  style='color:white'href="createChallenge.php">Dodaj swoje wyzwanie</a></li>
        <li><a  style='color:white'href="contact.php">Kontakt</a></li>
        <li><a  style='color:white'href="/index.php?cat=active">Aktywne</a></li>

        </ul>
        <ul class="right">
            <li><a  style='color:white'href="account.php">Witaj, <?php echo $_SESSION['username']; ?>!</a></li>
            <li><a style='color:white'href="logout.php">Wyloguj</a></li>
                
        </ul>
        </div>
    </nav>
    <a href="#" class="sidenav-trigger hide-on-med-and-up" data-target="mobile-links">
        <i class="material-icons circle orange darken-1 grey-text text-darken-3">menu</i>
    </a>
        <!-- <div style=' display:flex; justify-content:flex-end'>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" onclick='turnOnDarkMode()' viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:32px;"><path     stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
        </div> -->
<div class="container">
    <h2 class="center">Kategorie Wyzwań</h2>
    <div class="collection center" style="border: none;">
        <?php
            foreach ($rows as $row)
            {
                echo '<a href="index.php?cat=' . $row[0] . '" style="margin-bottom: 5px; border: none; color:#EF921C !important" class="collection-item waves-effect grey darken-4 waves-light">' . $row[1] . '</a>';
            }
        ?>
      </div>
</div>
      <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src='index.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
    <script>
        $(document).ready(function()
        {
            $('.sidenav').sidenav();
        });

        document.addEventListener('load', function () {
            console.log(localStorage.getItem('dark-mode'))
            if (localStorage.getItem('dark-mode')) {
                document.documentElement.classList.toggle('dark-mode')
            }
        });

    </script>
</body>
</html>

<?php
}
else
{
    header("Location: login.php");
}

?>