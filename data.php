<?php

if(isset($_GET['cmd']) && !empty($_GET['cmd']))
{
    $cmd = $_GET['cmd'];
    
    require_once("db.php");

    if ($cmd == "getChallenge" && isset($_GET['challengeId']) && !empty($_GET['challengeId']))
    {
        $id = $_GET['challengeId'];

        $sth = $pdo->prepare('SELECT `name`, `description`, `length`, `author_id`, `image_src`, `pkts` FROM `challenges` WHERE `id` = :id', array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute(array(':id' => $id));
        $rows = $sth->fetchAll(PDO::FETCH_NUM);

        if (count($rows) == 1)
            echo(json_encode($rows[0]));
        else
            displayError("Challenge o id $id nie znaleziony.");
    }
}


function displayError($errorMessage)
{
    echo(json_encode(array( "errorMessage" => $errorMessage )));
}

?>