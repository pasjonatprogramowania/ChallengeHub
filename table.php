<?php

session_start();

if( $_SESSION['logged'] == true )
{
?>


<?php

session_start();

if ($_SESSION['logged'] == true) {

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];

        require_once "db.php";

        //Sprawdzenie czy osoba zaczeła wyzwanie
        $sthCheck = $pdo->prepare('SELECT count(*) FROM `users_challenges` WHERE `user_id` = :user_id AND `challenge_id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sthCheck->execute(array(':user_id' => $_SESSION['user_id'], ":id" => $id));
        $rowsCheck = $sthCheck->fetchAll(PDO::FETCH_NUM);

        if($rowsCheck[0][0] == 0)
        {
            header("Location: index.php");
        }
        //Sprawdzenie czy osoba zaczeła wyzwanie

        $sth = $pdo->prepare('SELECT `name`, `description`, `length`, `author_id`, `image_src`, `pkts`, `category_id` FROM `challenges` WHERE `id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute(array(':id' => $id));
        $rows = $sth->fetchAll(PDO::FETCH_NUM);

        //SELECT `username` FROM `users` INNER JOIN `users_challenges` ON `users_challenges`.`user_id` = `users`.`id` WHERE `users_challenges`.`challenge_id` = 33
        $sth3 = $pdo->prepare('SELECT `username`, `id` FROM `users` INNER JOIN `users_challenges` ON `users_challenges`.`user_id` = `users`.`id` WHERE `users_challenges`.`challenge_id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth3->execute(array(':id' => $id));
        $usersInChallenge = $sth3->fetchAll(PDO::FETCH_NUM);

        if (count($rows) == 1)
        {
            $sth2 = $pdo->prepare('SELECT `id`, `data`, `day` FROM `habits` WHERE `challenge_id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $sth2->execute(array(':id' => $id));
            $habits = $sth2->fetchAll(PDO::FETCH_NUM);

            $challengeName = $rows[0][0];

            ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Profil</title>
    <link href="main.css" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">
    <style>
    .box{
        /* background: lightblue; */
        height:10%;

    }
    .huge-flow-text{
        /* background: lightblue; */
        height:5%;
         font-size: 2.248rem;
         align: center;
    }
    nav .profile{
        position: relative;
        top:20px
    }
     /* .container {
  display: flex;  or inline-flex 
  flex-direction: row;
  flex-flow: column wrap;
  
                }  */
    *{
        color:white
    }
    input,label{
        color:white !important ;
    }
</style>
</head>
<body class='black'>
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

    <table class="highlight grey darken-4">
        <thead>
            <tr class="orange-text text-darken-1">
                <th>Miejsce</th>
                <th>Nick</th>
                <th>Ilość Punktów</th>
            </tr>
        </thead>

        <tbody class="orange-text text-darken-1">
            <?php
            $rating = array();
            $rating2 = array();
            $temp = 0;
            foreach ($usersInChallenge as $user)
            {
                $rating[$temp] = calcUserPoints($pdo, $user[1], $_GET['id']);
                $rating2[$temp] = array($user[1], $user[0]);
                $temp++;
            }
            arsort($rating);
            $num = 1;
            foreach ($rating as $key => $val)
            {
                $_user = $rating2[$key];
                ?>
                    <tr>
                        <td><?php echo $num; ?></td>
                        <td><?php echo $_user[1]; ?></td>
                        <td><?php echo $val; ?>pkt</td>
                    </tr>
                <?php
                $num++;
            }
            ?>
        </tbody>
    </table>



    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src='index.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
    <script>
        $(document).ready(function()
        {
            $('.sidenav').sidenav();

        })
        document.addEventListener('load', function () {
            console.log(localStorage.getItem('dark-mode'))
            console.log('aaa')
            if (localStorage.getItem('dark-mode')) {
                document.documentElement.classList.add('dark-mode')
            } else{
                document.documentElement.classList.remove('dark-mode')
            }
        });

        $.get('/api/?cmd=getUserPoints')
            .done(function(data) {
                document.querySelector('#punkty').value = data;
        });
    </script>
</body>
</html>


