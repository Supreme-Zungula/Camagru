<?php

    require_once("../config/class.user.php");
    require_once("session.php");

    $auth_user = new USER();


    $uname = $_SESSION['user_session'];
    $stmt = $auth_user->run_query("SELECT * FROM users WHERE username=:username");
    $stmt->execute(array(":username"=>$uname));

    $user_row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (isset($_POST['confirm-details-btn']))
    {
        $firstname = strip_tags($_POST['firstname']);
        $lastname = strip_tags($_POST['lastname']);
        $username = strip_tags($_POST['username']);
        $email = strip_tags($_POST['email']);
       

        if ($_POST['email_not'] == "Yes")
            $notifications = "Yes";
        else
            $notifications = "No";
            
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
        else{
            if ($auth_user->update_details($uname, $firstname, $lastname, $username, $email, $notifications)){
                $succes_msg = "Details successfully updated";
                $message = "Hi $firstname $lastname\n\nYou have updated your Camagru account details just so you know. "
                        . "You better not forgot those new details you just added peasant";
                mail($email, "Account details", $message);
                $_SESSION['user_session'] = $username;
            }
            else{
                $error[] = "Sorry could not update your details, try again later";
            }
        }
    }

    if (isset($_POST['confirm-passwords-btn']))
    {
        $old_password = strip_tags($_POST['old_password']);
        $new_password = strip_tags($_POST['new_password']);
        $confirm_pass = strip_tags($_POST['confirm_password']);

        if (empty($old_password)){
            $pwd_error[] = "Enter your old password, you peasant";
        }
        else if (empty($new_password)){
            $pwd_error[] = "Enter your new password";
        }
        else if (empty($confirm_pass)){
            $pwd_error[] = "Confirm your new password, peasant";
        }
        else if ($new_password != $confirm_pass){
            $pwd_error[] = "New passwords  do not match";
        }
        else{
            if ($auth_user->change_password($uname, $new_password))
            {
                $message = "Hi\nYou have changed your password to $new_password";
                mail($user_row['email'], "Password change", $message);
                $pwd_success = "Password succesfully updated";     
            }
            else{
                $pwd_error[] = "Sorry could not change your password try again later";
            }
        }
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="../css/style.css" type="text/css"  />
  <title>Profile: <?php print($_SESSION['user_session']); ?></title>
</head>

<body>

<div class="container">
  <header class="site-header">
      <a href="../index.php"><h1>Camagru</h1></a>
  </header>
  
    <div class="signin-form">
                
      <form method="post" class="form-signin">
            <h2 class="form-signin-heading">Change profile details</h2><hr />
           <div class="error">
            <?php
                if(isset($error))
                {
                    foreach($error as $error)
                    {
                        ?>
                        <div class="error-alert">
                            <?php echo $error; ?>
                        </div>
                        <?php
                    }
                }
                else if(isset($succes_msg))
                {
                    ?>
                    <div class="alert-info">
                        <?php echo $succes_msg; ?>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="firstname" pattern='[a-zA-Z\-]+' title="This field can only have letters"
                    placeholder="Enter your first name" required="ture" value="<?php echo $user_row['firstname'];?>">
            </div>
    
            <div class="form-group">
                <input type="text" class="form-control" name="lastname" pattern='[a-zA-Z\-]+' title="This field can only have letters" 
                    placeholder="Enter your last name" required="true" value="<?php echo $user_row['lastname'];?>">
            </div>
            
            <div class="form-group">
                <input type="text" class="form-control" name="username" pattern="\w+" title="Can only have letters, digits and underscores" 
                    placeholder="Enter your username" required value="<?php echo $user_row['username'];?>" />
            </div>
            
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Enter your email address" 
                required="true" value="<?php echo $user_row['email'];?>" />
            </div>            

            <label for="email_otification">Get email notfications</label><br />
            <div class="form-control">
                <input type="radio" name="email_not" value="Yes" checked> Yes<br>
                <input type="radio" name="email_not" value="No">No<br>
            </div>
            <hr />

            <div class="form-group">
                <button type="submit" name="confirm-details-btn">Confirm</button>
            </div>
            <br />
            
        </form>
    </div>
    <!-- This form is form changing a password only -->
    <div class="signin-form">
        <form method="post" class="form-signin">
            <h2 class="form-signin-heading">Change password</h2><hr />
                <div class="error">
                    <?php
                        if(isset($pwd_error))
                        {
                            foreach($pwd_error as $error)
                            {
                                ?>
                                <div class="error-alert">
                                    <?php echo $error; ?>
                                </div>
                                <?php
                            }
                        }
                        else if(isset($pwd_success))
                        {
                            ?>
                            <div class="alert-info">
                                <?php echo $pwd_success; ?>
                            </div>
                            <?php
                        }
                    ?>
                </div>
            <div class="form-group">
                <input type="password" class="form-control" name="old_password" minlength="6" pattern="(?=\S*\d)(?=\S*[a-z])(?=\S*[A-Z])\S*" 
                    title="Must have digits, caps and small letters" placeholder="Enter your old password" required="true" />
            </div>
            
            <div class="form-group">
                <input type="password" class="form-control" name="new_password" minlength="6" pattern="(?=\S*\d)(?=\S*[a-z])(?=\S*[A-Z])\S*" 
                    title="Must have digits, caps and small letters" placeholder="Enter your new Password" required="true" />
            </div>
            
            <div class="form-group">
                <input type="password" class="form-control" name="confirm_password" minlength="6" pattern="(?=\S*\d)(?=\S*[a-z])(?=\S*[A-Z])\S*" 
                title="Must have digits, caps and small letters" placeholder="Confirm your new password" />
            </div><hr>
            <button type=submit name="confirm-passwords-btn">confirm</button>
        </form>       
    </div>
    <script src="../js/validate.js"></script>
    <?php include_once "footer.php";?>
</div>

</body>
</html>