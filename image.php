<?php

session_start();

if(isset($_GET['id']) && !empty($_GET['id']))
{
    $id = $_GET['id'];

    require_once 'db.php';

    $sth = $pdo->prepare('SELECT `author_id` FROM `challenges` WHERE `id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute(array(':id' => $id));
    $rows = $sth->fetchAll(PDO::FETCH_NUM);
    
    if(count($rows) == 0)
    {
        header('Location: index.php');
        echo 'no challenge';
        die();
    }

    $authorId = $rows[0][0];

    if(intval($authorId) != intval($_SESSION['user_id']))
    {
        header('Location: index.php');
        echo 'bad user';
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
        <title>Obraz</title>
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
        <div class="container">
            <div class="row" style='margin-top:40px;'>
                <form style='color:white'action="image.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data" id="myForm">
                    Select image to upload:
                    <input style='color:black' type="file" name="image" id="fileToUpload">
                    <br /><img id="myImg" src="" alt="your image" /><br />
                    <input style='color:black' type="submit" value="Upload Image" name="submit" onchange="checkPhoto(this,event)"/>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <a href="" style='color:white' class="btn orange darken-1 black-text toast-container col s12" onclick="postImg(event)">Zapisz wyzwanie</a>
    </div>
    <<script>
        function postImg(e){
            e.preventDefault();

        }
    </script>>
</body>
</html>
<?php

}
else
{
    header("Location: index.php");
}

?>