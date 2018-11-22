<?php
session_start();
require_once("./config/class.user.php");
$user = new USER();

if($user->is_loggedin() == true)
{
	$user->redirect('./pages/home.php');
}

if(isset($_POST['btn-login']))
{
	$username = strip_tags($_POST['username']);
    $password = strip_tags($_POST['password']);
    if ($user->is_registered($username) == false)
    {
        $error = "You are not registered. Click sign up to register";
    }
    else if ($user->is_verified($username) == false)
    {   
        $error = "You have not confirm your email yet";
    }
	else if($user->login($username, $password) == true)
	{
        $_SESSION['user_session'] = $username;
		$user->redirect('./pages/home.php');
	}
	else
	{
		$error = "Wrong Details";
	}	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Camagru: Login</title>
    <link rel="stylesheet" href="./css/style.css" type="text/css"  />
</head>
<body>
<div id="container">
    <header class="site-header">
        <h1>Camagru</h1>
    </header>
    <div id="public_gallery">
        <a href="pages/public_gallery.php">Click here to view public gallery</a>
    </div>
    <div class="signin-form">

        <form class="form-signin" method="post" id="login-form">
    
            <h2 class="form-signin-heading">Login to Camagru.</h2><hr />
            
            <div id="error">
                <?php
                    if(isset($error))
                    {
                        ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                        <?php
                    }
                ?>
            </div>
            
            <div class="form-group">
                <input type="text" class="form-control" name="username" pattern="\w+" title="This field can only have letters and/or digits" 
                    placeholder="Enter your username" required="true">
            </div>
            
            <div class="form-group">
                <input type="password" class="form-control" name="password" pattern="(?=\S*\d)(?=\S*[a-z])(?=\S*[A-Z])\S*"
                    title="Password must have digits, caps and small letters" placeholder="Enter your Password" required="true" />
            </div>
            <hr />
            
            <div class="form-group">
                <button type="submit" name="btn-login" class="btn btn-default">Login</button>
            </div><br />
            <label>Don't have account yet? <a href="./pages/sign-up.php">Sign Up</a></label>
            <br /><br />
            <label>Forgot your password? <a href="pages/forgot_password.php">click here</a></label>
        </form>
    </div>

</div>
</body>
</html>