<?php

session_start();

if( $_SESSION['logged'] == true )
{
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
    </a>    <!-- <div style=' display:flex; justify-content:flex-end'>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" onclick='turnOnDarkMode()' viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:32px;"><path     stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
        </div> -->

        <!--    Info   -->
        <div class="container center">

            <div class="row center">
                <div class="col s12 m9 l6 box center">
                    <span class="huge-flow-text">Profil <i class="material-icons text-white circle orange darken-1 profile">person</i></span>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12 box">
                    <input style='color:orange; border-color:#fb8c00' name="" id="username" type="text" class="validate"  data-min="10" value="<?php echo($_SESSION['username']); ?>" disabled="true">
                    <label style='color:white' for="username">Nazwa Użytkownika: </label>
                </div>
            </div>
       
           

        <div class="row">
            <div class="input-field col s12 box">
              <input style='color:orange ; border-color:#fb8c00' name="" id="email" type="text" class="validate"  data-min="10" value="<?php echo($_SESSION['email']); ?>" disabled="true">
              <label style='color:white' for="email">E-mail: </label>
            </div>
        </div>


            <div class="row">
                <div class="input-field col s12 box text-black">
                  <input style='color:orange;  border-color:#fb8c00' name="" id="idu" type="text" class="validate" value="<?php echo($_SESSION['user_id']); ?>" disabled="true">
                  <label style='color:white' for="idu">ID Użytkownika: </label>
                </div>
            </div>
            
            <div class="row">
                <div class="input-field col s12 box text-black">
                  <input style='color:orange;  border-color:#fb8c00' name="" id="date" type="text" class="validate" value="<?php echo(date('d.m.Y H:i:s', $_SESSION['registered'])); ?>" disabled="true">
                  <label style='color:white' for="date">Data założenia konta: </label>
                </div>
            </div>

            <div class="row">
            <div class="input-field col s12 box text-black">
                <input style='color:orange;  border-color:#fb8c00' name="" id="punkty" type="text" class="validate" disabled="true" value="data">
                <label style='color:white' for="punkty">Punkty: </label>
            </div>
        </div>


    </div>




       
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


<?php
}
else
{
    header("Location: login.php");
}

?>