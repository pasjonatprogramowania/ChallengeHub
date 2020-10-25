<?php
session_start();

if ( isset($_SESSION['logged']) && isset($_SESSION['username']) && !empty($_SESSION['username']) && $_SESSION['logged'] == true )
{
    header("Location: index.php");
}

if ( isset($_POST['email']) && isset($_POST['password']) && !empty($_POST['email']) && !empty($_POST['password']))
{
    $email = strtolower($_POST['email']);
    $pwd = md5($_POST['password']);


    require_once("db.php");

    $sql = 'SELECT * FROM `users` WHERE `email` = :email';
    $sth = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute(array(':email' => $email));

    $rows = $sth->fetchAll(PDO::FETCH_NUM);

    if ( count($rows) == 1 )
    {
        $_id = $rows[0][0];
        $_username = $rows[0][1];
        $_email = $rows[0][2];
        $_pwd = $rows[0][3];
        $_registered = $rows[0][4];
        
        if ( ($email == $_email) && ($pwd == $_pwd) )
        {
            $_SESSION['logged'] = true;
            $_SESSION['username'] = $_username;
            $_SESSION['email'] = $_email;
            $_SESSION['user_id'] = $_id;
            $_SESSION['registered'] = $_registered;

            header("Location: index.php");
        }
        else
        {
            //TODO: Bad password
            showLoginError();
        }
    }
    else{
        //TODO: Show error
        showLoginError();
    }
}

function showLoginError()
{
    echo("<script>
    document.addEventListener('DOMContentLoaded', function(){

        M.toast({
            html: 'Błędny e-mail lub hasło!',
            classes: 'toast-container',
            inDuration: 300,
            outDuration: 300,
            })
        }
        )  
    </script>");
}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="main.css" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">
    <title>Login</title>
</head>


<body class='black'>
    <style>
        .toast-container {
    font-weight: bold;
    background-color: red;
}
    </style>
    <ul class="sidenav grey darken-4 white-text" id="mobile-links">
        <li><a  style='color:white'href="slider.php">O nas</a></li>
        <li><a  style='color:white'href="register.php">Zarejestruj</a></li>
    </ul>
    <nav class="nav-wraper grey darken-4 white-text hide-on-small-only">
        <div class="container">
        <ul class="left">
        <li><a  style='color:white'href="slider.php.php">O Nas</a></li>
            </ul>
            <ul class="right">
            <li><a style='color:white'href="register.php">Zarejestruj</a></li>
                
            </ul>
        </div>
    </nav>
    <a href="#" class="sidenav-trigger hide-on-med-and-up" data-target="mobile-links">
        <i class="material-icons circle orange darken-1 grey-text text-darken-3">menu</i>
    </a>
    <!-- <div style='background-color:white; display:flex; justify-content:flex-end'>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" onclick='turnOnDarkMode()' viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:32px;"><path     stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
        </div> -->
        <!--    login   -->

        
        <div class="container">
        <p class="flow-text orange-text">Zaloguj się</p>
        <form method="post" action="">
        <div class="row">
            <div class="input-field col s12">
              <input name="email" id="email" type="email" class="validate grey darken-4  white-text"  data-min="10" value="<?php  if(isset($_POST['email']) && !empty($_POST['email'])) echo( $_POST['email'] ); ?>">
              <label for="email">Wpisz e-mail</label>
            </div>
        </div>
        
        <div class="row">
            <div class="input-field col s12">
                <input name="password" id="password" type="password" class="validate grey darken-4 white-text">
                <label for="password">Wpisz Hasło</label>
            </div>
        </div>
        <div class="row">
            <input class="btn orange darken-1" type="submit" value="Zaloguj">
            <p class="white-text">Nie masz jeszcze konta? <a href="register.php" class="orange-text">Zarejestruj się</a></p>
        </div>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
    <script>
        $(document).ready(function()
        {
            $('.sidenav').sidenav();

        })
            function redirect() {
                var thecookie = readCookie('doRedirect');
                if (!thecookie) {
                    //Tu wprowadz nazwe slidera która bedzie na serwerze
                    window.location = 'slider.php';
                }
            }
            function createCookie(name, value, days) {
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
                    var expires = '; expires=' + date.toGMTString();
                } else var expires = '';
                document.cookie = name + '=' + value + expires + '; path=/';
            }
            function readCookie(name) {
                var nameEQ = name + '=';
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
                }
                return null;
            }
            window.onload = function () {
                redirect();
                createCookie('doRedirect', 'true', '999');
            };

            document.addEventListener('load', function () {
                if (localStorage.getItem('dark-mode')) {
                    document.documentElement.classList.toggle('dark-mode')
                }
            });
        </script>
    <script src='index.js'></script>

</body>
</html>