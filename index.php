<?php

session_start();

if ($_SESSION['logged'] == true) {

    ?>


<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Wyzwania</title>
            <link href="main.css" rel="stylesheet">
            <link rel="manifest" href="/manifest.json">

</head>
<style>
    div{
        border-radius: 0px !important 
    }
 
</style>
<body class='black'>
<div id="chId" style="display: none;"><?php echo $_GET['id']; ?></div>    


    <!--    nav     -->
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
    </a>        <!-- <div style=' display:flex; justify-content:flex-end'>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" onclick='turnOnDarkMode()' viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:32px;"><path     stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
        </div> -->
    <!--    wyzwania    -->


    <div class="container">

        <?php

    require_once "db.php";

    $isCat = false;
    $isActive = false;

    if(isset($_GET['cat']) && !empty($_GET['cat']))
    {
        $cat = $_GET['cat'];
        if($cat == 'active')
        {
            echo '<h4 class="grey-text" style="margin-left: 20px;">Aktywne wyzwania</h4>';
            $isActive = true;
        }
        else
        {
            $sthCat = $pdo->prepare('SELECT * FROM `categories` WHERE `id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $sthCat->execute(array('id' => $cat));
            $rowsCat = $sthCat->fetchAll(PDO::FETCH_NUM);

            if(count($rowsCat) == 1)
            {
                echo '<h4 class="grey-text" style="margin-left: 20px;">' . $rowsCat[0][1] . '</h4>';
                $isCat = true;
            }
        }
    }

    $sthStarted = $pdo->prepare('SELECT `challenge_id` FROM `users_challenges` WHERE `user_id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sthStarted->execute(array(':id' => $_SESSION['user_id']));
    $rowsStarted = $sthStarted->fetchAll(PDO::FETCH_NUM);

    $startedChallenges = array();

    foreach ($rowsStarted as $row)
    {
        array_push($startedChallenges, intval($row[0]));
    }

    $sql = 'SELECT * FROM `challenges`';
    $sth = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute();
    $rows = $sth->fetchAll(PDO::FETCH_NUM);
 
    foreach ($rows as $row) {
        $id = $row[0];
        $name = $row[1];
        $desc = $row[2];
        $len = $row[3];
        $author_id = $row[4];
        $image_src = $row[5];
        $_cat = $row[7];

        $started = in_array(intval($id), $startedChallenges);

        if($isActive)
        {
            if(!$started)
                continue;
        }
        elseif ($isCat)
        {
            if($_cat != $cat)
                continue;
        }
        else
        {
            if($started)
                continue;
        }

        ?>
        <div class="row">
            <div class="col s12 m9 l6">
                <div class="card black">
                    <?php
                        if($started)
                        {
                            echo '<a href="challenge.php?id=' . $id . '" style="color: rgba(0,0,0,0.87);">';
                        }
                    ?>
                        <div class="card-image">
                            <img src="<?php echo ($image_src); ?>" height="120px" class="cut_image">
                        </div>

                        <div class="card-content grey darken-4" width="400px" >
                            <span class="card-title" style="color: #FAFAFA;"><?php echo ($name); ?></span>

                        </div>
                    <?php
                        if($started)
                        {
                            echo '</a>';
                        }
                    ?>
                    <div class="card-action grey darken-4">
                        <?php
                            if($started == true)
                            {
                                echo '<a href="challenge.php?id=' . $id . '" class="btn transparent orange-text" style="box-shadow: none!important;">Otwórz wyzwanie</a>
                                <a class="waves-effect waves-light btn modal-trigger transparent orange-text" style="box-shadow: none!important;" href="#delInfo' . $id . '">Zakończ wyzwanie</a>';
                            }
                            else
                            {
                                echo '<a href="startChallenge.php?id=' . $id . '" class="btn transparent orange-text" style="box-shadow: none!important;">Zacznij Wyzwanie</a>
                                <a class="waves-effect waves-light btn modal-trigger transparent orange-text" style="box-shadow: none!important;" href="#info' . $id . '">info</a>';
                            }
                        ?>
                        
                        <div id="info<?php echo $id; ?>" class="modal">
                            <div class="modal-content">
                                <h4 class='challengeTitle'><?php echo ($name); ?></h4>
                                <div class='challengeDesc'><?php echo ($desc); ?></div>
                            </div>
                            <div class="modal-footer">
                                <a href="#!" class="btn modal-close waves-effect waves-light green">Ok</a>
                            </div>
                        </div>
                        <div id="delInfo<?php echo $id; ?>" class="modal">
                            <div class="modal-content">
                                <h4 class='challengeTitle'>Czy napewno chcesz zakończyć to wyzwanie?</h4>
                                <div class='challengeDesc'>UWAGA! Stracisz wszystkie puknkty za to wyzwanie.</div>
                            </div>
                            <div class="modal-footer">
                                <a href="stopChallenge.php?id=<?php echo $id; ?>" class="btn modal-close waves-effect waves-light green">Ok</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php

    }

    ?>


        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.modal').modal();
    })

    $(document).ready(function() {
        $('.sidenav').sidenav();
    })

    document.addEventListener('load', function () {
        if (localStorage.getItem('dark-mode')) {
            document.documentElement.classList.toggle('dark-mode')
        }
    });
    </script>
        </div>
        <div>
        </div>
    </div>
    <script src='index.js'></script>
    <script src='app.js'></script>
</body>

</html>

<?php
} else {
    header("Location: login.php");
}

?>