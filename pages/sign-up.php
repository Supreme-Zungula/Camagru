<?php
session_start();
require_once('../config/class.user.php');
$user = new USER();

if($user->is_loggedin() == true)
{
	$user->redirect('home.php');
}

if(isset($_POST['btn-signup']))
{
	$firstname = strip_tags($_POST['firstname']);
	$lastname = strip_tags($_POST['lastname']);
	$username = strip_tags($_POST['username']);
	$email = strip_tags($_POST['email']);
	$password = strip_tags($_POST['password']);
	$confirm_password = strip_tags($_POST['confirm_password']);	
	 
	if ($firstname == ""){
		$error[] = "Please provide your first name";
	}
	else if ($lastname == ""){
		$error[] = "Please provide your last name";
	}
	else if($username ==""){
		$error[] = "Please provide your username";	
	}
	else if($email=="")	{
		$error[] = "Provide email your email address";	
	}
	else if(!filter_var($email, FILTER_VALIDATE_EMAIL))	{
	    $error[] = 'Please enter a valid email address';
	}
	else if($password=="")	{
		$error[] = "Please provide a password";
	}
	else if(strlen($password) < 6){
		$error[] = "Password must be atleast 6 characters";	
	}
	else if ($confirm_password == ""){
		$error[] = "Please confirm your password";
	}
	else if ($password != $confirm_password){
		$error[] = "Passwords do not match";
	}
	else
	{
		try
		{
			$stmt = $user->run_query("SELECT `username`, `email` FROM users WHERE `username`=:username OR `email`=:email");
			$stmt->execute(array(':username'=>$username, ':email'=>$email));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() >= 1)
			{	if($row['username'] == $username) {
					$error[] = "Sorry username already taken !";
				}
				else if($row['email'] == $email) {
					$error[] = "Sorry email is already taken !";
				}
			}
			else
			{
				if (isset($error))
					$user->redirect("index.php");
				if($user->register($firstname, $lastname, $username, $email,$password))
				{	
					$hash = hash("whirlpool", $email);
					$subject = "Camagru account confirmation";
					$link = "http://localhost:8080/camagru/pages/verify_user.php?id=$username&key=$hash";
					$message = "Hi \n Please click on the link below to comfirm you Camagru account\n" . $link;
					mail($email, $subject, $message);
					$user->redirect('sign-up.php?joined');
				}
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}	
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Camagru: Sign up</title>
	<link rel="stylesheet" href="../css/style.css" type="text/css"  />
</head>
<body>
    <div class="container">
        <header class="site-header">
            <a href="../index.php"><h1>Camagru</h1></a>
        </header>

        <div class="signin-form">
                
                <form method="post" class="form-signin">
                    <h2 class="form-signin-heading">Sign up.</h2><hr />
                    <?php
                    if(isset($error))
                    {
                        foreach($error as $error)
                        {
                            ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                            <?php
                        }
                    }
                    else if(isset($_GET['joined']))
                    {
                        ?>
                        <div class="alert alert-info">
                            Successfully registered. Check your email to confirm your account
                        </div>
                        <?php
                    }
                    ?>
                    <div class="form-group">
						<input type="text" class="form-control" name="firstname" pattern='[a-zA-Z\-]+' title="This field can only have letters"
								placeholder="Enter your first name" required="ture" value="<?php if(isset($error)){echo $firstname;}?>">
                    </div>
            
                    <div class="form-group">
						<input type="text" class="form-control" name="lastname" pattern='[a-zA-Z\-]+' title="This field can only have letters" 
								placeholder="Enter your last name" required="true" value="<?php if(isset($error)){echo $lastname;}?>">
                    </div>
                    
                    <div class="form-group">
						<input type="text" class="form-control" name="username" pattern="\w+" title="Can only have letters, digits and underscores" 
								placeholder="Enter your username" required value="<?php if(isset($error)){echo $username;}?>" />
                    </div>
                    
                    <div class="form-group">
						<input type="email" class="form-control" name="email" placeholder="Enter your email address" 
							required="true" value="<?php if(isset($error)){echo $email;}?>" />
                    </div>
                    
                    <div class="form-group">
						<input type="password" class="form-control" name="password" minlength="6" pattern="(?=\S*\d)(?=\S*[a-z])(?=\S*[A-Z])\S*" 
								title="Must have digits, caps and small letters" placeholder="Enter Password" required="true" />
                    </div>
                    
                    <div class="form-group">
						<input type="password" class="form-control" name="confirm_password" minlength="6" pattern="(?=\S*\d)(?=\S*[a-z])(?=\S*[A-Z])\S*" 
							title="Must have digits, caps and small letters" placeholder="Re-Enter your password" />
                    </div>
    
                    <hr />
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" name="btn-signup">SIGN UP</button>
                    </div>
                    <br />
                    <label>Have an account? <a href="../index.php">Sign In</a></label>
                </form>
          
        </div>
    <?php include_once "footer.php";?>
    </div>
</body>
</html>