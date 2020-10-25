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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Wyzwania</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="main.css" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">
</head>

<body class="black">
    <style>
    a {
        cursor: pointer;
    }
    td,
    th {
        width: 25%;
        text-align: center;
    }

    .white-important {
        color: white !important;
    }
    p,span{
        color:white !important;
    }
    </style>
    <!--    nav     -->
<div id="chId" style="display: none;"><?php echo $_GET['id']; ?></div>    
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
        <!-- <div style='background-color:white; display:flex; justify-content:flex-end'>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" onclick='turnOnDarkMode()' viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:32px;"><path     stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
        </div> -->
    <!-- content -->
    <div class="center black">
        <div class="row">
            <div class="col s12 black white-text">
                <h2 style='color:white'> <?php echo ($challengeName); ?> </h2>
                <p class="flow-text" id="daysPast"></p>
                <div class="progress orange lighten-2" style="height: 20px">
                    <div id='progressBar' class="determinate orange darken-1"></div>
                </div>
            </div>
        </div>
            <div class="row  orange darken-1">
                <div class="col s12 slider">
                    <?php

            foreach ($habits as $habit) {
                //`id`, `data`, `day`

                ?>
                    <div class="slide small white-text text-lighten-2">
                        <div class="card  grey darken-3">
                            <div class="card-content white-text">
                                <span style='color:white'class="card-title white-text"> <?php echo getDayName($habit[2]); ?> </span>
                                <p> <?php echo $habit[1]; ?> </p>
                                <div class="progress orange lighten-2">
                                    <div class="determinate  orange darken-2" name="<?php echo $habit[0]; ?>"
                                        style="width: 0%"></div>
                                </div>
                                <p class="flow-text" name="<?php echo $habit[0]; ?>"></p>
                            </div>
                            <div class="card-action grey darken-4">
                                <a href="#" onclick="addPoint( <?php echo $habit[0]; ?> ,event)" name="<?php echo $habit[0]; ?>">
                                    <svg style="width: 35px" class="w-6 h-6" fill="none" stroke="white "
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7"></path>
                                    </svg>
                                </a>
                                <a onclick="subtractPoint( <?php echo $habit[0]; ?> ,event)" name="<?php echo $habit[0]; ?>">
                                    <svg style="width: 35px" class="w-6 h-6" fill="none" stroke="white "
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
}
            ?>
                </div>
                <div class="divider"></div>
            </div>
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
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
            <script src='index.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>

    let challengeId = document.querySelector('#chId').innerHTML;
    document.addEventListener('load', function () {
        console.log(localStorage.getItem('dark-mode'))
        if (localStorage.getItem('dark-mode')) {
            document.documentElement.classList.toggle('dark-mode')
        }
    });
    document.addEventListener('DOMContentLoaded', function() {

        var elems = document.querySelectorAll('.sidenav');
        var instances = M.Sidenav.init(elems);

        $.post('/api/?cmd=getChallenge', {
            challengeId: challengeId
        }).done(function(data) {
            let dataChallenge = data;
            $.post('/api/?cmd=getUserChallengeStartTime', {
                challengeId: challengeId
            }).done(function(dataTime) {
                let timeStartChallenge = dataTime
                console.log(dataTime)
                $.get('/api/?cmd=getServerTime')
                .done(function(serverTime) {
                    onSet(dataChallenge,dataTime.startTime,serverTime.timestamp)
                });
            })                              
        });
    });

    let week = [];
    let weekId =[];
    let procent = [];
    let time;
    let dayPast = 3;

    function onSet(data, dataTime,serverTime) {
        console.log('data',data)
        console.log('dataTime',dataTime)
        console.log('serverTime',serverTime)
        data.habits.forEach((item, index) => {
            week[index] = parseInt(item[3]);
            weekId[index] = parseInt(item[0]);
            procent[index] =  parseInt((item[3] / time) * 100);
        });
        time = data.len;
        let subtract = 24 *60 *60;
        dayPast = Math.floor(( parseInt(serverTime) - parseInt(dataTime)) /subtract);
        if(dayPast > time){
            dayPast = time;
            document.querySelectorAll('p[name]').forEach((item, index) => {
                item.innerHTML = `${week[index]}/${time}`;
                document.querySelectorAll('p[name]').forEach((item, index) => {
                    document.querySelector('#daysPast').innerHTML = `Dzień ${dayPast} z ${time}`;
                });
                week.forEach((item, index) => {
                    procent[index] = parseInt((item / time) * 100);
                    document.querySelectorAll(`div[name]`).forEach((item, index) => {
                        item.style = `width:${procent[index]}%`;
                    });
                });
            });
        }else{
            document.querySelectorAll('p[name]').forEach((item, index) => {
                item.innerHTML = `${week[index]}/${time}`;
                document.querySelectorAll('p[name]').forEach((item, index) => {
                    document.querySelector('#daysPast').innerHTML = `Dzień ${dayPast} z ${time}`;
                });
                week.forEach((item, index) => {
                    procent[index] = parseInt((item / time) * 100);
                    document.querySelectorAll(`div[name]`).forEach((item, index) => {
                        item.style = `width:${procent[index]}%`;
                    });
                });
            });
        }
        
        let progress = parseInt((dayPast / time) * 100)
        document.querySelector('div#progressBar').style=`width:${progress}%`
    }

    function addPoint(name, e) {
        e.preventDefault();
        weekId.forEach((item,index)=>{
        if(item == name){
            console.log(item)
            if (week[index] < time) {
                week[index]++;
            }
                document.querySelector(`p[name='${name}']`).innerHTML = `${week[index]}/${time}`;
                procent[index] = parseInt((week[index] / time) * 100);
                document.querySelector(`div[name='${name}']`).style = `width:${procent[index]}%`;
                console.log('week[index]',week[index]);
                console.log('name',name);

                $.post('/api/?cmd=updateHabitPoints', { 
                    id:name,
                    value:week[index]
                }).done(function(data){
                    console.log(data)
                })
            }
        })
    }

    function subtractPoint(name, e) {
        e.preventDefault();
        weekId.forEach((item,index)=>{

        if(item == name){
            console.log(item)
            if (week[index] > 0) {
                week[index]--;
            }
                document.querySelector(`p[name='${name}']`).innerHTML = `${week[index]}/${time}`;
                procent[index] = parseInt((week[index] / time) * 100);
                document.querySelector(`div[name='${name}']`).style = `width:${procent[index]}%`;
                console.log('week[index]',week[index]);
                console.log('name',name);
                $.post('/api/?cmd=updateHabitPoints', { 
                    id:name,
                    value:week[index]
                }).done(function(data){
                    console.log(data)
                })
            }
        });
    }

    
    </script>
