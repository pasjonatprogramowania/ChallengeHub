<?php
session_start();

if ( isset($_SESSION['logged']) && isset($_SESSION['username']) && !empty($_SESSION['username']) && $_SESSION['logged'] == true )
{
    header("Location: index.php");
}

if ( isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password2']) && !empty($_POST['email']) && !empty($_POST['username']) && !empty($_POST['password'])  && !empty($_POST['password2']) ) 
{
    if(strlen($_POST['password']) < 8)
    {
        displayError("Za któtkie hasło");
    }
    elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        displayError("Podano nie poprawny format e-mail!");
    }
    else
    {
        $username = strtolower($_POST['username']);
        $email = strtolower($_POST['email']);
        $pwd = md5($_POST['password']);
        $pwd2 = md5($_POST['password2']);

        if($pwd == $pwd2)
        {
            require_once("db.php");
        
            $sql = 'SELECT `id` FROM `users` WHERE `email` = :email';
            $sth = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $sth->execute(array(':email' => $email));

            $rows = $sth->fetchAll(PDO::FETCH_NUM);

            if(count($rows) == 0)
            {
                $sql2 = 'SELECT `id` FROM `users` WHERE `username` = :username';
                $sth2 = $pdo->prepare($sql2, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $sth2->execute(array(':username' => $username));

                $rows2 = $sth2->fetchAll(PDO::FETCH_NUM);

                if(count($rows2) == 0)
                {
                    $sql3 = 'INSERT INTO `users` (`username`, `email`, `password`, `registered`) VALUES (:username, :email, :password, :registered)';
                    $sth3 = $pdo->prepare($sql3, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $sth3->execute(array(':username' => $username, ':email' => $email, ':password' => $pwd, ':registered' => time()));

                    $sql4 = 'SELECT * FROM `users` WHERE `email` = :email';
                    $sth4 = $pdo->prepare($sql4, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $sth4->execute(array(':email' => $email));

                    $rows4 = $sth4->fetchAll(PDO::FETCH_NUM);

                    if ( count($rows4) == 1 )
                    {
                        $_id = $rows4[0][0];
                        $_username = $rows4[0][1];
                        $_email = $rows4[0][2];
                        $_pwd = $rows4[0][3];
                        $_registered = $rows4[0][4];
                        
                        $_SESSION['logged'] = true;
                        $_SESSION['username'] = $_username;
                        $_SESSION['email'] = $_email;
                        $_SESSION['user_id'] = $_id;
                        $_SESSION['registered'] = $_registered;

                        header("Location: index.php");
                    }
                }
                else
                {
                    displayError("Ta nazwa użytkownika jest już zajęta!");
                }
            }
            else
            {
                displayError("ten E-mail jest już zajęty!");
            }
        }
        else
        {
            displayError("Hasła nie zgadzają się!");
        }
    }
}

function displayError($str)
{
    echo "<script>
    document.addEventListener('DOMContentLoaded', function(){

        M.toast({
            html: '" . $str . "',
            classes: 'toast-container',
            inDuration: 300,
            outDuration: 300,
            })
        }
        )  
    </script>";
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
    <title>Rejestracja</title>
    <style>
        .toast-container {
    font-weight: bold;
    background-color: red;
}
    </style>
</head>
<body class='black'>
<ul class="sidenav grey darken-4 white-text" id="mobile-links">
        <li><a  style='color:white'href="slider.php">O nas</a></li>
        <li><a  style='color:white'href="login.php">Zaloguj</a></li>
    </ul>
    <nav class="nav-wraper grey darken-4 white-text hide-on-small-only">
        <div class="container">
        <ul class="left">
        <li><a  style='color:white'href="slider.php.php">O Nas</a></li>
            </ul>
            <ul class="right">
            <li><a style='color:white'href="login.php">Zaloguj</a></li>
                
            </ul>
        </div>
    </nav>
    <a href="#" class="sidenav-trigger hide-on-med-and-up" data-target="mobile-links">
        <i class="material-icons circle orange darken-1 grey-text text-darken-3">menu</i>
    </a>

        <!-- <div style='background-color:white; display:flex; justify-content:flex-end'>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" onclick='turnOnDarkMode()' viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:32px;"><path     stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
        </div> -->
     
        <style>
            p{
                color:#fb8c00 
            }
        </style>
    <div class="container">
        <p class="flow-text">Zarejestruj się</p>
        
        <form method="post" action="">

        <div class="row">
            <div class="input-field col s12">
              <input name="username" id="username" type="text" class=" grey darken-4  white-text" value="<?php if(isset($_POST['username'])) echo($_POST['username']); ?>">
              <label for="username">Wpisz imię użytkownika</label>
              </div>
        </div>

        <div class="row">
            <div class="input-field col s12">
              <input  class=" grey darken-4  white-text"  name="email" id="email" type="email" value="<?php if(isset($_POST['email'])) echo($_POST['email']); ?>">
              
              <label for="email">Wpisz E-Mail</label>
            </div>
        </div>
            <div class="row">
                <div class="input-field col s12">
                  <input  name="password" id="password" type="password" class="validate grey darken-4  white-text" value="<?php if(isset($_POST['password'])) echo($_POST['password']); ?>">
                  <label for="password">Wpisz Hasło</label>
                </div>
                
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input name="password2" id="password2" type="password" class="validate grey darken-4  white-text" value="<?php if(isset($_POST['password2'])) echo($_POST['password2']); ?>">
                    <label for="password2">Potwierdź Hasło</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <p>
                        <label>
                            <input name="1" onchange='changeCheckBox()' id='checkMePls' type="checkbox" require>
                            <span>Czy zgadasz się na przetważanie twoich danych osobowych?</span>
                        </label>
                    </p>
                    <div class="input-field col s12">
                     <p><input type="submit" class="modal-close btn orange darken-1" style='backgound-color:#fb8c00'id='register' value="Zarejestruj" disabled></p>
                    </div>
                </div>
            </div>
        </form>
        <p class="white-text">Masz już konto? <a href="login.php" class="orange-text text-darken-1">Zaloguj się</a></p>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
    <script>
        $(document).ready(function()
        {
            $('.sidenav').sidenav();
        })

        function changeCheckBox(){
            if(document.querySelector(`input#checkMePls`).name == '0'){
                document.querySelector(`input#checkMePls`).name='1'
                document.querySelector(`input#checkMePls`).setAttribute(`checked`,"checked")
                document.querySelector('input#register').disabled=true
            }else{
                document.querySelector(`input#checkMePls`).name='0'
                document.querySelector(`input#checkMePls`).removeAttribute('checked')
                document.querySelector('input#register').disabled=false
            }
        }

        document.addEventListener('load', function () {
            if (localStorage.getItem('dark-mode')) {
                document.documentElement.classList.toggle('dark-mode')
            }
        });
        

    </script>
    <script src='index.js'></script>

</body>
</html>