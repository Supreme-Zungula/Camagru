<?php
    require_once "../config/class.user.php";
    // require_once "session.php";
    
    $user = new USER();

    /* if ($user->is_loggedin() == true)
    {
        $user->redirect("home.php");
    } */
    if (isset($_POST['btn-confirm']))
    {
        $username = strip_tags($_POST['username']);
        $email = strip_tags($_POST['email']);
    
        if (empty($username)){
            $error[] = "Username not entered";
        }
        else if (empty($email)){
            $error[] = "Email address must be filled";
        }
        else{
            try{
                $stmt = $user->run_query("SELECT * FROM users WHERE username=:username");
                $stmt->bindParam(":username", $username, PDO::PARAM_STR);
                $stmt->execute();
                if ($stmt->rowCount() == 1)
                {
                    $user_row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($email != $user_row['email']){
                        $error = "User does not exist. Click sign up to register";
                    }
                    else{
                        $subject = "Password reset";
                        $message = "Hi click on the link below to reset your password\n" .
                                    "http://localhost:8080/camagru/pages/password_reset.php";
                        $header = "Camagru security alert";
                        mail($email, $subject, $message, $header);
                        $success = "Check your email for a link to change your password";
                    }
                }
                else
                    $error = "You are not a registered user. Click sign up to register";
            }
            catch(PDOException $ex)
            {
                echo("ERROR: " . $ex->getMessage());
            }
        }
    }
?>

<!doctype <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Password reset</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/style.css" />
</head>
<body>
<div id="container">
    <header class="site-header">
        <a href="../index.php"><h1>Camagru</h1></a>
    </header>

    <form class="form-signin" method="POST">

        <h2 class="form-signin-heading">Password reset</h2><hr />
        
        <div id="error">
            <?php
                if(isset($error))
                {
                    ?>
                    <div class="error-alert">
                        <?php echo $error; ?>
                    </div>
                    <?php
                }
                else if (isset($success))
                {
                    ?>
                    <div class="alert-info">
                        <?php echo $success ?>
                    </div>
                    <?php
                }
            ?>
        </div>
        
        <div class="form-group">
            <input type="text" class="form-control" name="username" pattern="\w+" title="This field can only have letters and/or digits" placeholder="Enter your username">
        </div>
        
        <div class="form-group">
            <input type="email" class="form-control" name="email" placeholder="Enter your email address"  required="true"/>
        </div>
        <hr />
        
        <div class="form-group">
            <button type="submit" name="btn-confirm">Confirm</button><br /><br />
            <label>Not registered? <a href="sign-up.php">Sign up</a></label>
        </div><br />
    </form>
</div>
</body>
</html>