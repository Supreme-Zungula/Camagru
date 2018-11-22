<?php
    require_once "session.php";
    require_once "../config/class.user.php";

    $user = new USER();
    
    // redirect user if not logged in
    if ($user->is_loggedin() === false){
        $user->redirect("../index.php");
    }
    /* retrieve images from the database */
    $query = $user->run_query("SELECT * FROM media WHERE media_id=:image_id AND media_name=:image_name");
    $query->bindParam(":image_id", $_GET['image_id'], PDO::PARAM_STR);
    $query->bindParam(":image_name", $_GET['image'], PDO::PARAM_STR);
    $query->execute();
    $picture = $query->fetch(PDO::FETCH_ASSOC);
    /* Retrieves from the database when the picture loads */
    if (isset($_GET['image_id']))
    {
        $image_id = strip_tags($_GET['image_id']);
        /* retrieve latest comments from the database */
        $stmt = $user->run_query("SELECT * FROM comments WHERE media_id=:image_id
                    ORDER BY comment_date ASC LIMIT 18");
        $stmt->bindParam(":image_id", $image_id, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() >= 1)
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Adds comment to the comments table  */
    if (isset($_POST['comment-btn']) && !empty($_POST['comment_input'])){
        $image_id = strip_tags($_POST['image_id']);
        $comment = strip_tags($_POST['comment_input']);
        
        $query = $user->run_query("INSERT INTO comments(media_id, username, comment)
                VALUES(:image_id, :username, :comment)");
        $query->bindParam(":image_id", $image_id, PDO::PARAM_STR);
        $query->bindParam(":username", $_SESSION['user_session'], PDO::PARAM_STR);
        $query->bindParam(":comment", $comment, PDO::PARAM_STR);
        $query->execute();
        
        /* send email notification to the user if needs be */
        $query = $user->run_query("SELECT username FROM media WHERE media_id=:image_id");
        $query->bindParam(":image_id", $image_id, PDO::PARAM_STR);
        $query->execute();
        $user_row = $query->fetch(PDO::FETCH_ASSOC);
        
        $query = $user->run_query("SELECT email, notifications FROM users WHERE username=:uname");
        $query->bindParam(":uname", $user_row['username'], PDO::PARAM_STR);
        $query->execute();
        $user_details = $query->fetch(PDO::PARAM_STR);
        if ($user_details['notifications'] === 'Yes')
        {
            $header = "Hi " . $user_row['username'] . "\n\n"; 
            $message = $_SESSION['user_session'] . " Just comment on you photo. Click here to reply http://localhost:8080/camagru";
            mail($user_details['email'], 'Notifications' ,$message, $header);
        }
        // $user->redirect("comments.php?image_id=" . $image_id . "&image=" . $_GET['image']);
    }
    /* retrieve latest comments from the database when person comments*/
    if (isset($_POST['image_id']))
    {
        $image_id = strip_tags($_POST['image_id']);
        $stmt = $user->run_query("SELECT * FROM comments WHERE media_id=:image_id
                    ORDER BY comment_date ASC LIMIT 18");
        $stmt->bindParam(":image_id", $image_id, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() >= 1)
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/style.css" type="text/css"  />
    <title>Comments</title>
</head>
<body>
<div class="container">
  <header class="site-header">
      <a href="../index.php"><h1>Camagru</h1></a>
  </header>
  <?php include "navigation.php"?>
  <div class="grid-container">
      <div class="grid-item" id="image-div">
          <?php echo "uploaded by: ". $picture['username'];?>
          <?php echo "<img src='../media/images/". $picture['media_name'] . "'>";?>
      </div>
      <div class="grid-item" id="comments-div">
          <div id="comments">
            <?php
                if(isset($comments)){
                    echo "<br >";
                    foreach($comments as $comment)
                        echo "<p class='comment'><strong color='white'>".$comment['username']."</strong>: " .$comment['comment'] ."</p>";
                }
            ?>
          </div>
          <textarea name="comment_input" form="comments-form"></textarea><br />
          <form method="POST" id="comments-form">
              <input type="hidden" name="image_id" value="<?php echo strip_tags($_GET['image_id']);?>">
              <button type="submit" name="comment-btn" value="OK">Comment</button>
          </form>
      </div>
  </div>
<?php include_once "footer.php";?>

</div>
</body>
</html>