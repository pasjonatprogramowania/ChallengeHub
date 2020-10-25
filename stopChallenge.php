<?php

session_start();

if($_SESSION['logged'] == true)
{
    if(isset($_GET['id']) && !empty($_GET['id']))
    {
        require_once("db.php");

        $id = $_GET['id'];

        $userId = $_SESSION['user_id'];

        $alreadyStarted = $pdo->prepare('SELECT count(*) FROM `users_challenges` WHERE `user_id` = :user_id AND `challenge_id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $alreadyStarted->execute(array(':id' => $id, ':user_id' => $userId));
        $_alreadyStarted = $alreadyStarted->fetchAll(PDO::FETCH_NUM);

        if($_alreadyStarted[0][0] > 0)
        {
            $sth = $pdo->prepare('SELECT `id` FROM `habits` WHERE `challenge_id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $sth->execute(array(':id' => $id));
            $rows = $sth->fetchAll(PDO::FETCH_NUM);

            $sth2 = $pdo->prepare('DELETE FROM `users_challenges` WHERE `user_id` = :user_id AND `challenge_id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $sth2->execute(array(':id' => $id, ':user_id' => $userId));

            foreach ($rows as $row)
            {
                $sth3 = $pdo->prepare('DELETE FROM `users_habits` WHERE `user_id` = :user_id AND `habit_id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $sth3->execute(array(':id' => $row[0], ':user_id' => $userId));
            }
            
            header("Location: index.php");
        }
        else
        {
            displayError("W tym wyzwaniu już bierzesz udział!");
            header("Location: index.php");
            die();
        }
    }
}

function displayError($errorMessage)
{
    header("Content-type: application/json");
    echo(json_encode(array( "errorMessage" => $errorMessage )));
}
?>