<?php
    require_once "../config/class.user.php";

     $user = new USER();

     if ($user->is_loggedin() == true)
     {
         $user->redirect("home.php");
     }
     if (isset($_POST['btn-confirm']))
     {
         $username = strip_tags($_POST['username']);
         $new_password = strip_tags($_POST['new_password']);
         $confirm_password = strip_tags($_POST['confirm_password']);
     
         if (empty($username)){
             $error = "Username not entered";
         }
         else if (empty($new_password)){
             $error = "Please fill in your new password";
         }
         else if (empty($confirm_password)){
             $error = "Please confirm your new password";
         }
         else if ($new_password !=  $confirm_password){
             $error = "Passwords do not match";
         }
         else{
             try{
                 $stmt = $user->run_query("SELECT * FROM users WHERE username=:username");
                 $stmt->bindParam(":username", $username, PDO::PARAM_STR);
                 $stmt->execute();
                 if ($stmt->rowCount() == 1)
                 {
                    $user_row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($username != $user_row['username']){
                    $error = "User does not exist. Click sign up to register";
                    }
                    else{
                    if($user->change_password($username, $new_password))
                        $success = "Password succefully reset now click sign in to login";
                    else
                        $error = "Sorry could not reset your password. Try again later";
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
<!Doctype <!DOCTYPE html>
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
                        <?php echo($error); ?>
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
            <input type="password" class="form-control" name="new_password" placeholder="Enter new password" pattern="(?=\S*\d)(?=\S*[a-z])(?=\S*[A-Z])\S*" 
                title="Passwords must have digits, caps and small letters" required="true"/>
        </div>

        <div class="form-group">
            <input type="password" class="form-control" name="confirm_password" placeholder="Re-enter password" pattern="(?=\S*\d)(?=\S*[a-z])(?=\S*[A-Z])\S*"
                title="Passwords must have digits, capsa and small leteters" required="true">
        </div>
        <hr />
        
        <div class="form-group">
            <button type="submit" name="btn-confirm">Confirm</button><br /><br />
            <label>Not registered? <a href="../index.php">Sign in</a></label>
        </div><br />
    </form>
</div>
</body>
</html>