</body>

</html>

<?php
} else {
            header("Location: index.php");
        }
    } else {
        header("Location: index.php");
    }
} else {
    header("Location: login.php");
}

function getDayName($id)
{
    switch ($id) {
        case 0:
            return "Poniedziałek";
        case 1:
            return "Wtorek";
        case 2:
            return "Środa";
        case 3:
            return "Czwartek";
        case 4:
            return "Piątek";
        case 5:
            return "Sobota";
        case 6:
            return "Niedziela";
    }
}

function calcUserPoints($pdo, $userId, $chId)
{
    $sthMax = $pdo->prepare('SELECT `length` FROM `challenges` WHERE `id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sthMax->execute(array(':id' => $chId));
    $rowsMax = $sthMax->fetchAll(PDO::FETCH_NUM);

    $maxDays = intval($rowsMax[0][0]);

    $sth = $pdo->prepare('SELECT start_time, count(*) FROM `users_challenges` INNER JOIN habits ON habits.challenge_id = users_challenges.challenge_id WHERE user_id = :user_id AND `users_challenges`.`challenge_id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute(array(':user_id' => $userId, ':id' => $chId));
    $rows = $sth->fetchAll(PDO::FETCH_NUM);

    $pkts = 0;

    if(count($rows) == 1 && $rows[0][0] != null)
    {
        $daysGone = floor((time() - $rows[0][0]) / 86400);

        if ($daysGone > $maxDays)
            $daysGone = $maxDays;

        $pkts = ($daysGone * 50) + ($rows[0][1] * 500);
    }

    return($pkts);
}

?>