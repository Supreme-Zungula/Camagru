<?php
    require "../config/class.user.php";
    require_once "session.php";

    $user = new USER();
    if ($user->is_loggedin() ==  true)
    {
        $user->redirect("home.php");

    }
    if (isset($_GET['id']) && isset($_GET['key']))
    {
        $username = strip_tags($_GET['id']);
        $key_hash = $_GET['key'];

        $stmt = $user->run_query("SELECT username, email FROM users WHERE username=:username");
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() == 1)
        {
            if (hash("whirlpool", $userRow['email']) == $key_hash);
            {
                $user->verify($username, $userRow['email']);
                $user->redirect("../index.php");
            }
        }
    } 
?>
