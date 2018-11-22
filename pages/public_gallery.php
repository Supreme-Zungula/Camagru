<?php

    require_once("../config/class.user.php");
    // require_once("session.php");
  
	$user = new USER();
	
	// $username = $_SESSION['user_session'];
    
    //set number of pictures per page
    $query = $user->run_query("SELECT media_name FROM media");
    $query->execute();
    $number_of_results = $query->rowCount();
    
    $results_per_page = 6;
    //determine the number of pages needed
    $number_of_pages = ceil($number_of_results / $results_per_page);

    $page = 1;
    // determine whit page number the visitor is currently on
    if (!isset($_GET['page'])){
        $page = 1;
    }
    else{
        $page = $_GET['page'];
    }

    // determine the SQL limit starting number for the results on the displaying page
    $start_limit = ($page - 1) * $results_per_page;

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
  <title>Public gallery</title>
</head>

<body>

<div class="container">
  <header class="site-header">
      <a href="../index.php"><h1>Camagru</h1></a>
  </header>

  <nav class="nav-bar">
      <label class="h5">Welcome to Camagru</label><hr />
  </nav>

  <div class="grid-container">
      <?php
        if (!empty($pictures)){
          foreach($pictures as $pic)
          {
              ?>
                <div class='grid-item'>
                    <?php echo "<img src='../media/images/". $pic['media_name'] . "'>"; ?>
                    <label id="image-label" for="image"><?php echo "Uploaded by: " . $pic['username'];?></label>
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
        echo "<a href='public_gallery.php?page=" . $page . "'>" . $page . "-</a>";
    ?>
  </div>
<?php include_once "footer.php";?>
</div>

</body>
</html>