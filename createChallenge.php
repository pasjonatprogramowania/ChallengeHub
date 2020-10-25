<?php
session_start();

if ($_SESSION['logged'] == true)
{
    if ( isset($_POST['title'])  && isset($_POST['time'])  && isset($_POST['habits'])  && isset($_POST['habitsDay'])  && isset($_POST['points'])  && isset($_POST['description']) &&
         !empty($_POST['title']) && !empty($_POST['time']) && !empty($_POST['habits']) && !empty($_POST['habitsDay']) && !empty($_POST['points']) && !empty($_POST['description']))
    {
        
        //title, habits, habitsDay, time, points, description

        $title       = $_POST['title'];
        $habits      = $_POST['habits'];
        $habitsDay   = $_POST['habitsDay'];
        $time        = intval($_POST['time']);
        $points      = intval($_POST['points']);
        $description = $_POST['description'];

        header('Content-type: application/json');
        echo json_encode($_POST['file']);

        die();

        require_once("db.php");

        

        $sth = $pdo->prepare('INSERT INTO `challenges` (`name`, `description`, `length`, `author_id`, `image_src`, `pkts`, `category_id`) VALUES (:name, :desc, :len, :author, :img, :pkts, :category)', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute(array(':name' => $title, ':desc' => $description, ':len' => $time, ':author' => $_SESSION['user_id'], ':pkts' => $points, ':category' => 228, ':img' => 'images/no_image.jpg'));

        $sth2 = $pdo->prepare('SELECT `id` FROM `challenges` WHERE `name` = :name AND `description` = :desc AND `length` = :len AND `author_id` = :author AND `image_src` = :img AND `pkts` = :pkts AND `category_id` = :category', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth2->bindValue(':name', $title);
        $sth2->bindValue(':desc', $description);
        $sth2->bindValue(':len', intval($time), PDO::PARAM_INT);
        $sth2->bindValue(':author', intval($_SESSION['user_id']), PDO::PARAM_INT);
        $sth2->bindValue(':img', 'No image');
        $sth2->bindValue(':category', 228, PDO::PARAM_INT);
        $sth2->bindValue(':pkts', intval($points), PDO::PARAM_INT);
        $sth2->execute();
        $rows = $sth2->fetchAll(PDO::FETCH_NUM);

        if(count($rows) >= 1)
        {
            $id = -1;

            foreach ($rows as $row)
            {
                $id = $row[0];
            }
            
            for ($i = 0; $i < count($habits); $i++)
            {
                $habitName = $habits[$i];
                $habitDay = $habitsDay[$i];

                $sth = $pdo->prepare('INSERT INTO `habits` (`challenge_id`, `data`, `day`) VALUES (:id, :data, :day)', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $sth->execute(array(':id' => $id, ':data' => $habitName, ':day' => $habitDay));
            }
            
            //echo("startChallenge.php?id=$id");
            echo("image.php?id=$id");
        }
        
        die();
    }

?>

<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" />
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
        <link href="main.css" rel="stylesheet">
        <link rel="manifest" href="/manifest.json">
        <title>Stwórz wyzwanie</title>
    </head>
    <body class='black' style='color:white'>
        <style>
            img {
                max-width: 600px;
                max-height: 200px;
            }
            input{
                color:white;
            }
            .btn{
                color:white
            }
            h5{
                color:white;
            }
        </style>
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
    </a>        
          <!-- <div style=' display:flex; justify-content:flex-end'>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" onclick='turnOnDarkMode()' viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:32px;"><path     stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
        </div> -->
        <!-- Form -->

        <div class="container mobile">
            <div class="row">
                <form action="">
                    <div class="row">
                        <header>
                            <h5 id="points" >Za ukończenie tego wyzwania otrzymasz:</h5>
                            <div class="progress orange lighten-2" style="height: 20px">
                                <div class="determinate orange darken-1" id="pointsBar"></div>
                            </div>
                        </header>
                    </div>
                    <div class="sticky col s12">
                        <div class="row">
                            <div class="input-field col s12">
                                <input name="title" id="title" type="text" class="validate" />
                                <label for="title">Tytuł wyzwania</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input  name="description" id="description" type="text" class="validate" />
                                <label for="description"> Opis</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                            <a class='dropdown-trigger btn black text-darken-1 orange-text' href='#' style='display:flex; justify-content:flex-end' data-target='dropdown1'>Kategoria</a>
                                <ul id='dropdown1' id='' class='dropdown-content orange darken-1 '>
                                    <li><a class='black-text' name='0' onchange="console.log('a')" href="#!">Dieta</a></li>
                                    <li><a class='black-text' name='1' onchange="console.log('a')" href="#!">Ciało</a></li>
                                    <li><a class='black-text' name='2' onchange="console.log('a')" href="#!">Nałogi</a></li>
                                    <li><a class='black-text' name='3' onchange="console.log('a')" href="#!">Inne</a></li>
                                </ul>
                            
   
                                <!-- <label for="description"> Kategoria</label> -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input name="length" id="time" type="number" class="validate" onchange="countPointsForTime(event)" />
                                <label for="time">Czas trwania</label>
                            </div>
                        </div>
                        <div id="habits">
                            <div class="row" data="1">
                                <div class="input-field col s12">
                                    <input id="habit" data="1" onchange="addHabitTitle(event,this.value,this.name)" type="text" name="1" class="validate input" value="" />
                                    <label for="habit">Tresc nawyku</label>
                                </div>
                                <div>
                                    <label>
                                        <input name="1" type="radio" onchange="addChecked(event,this.name)" value="0" />
                                        <span>Poniedzialek</span>
                                    </label>
                                    <label>
                                        <input name="1" type="radio" onchange="addChecked(event,this.name)" value="1" />
                                        <span>Wtorek</span>
                                    </label>
                                    <label>
                                        <input name="1" type="radio" onchange="addChecked(event,this.name)" value="2" />
                                        <span>Środa</span>
                                    </label>

                                    <label>
                                        <input name="1" type="radio" onchange="addChecked(event,this.name)" value="3" />
                                        <span>Czwartek</span>
                                    </label>
                                    <label>
                                        <input name="1" type="radio" onchange="addChecked(event,this.name)" value="4" />
                                        <span>Piątek</span>
                                    </label>
                                    <label>
                                        <input name="1" type="radio" onchange="addChecked(event,this.name)" value="5" />
                                        <span>Sobota</span>
                                    </label>
                                    <label>
                                        <input name="1" type="radio" onchange="addChecked(event,this.name)" value="6" />
                                        <span>Niedziela</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <footer>
                            <div class="row">
                                <a href="" style='color:white' class="btn orange darken-1 black-text col s6" onclick="createHabitField(event)">Dodaj Nawyk</a>
                                <a href="" style='color:white' class="btn orange darken-1 black-text col s6" onclick="removeHabitField(event)">Usuń Nawyk</a>
                                <a href="" style='color:white' class="btn orange darken-1 black-text toast-container col s12" onclick="saveChallenges(event)">Zapisz wyzwanie</a>
                            </div>
                        </footer>
                    </div>
                </form>
            </div>
        </div>
       
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
        <script>

                document.addEventListener('DOMContentLoaded', function() {
                    var elems = document.querySelectorAll('.dropdown-trigger');
                    var instances = M.Dropdown.init(elems );
                });
                $('.dropdown-trigger').dropdown();
                // Or with jQuery

                


            // const formSelector = document.querySelector("#myForm")
            // const fileSelector = document.querySelector("input[type='file']")
            // console.log(document.querySelector("#myForm"))

            // function onsubmit(e){
            //         e.preventDefault();
            //         const endpoint = 'upload.php';
            //         const formData = new FormData();
            //     formData.append('fileSelector',fileSelector.file[0]);
            //     fetch(endpoint,{
            //         method:'post',
            //         body:formData
            //     }).catch(console.error);
            // }
            document.addEventListener('load', function () {
                if (localStorage.getItem('dark-mode')) {
                    document.documentElement.classList.toggle('dark-mode')
                }
            });
            document.addEventListener('DOMContentLoaded', function () {
                var elems = document.querySelectorAll('.tooltipped');
                var instances = M.Tooltip.init(elems);
            });

            document.addEventListener('DOMContentLoaded', function() {
                var elems = document.querySelectorAll('.sidenav');
                var instances = M.Sidenav.init(elems);
            });

            let habits = [];
            let habitsDay = [];
            let domIndexToHide = [];
            let iterationCounter = 1;
            let pointsTotal = 0;
            let pointsForTime = 0;
            let pointsForHabits = 0;

            setInterval(function () {
                pointsForTime = Math.abs(pointsForTime);
                pointsForHabits = Math.abs(pointsForHabits);
                pointsTotal = pointsForTime + pointsForHabits;
                if (!isNaN(parseFloat(pointsTotal)) && isFinite(pointsTotal)) {
                    document.querySelector('#points').innerHTML = `Za ukończenie tego wyzwania otrzymasz: ${pointsTotal}pkt`;
                    let procent = (pointsTotal / 4000) * 100;
                    document.querySelector('#pointsBar').style = `width:${procent}%`;
                }

                if (habitsDay.length == habits.length) {
                    pointsForHabits = 500 * habits.length;
                }
            }, 100);

            function countPointsForTime(e) {
                e.preventDefault();
                let time = document.querySelector('#time').value;
                pointsForTime = time * 50;
                sessionStorage.setItem('lastTime', time);
                if (!isNaN(parseFloat(time)) && isFinite(time)) {
                    time = Math.abs(time);
                    document.querySelector('#time').value = time;
                }
            }

            function addChecked(e, name) {
                e.preventDefault();
                document.querySelectorAll(`input[name='${name}']`).forEach((item, index) => {
                    if (!item.checked) {
                        return;
                    } else {
                        habitsDay[parseInt(parseInt(name) - 1)] = item.value;
                        item.setAttribute('checked', 'checked');
                    }
                });
            }

            function addHabitTitle(e, value, name) {
                e.preventDefault();
                habits[name - 1] = document.querySelector(`input[name='${name}']`).value;
            }
            function createHabitField(e) {
                e.preventDefault();
                let habitInputValue = [];
                console.log(document.querySelectorAll(`input.input:not([style='display: none;'])`).length);
                if (document.querySelectorAll(`input.input:not([style='display: none;'])`).length != 0) {
                    document.querySelectorAll(`input.input:not([style='display: none;']`).forEach((item, index) => {
                        habitInputValue[index] = item.value;
                    });
                }
                console.log('habitInputValue', habitInputValue);
                console.log('habits', habits);
                console.log('habitsDay', habitsDay);
                if (habitInputValue.length == habitsDay.length && !habitInputValue.includes('')) {
                    iterationCounter++;
                    if (domIndexToHide.includes(iterationCounter)) {
                        let html = [];
                        html = document.querySelectorAll(`[data='${iterationCounter}']`);
                        html.forEach((item, index) => {
                            item.style = 'display:block;';
                        });
                    } else {
                        let html = document.querySelector('#habits').innerHTML;
                        html += `
                                <div class="row" data="${iterationCounter}">
                                    <div class="input-field col s12">
                                        <input onchange="addHabitTitle(event,this.value,this.name)" data='${iterationCounter}'id='${iterationCounter}'  type="text" name="${iterationCounter}" class="validate input" />
                                        <label for='${iterationCounter}' >Tresc nawyku</label>
                                    </div>
                                    <div>
                                        <label>
                                            <input name="${iterationCounter}" type="radio" onchange='addChecked(event,this.name)' value="0" />
                                            <span>Poniedzialek</span>
                                        </label>
                                        <label>
                                            <input name="${iterationCounter}" type="radio" onchange='addChecked(event,this.name)' value="1" />
                                            <span>Wtorek</span>
                                        </label>
                                        <label>
                                            <input name="${iterationCounter}" type="radio" onchange='addChecked(event,this.name)' value="2" />
                                            <span>Środa</span>
                                        </label>

                                         <label>
                                            <input name="${iterationCounter}" type="radio" onchange='addChecked(event,this.name)' value="3" />
                                            <span>Czwartek</span>
                                        </label>
                                        <label>
                                            <input name="${iterationCounter}" type="radio" onchange='addChecked(event,this.name)' value="4" />
                                            <span>Piątek</span>
                                        </label>
                                        <label>
                                            <input name="${iterationCounter}" type="radio" onchange='addChecked(event,this.name)' value="5" />
                                            <span>Sobota</span>
                                        </label>
                                        <label>
                                            <input name="${iterationCounter}" type="radio" onchange='addChecked(event,this.name)' value="6" />
                                            <span>Niedziela</span>
                                        </label>
                                    </div>
                                </div>`;
                        document.querySelector('#habits').innerHTML = html;
                    }

                    document.querySelectorAll(`input.input`).forEach((item, index) => {
                        if (!habits[index]) {
                            return;
                        } else {
                            item.value = habits[index];
                        }
                    });

                    M.toast({
                        html: 'Nawyk został dodany',
                        classes: 'toast-container',
                        inDuration: 300,
                        outDuration: 300,
                    });
                } else {
                    M.toast({
                        html: 'Uzupelnij informacje',
                        classes: 'toast-container',
                        inDuration: 300,
                        outDuration: 300,
                    });
                }
            }

            function removeHabitField(e) {
                e.preventDefault();
                console.log(document.querySelector(`[data='${iterationCounter}']`));
                if (document.querySelector(`[data='${iterationCounter}']`)) {
                    let html = [];
                    html = document.querySelectorAll(`[data='${iterationCounter}']`);
                    html.forEach((item, index) => {
                        item.style = 'display:none;';
                    });
                    domIndexToHide.push(iterationCounter);
                    document.querySelector(`input[type='text'][name='${iterationCounter}']`).value = '';
                    if (document.querySelector(`input[type='radio'][name='${iterationCounter}'][checked='checked']`)) {
                        document.querySelector(`input[type='radio'][name='${iterationCounter}'][checked='checked']`).checked = false;
                        document.querySelector(`input[type='radio'][name='${iterationCounter}'][checked='checked']`).removeAttribute('checked');
                    }
                    iterationCounter--;
                }

                habitsDay.splice(iterationCounter, 1);
                habits.splice(iterationCounter, 1);

                document.querySelectorAll(`input.input`).forEach((item, index) => {
                    if (!habits[index]) {
                        return;
                    } else {
                        item.value = habits[index];
                    }
                });

                M.toast({
                    html: 'Nawyk został usunięty',
                    classes: 'toast-container',
                    inDuration: 300,
                    outDuration: 300,
                });
            }

            function saveChallenges(e) {
                e.preventDefault();
                let time = document.querySelector('#time').value;
                if (!isNaN(parseFloat(time)) && isFinite(time)) {
                    time = Math.abs(time);
                }
                if (habitsDay.length != habits.length) {
                    if (habitsDay.length > habits.length) {
                        habitsDay.pop();
                    } else {
                        habits.pop();
                    }
                }
                

                let title = document.querySelector(`input[name='title']`).value;
                let _time = document.querySelector(`input[name='length']`).value;
                let description = document.querySelector(`input[name='description']`).value;

                $.post('createChallenge.php', {
                    title: title,
                    habits: habits,
                    habitsDay: habitsDay,
                    time: _time,
                    points: pointsTotal,
                    description: description
                }).done(function (data) {
                    console.log(data);
                    window.location.href = data;
                });
                

                M.toast({
                    html: 'Wyzwanie zostało zapisane',
                    classes: 'toast-container',
                    inDuration: 300,
                    outDuration: 300,
                });

                console.log('iterationCounter', iterationCounter);
                console.log('title', title);
                console.log('habits', habits);
                console.log('habitsDay', habitsDay);
                console.log('description', description);
                console.log('time', _time);
                console.log('points', pointsTotal);
            }

            // window.addEventListener('load', function () {
            //     document.querySelector('input[type="file"]').addEventListener('change', function () {
            //     if (this.files && this.files[0]) {
            //         var img = document.querySelector('img');
            //         img.src = URL.createObjectURL(this.files[0]);
            //         img.onload = imageIsLoaded;
            //     }
            //     });
            // });

            function imageIsLoaded() {
                alert(this.src);
            }
        </script>
        <script src='index.js'></script>
    </body>
</html>


<?php
} else {
    header("Location: login.php");
}

?>
