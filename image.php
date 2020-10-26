<?php

error_reporting(0);

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

    if(isset($_POST["submit"]))
    {
        $target_file = "images/" . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
        $target_file = "images/challenge_" . $id . "." . $imageFileType;

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false)
        {
            if ($_FILES["image"]["size"] > 500000)
            {
                showError("Ten plik jest za duży.");
            }
            else
            {
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
                {
                    showError("Plik może być tylko o rozszerzeniach JPG, JPEG, PNG i GIF.");
                }
                else
                {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file))
                    {
                        $sth = $pdo->prepare('UPDATE `challenges` SET `image_src` = :src WHERE `id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        $sth->execute(array(':id' => $id, ':src' => $target_file));

                        header('Location: startChallenge.php?id=' . $id);
                    }
                    else
                    {
                        showError('Pod czas załadowania pliku wystąpił błąd.');
                    }
                }
            }

            
        }
        else
        {
            showError("Ten plik nie jest obrazem.");
        }
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

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src='index.js'></script>
    <script>
        
    window.addEventListener('load', function () {
        document.querySelector('input[type="file"]').addEventListener('change', function () {
        if (this.files && this.files[0]) {
            var img = document.querySelector('img');
            img.src = URL.createObjectURL(this.files[0]);
        }
        });
    });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
    <div class="container">
        <div class="row" style='margin-top:40px;'>
            <h4>Wybierz obrazek do wyzwania</h4>
            <form action="image.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data" id="myForm">
                <input style='color:black' type="file" name="image" id="fileToUpload">
                <br />
                <br />
                <br />
                <br />
                <img id="myImg" src="" alt="your image" />
                <br />
                <br />
                <br />
                <br />
                <br />
                <input style='color:black;width:100%' class='btn orange' type="submit" value="Wrzuć" name="submit" onchange="checkPhoto(this,event)"/>
            </form>
        </div>
    </div>
</body>
</html>
<?php

}
else
{
    header("Location: index.php");
}

function showError($text)
{
    echo("<script>
    document.addEventListener('DOMContentLoaded', function(){

        M.toast({
            html: '$text',
            classes: 'toast-container',
            inDuration: 300,
            outDuration: 300,
            })
        }
        )  
    </script>");
}

?>