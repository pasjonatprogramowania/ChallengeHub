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
        echo("Hasło jest za krótkie!");
    }
    elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo("Podano niepoprawny format E-mail!");
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
                    echo("Ta nazwa użytkownika już jest zajęta!");
                }
            }
            else
            {
                echo("Ten adres Email już jest zajęty!");
            }
        }
        else
        {
            echo("Hasła nie zgadzają się!");
        }
    }
}
?>

<form method="post" action="">
    <span>Imię użytkownika: </span></br><input type="text" name="username" value="<?php if(isset($_POST['username'])) echo($_POST['username']); ?>"> </br>
    <span>E-mail: </span></br><input type="text" name="email" value="<?php if(isset($_POST['email'])) echo($_POST['email']); ?>"> </br>
    <span>Hasło: </span></br><input type="password" name="password" value="<?php if(isset($_POST['password'])) echo($_POST['password']); ?>"> </br>
    <span>Powtórzenie: </span></br><input type="password" name="password2" value="<?php if(isset($_POST['password2'])) echo($_POST['password2']); ?>"> </br>
    <input type="submit" value="Załóż konto">
</form>