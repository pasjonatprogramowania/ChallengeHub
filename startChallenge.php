<?php

session_start();

if($_SESSION['logged'] == true)
{
    if(isset($_GET['id']) && !empty($_GET['id']))
    {
        require_once("db.php");
        header("Content-type: application/json");

        $id = $_GET['id'];

        $sth = $pdo->prepare('SELECT * FROM `challenges` WHERE `id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute(array(':id' => $id));
        $rows = $sth->fetchAll(PDO::FETCH_NUM);

        if($rows > 0)
        {
            $userId = $_SESSION['user_id'];
            //$challengeId = $rows[0][0];

            $alreadyStarted = $pdo->prepare('SELECT count(*) FROM `users_challenges` WHERE `user_id` = :user_id AND `challenge_id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $alreadyStarted->execute(array(':id' => $id, ':user_id' => $userId));
            $_alreadyStarted = $alreadyStarted->fetchAll(PDO::FETCH_NUM);

            if($_alreadyStarted[0][0] == 0)
            {
                $sth2 = $pdo->prepare('SELECT `id` FROM `habits` WHERE `challenge_id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $sth2->execute(array(':id' => $id));
                $rows2 = $sth2->fetchAll(PDO::FETCH_NUM);

                $sth3 = $pdo->prepare('INSERT INTO `users_challenges` (`user_id`, `challenge_id`, `start_time`) VALUES (:user_id, :id, :start_time)', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $sth3->execute(array(':id' => $id, ':user_id' => $userId, ':start_time' => time()));

                $sth4 = $pdo->prepare('SELECT count(*) FROM `users_challenges` WHERE `user_id` = :user_id AND `challenge_id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $sth4->execute(array(':id' => $id, ':user_id' => $userId));
                $rows4 = $sth4->fetchAll(PDO::FETCH_NUM);

                if($rows4[0][0] != 0)
                {
                    foreach ($rows2 as $row)
                    {
                        $habbitId = $row[0];

                        $sth3 = $pdo->prepare('INSERT INTO `users_habits` (`user_id`, `habit_id`, `value`) VALUES (:user_id, :id, 0)', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        $sth3->execute(array(':id' => $habbitId, ':user_id' => $userId));
                    }

                    header("Location: challenge.php?id=$id");
                }
                else
                {
                    displayError("Wystąpił błąd!");
                    die();
                }
            }
            else
            {
                displayError("W tym wyzwaniu już bierzesz udział!");
                header("Location: challenge.php?id=$id");
                die();
            }
        }
        else
        {
            displayError("To wyzwanie nie znalezione!");
            die();
        }
    }
}

function displayError($errorMessage)
{
    echo(json_encode(array( "errorMessage" => $errorMessage )));
}
?>