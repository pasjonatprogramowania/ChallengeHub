<?php

session_start();

if (isset($_GET['cmd']) && !empty($_GET['cmd']))
{
    $cmd = $_GET['cmd'];

    $logged = false;

    if (isset($_SESSION['logged']) && !empty($_SESSION['logged']))
    {
        if ($_SESSION['logged'] == true)
        {
            $logged = true;
        }
    }

    if($logged == false)
    {
        displayError("Nie jesteś zalogowany!");
        die();
    }

    $userId = $_SESSION['user_id'];
    
    require_once("../db.php");

    header("Content-type: application/json");

    if ($cmd == "getChallenge" && isset($_POST['challengeId']) && !empty($_POST['challengeId']))
    {
        $id = $_POST['challengeId'];

        $sth = $pdo->prepare('SELECT `name`, `description`, `length`, `author_id`, `image_src`, `pkts`, `category_id` FROM `challenges` WHERE `id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute(array(':id' => $id));
        $rows = $sth->fetchAll(PDO::FETCH_NUM);

        

        if (count($rows) == 1)
        {
            $sth2 = $pdo->prepare('SELECT `habits`.`id`, `habits`.`data`, `habits`.`day`, `users_habits`.`value` FROM `habits` INNER JOIN `users_habits` ON `habits`.`id` = `users_habits`.`habit_id` WHERE `challenge_id` = :id AND `users_habits`.`user_id` = :user_id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $sth2->bindValue(':id', intval($id), PDO::PARAM_INT);
            $sth2->bindValue(':user_id', intval($userId), PDO::PARAM_INT);
            $sth2->execute();
            $rows2 = $sth2->fetchAll(PDO::FETCH_NUM);

            //echo(json_encode($rows[0]));
            echo(
                json_encode(
                    array(
                        "name" => $rows[0][0],
                        "desc" => $rows[0][1],
                        "len" => $rows[0][2],
                        "author" => $rows[0][3],
                        "image" => $rows[0][4],
                        "pkts" => $rows[0][5],
                        "category" => $rows[0][6],
                        "habits" => $rows2
                    )
                )
            );
        }
        else
        {
            displayError("Challenge o id $id nie znaleziony.");
        }  
        
        die();
    }


    if ($cmd == "getCategories")
    {
        $sth = $pdo->prepare('SELECT * FROM `categories`', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute();
        $rows = $sth->fetchAll(PDO::FETCH_NUM);

        echo(json_encode($rows));

        die();
    }

    if ($cmd == "getServerTime")
    {
        echo json_encode(array( "timestamp" => time() ));

        die();
    }

    if ($cmd == "getUserChallengeStartTime" && isset($_POST['challengeId']) && !empty($_POST['challengeId']))
    {
        $challengeId = $_POST['challengeId'];

        //SELECT `start_time` FROM `users_challenges` WHERE user_id = AND challenge_id = 

        $sth = $pdo->prepare('SELECT `start_time` FROM `users_challenges` WHERE user_id = :user_id AND challenge_id = :challenge_id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute(array(':user_id' => $userId, ':challenge_id' => $challengeId));
        $rows = $sth->fetchAll(PDO::FETCH_NUM);

        echo(json_encode(array('startTime' => $rows[0][0])));

        die();
    }

    if ($cmd == "getUserPoints")
    {
        echo(calcUserPoints($pdo, $userId));

        die();
    }

    if ($cmd == "getCurrentUserId")
    {
        echo json_encode(array("user_id" => $userId));
        
        die();
    }

    if ($cmd == 'updateHabitPoints')
    {
        if (isset($_POST['id']) && !empty($_POST['id']))
        {
            $habitId = $_POST['id'];

            if (isset($_POST['value']))
            {
                $value = $_POST['value'];

                $sth = $pdo->prepare('SELECT * FROM `users_habits` WHERE `user_id` = :user_id AND `habit_id` = :habit_id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $sth->execute(array(':user_id' => $userId, ':habit_id' => $habitId));
                $rows = $sth->fetchAll(PDO::FETCH_NUM);

                if(count($rows) == 1)
                {
                    $sth2 = $pdo->prepare('UPDATE `users_habits` SET `value` = :value WHERE `user_id` = :user_id AND `habit_id` = :habit_id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $sth2->execute(array(':user_id' => $userId, ':habit_id' => $habitId, ':value' => $value));
                }
                else
                {
                    displayError("Nie bierzesz udziału w tym wyzwaniu!");
                }
            }
            else
            {
                displayError("Nie podano nowej wartości!");
            }
        }
        else
        {
            displayError("Nie podano id nawyki!");
        }
        

        die();
    }
}

function calcUserPoints($pdo, $userId)
{
    $sth = $pdo->prepare('SELECT start_time, count(*) FROM `users_challenges` INNER JOIN habits ON habits.challenge_id = users_challenges.challenge_id WHERE user_id = :user_id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute(array(':user_id' => $userId));
    $rows = $sth->fetchAll(PDO::FETCH_NUM);

    $pkts = 0;

    if(count($rows) == 1 && $rows[0][0] != null)
    {
        $daysGone = floor((time() - $rows[0][0]) / 86400);

        $pkts = ($daysGone * 50) + ($rows[0][1] * 500);
    }

    return($pkts);
}

function map($val, $fromL, $fromH, $toL, $toH)
{
    return(($val - $fromL) * ($toH - $toL) / ($fromH - $fromL) + $toL);
}

function displayError($errorMessage)
{
    echo(json_encode(array( "errorMessage" => $errorMessage )));
}

?>