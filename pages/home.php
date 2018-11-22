<?php

  require_once("../config/class.user.php");
  require_once("session.php");
  
	$user = new USER();
	
	$username = $_SESSION['user_session'];
  
  if (isset($_GET['like-btn'])){
    $query = $user->run_query("SELECT * FROM media WHERE media_name=:pic_name");
    $query->bindParam(":pic_name", $_GET['image'], PDO::PARAM_STR);
    $query->execute();
    $pic_row = $query->fetch(PDO::FETCH_ASSOC);

    //check if liked already
    $query = $user->run_query("SELECT * FROM likes WHERE media_id=:media_id AND username=:uname");
    $query->bindParam(":media_id", $pic_row['media_id'], PDO::PARAM_STR);
    $query->bindParam(":uname", $_SESSION['user_session'], PDO::PARAM_STR);
    $query->execute();
    if ($query->rowCount() < 1)
    {
      //add like to the likes table
      $query = $user->run_query("INSERT INTO likes(media_id, username)
              VALUES(:media_id, :username)");
      $query->bindParam(":media_id", $pic_row['media_id'], PDO::PARAM_STR);
      $query->bindParam(":username", $_SESSION['user_session'], PDO::PARAM_STR);
      $query->execute();
    }
    else{
      //remove like from likes table
      $query = $user->run_query("DELETE FROM likes WHERE media_id=:media_id AND username=:uname");
      $query->bindParam(":media_id", $pic_row['media_id'], PDO::PARAM_STR);
      $query->bindParam(":uname", $_SESSION['user_session'], PDO::PARAM_STR);
      $query->execute();
    }
  }
  
  // retrieve all pictures from the database
  $query = $user->run_query("SELECT media_name FROM media");
  $query->execute();
  $results = $query->rowCount();
  
  //set number of pictures per page
  $results_per_page = 6;

  //determine the number of pages needed
  $number_of_pages = ceil($results / $results_per_page);

  $current_page = 1;
  // determine whit page number the visitor is currently on
  if (!isset($_GET['page'])){
    $currrent_page = 1;
  }
  else{
    $current_page = $_GET['page'];
  }

  // determine the SQL limit starting number for the results on the displaying page
  $start_limit = ($current_page - 1) * $results_per_page;

  // retrieve data from the database
  $query = $user->run_query("SELECT * FROM media ORDER BY upload_time DESC
                  LIMIT $start_limit , $results_per_page");
  $query->execute();
  if($query->rowCount() < 1)
    $empty_gallery = "This is where recently posted images by the user will appear";
  else
    $pictures = $query->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="../css/style.css" type="text/css"  />
  <title>Home: <?php print($_SESSION['user_session']); ?></title>
</head>

<body>

<div class="container">
  <header class="site-header">
      <a href="../index.php"><h1>Camagru</h1></a>
  </header>
  <?php include "navigation.php"?>
  <div class="grid-container">
      <?php
        if (!empty($pictures)){
          foreach($pictures as $pic)
          {
              ?>
                <div class='grid-item'>
                  <!-- <label for="username"><?php echo $pic['username'];?></label> -->
                  <a href="<?php echo "comments.php?image_id=" . $pic['media_id'] . 
                          "&image=" . $pic['media_name'];?>">
                    <?php echo "<img src='../media/images/". $pic['media_name'] . "'>";?>
                  </a>
                  <form name="comment-like-form" method="GET">
                    <input type="hidden" name="image" value="<?php echo $pic['media_name'];?>"/>
                    <button type="submit" class="like-btn" name="like-btn" value="1">Like</button>
                    <label for="like" name="like-label">
                      <?php
                        $query = $user->run_query("SELECT * FROM likes WHERE media_id=:pic_id"); 
                        $query->bindParam(":pic_id", $pic['media_id'], PDO::PARAM_STR);
                        $query->execute();
                        echo $query->rowCount();
                      ?>
                  </form>
                </div>
                <?php
          }
        }
        else{
            echo "<h1>" . $empty_gallery . "</h1>";
        }
        ?>
  </div>
  <div class="page_numbers">
    <?php  // display links to pages
      for($page=1; $page <= $number_of_pages; $page++)
        echo "<a href='home.php?page=" . $page . "'>" . $page . "-</a>";
    ?>
  </div>
<?php include_once "footer.php";?>
</div>

</body>
</html>