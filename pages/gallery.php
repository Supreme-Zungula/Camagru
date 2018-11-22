<?php
    require_once "../config/class.user.php";
    include_once "session.php";
    
    
    $user = new USER();
    $username = $_SESSION['user_session'];
    if ($user->is_loggedin() !== true)
    {
        $user->redirect("../index.php");
    }
    /* Handles deletion of a picture from the database */
    if (isset($_GET['delete-btn']) && !empty($_GET['image_data']))
    {
        $filename = strip_tags($_GET['image_data']);
        try{
            $query = $user->run_query("SELECT * FROM media WHERE media_name=:image_name");
            $query->bindParam(":image_name", $filename, PDO::PARAM_STR);
            $query->execute();
            if ($query->rowCount() >= 1){
                $image_row = $query->fetch(PDO::FETCH_ASSOC);
            }
            else
                $delete_fail = "Image does not exist";
        }
        catch(PDOException $ex){
            $delete_fail = "Could not delete image";
        }
        $file_id = $image_row['media_id'];
        if ($user->delete_file($file_id, $filename, $username)){
            $delete_success = "Image deleted";
            // $user->redirect('gallery.php');
        }
        else
            $delete_fail = "Could not delete image";
    }
    $query = $user->run_query("SELECT media_name FROM media WHERE username=:uname");
    $query->bindParam(":uname", $username, PDO::PARAM_STR);
    $query->execute();
    $results = $query->rowCount();
    
    //set number of pictures per page
    $results_per_page = 6;
    
    //determine the number of pages needed
    $number_of_pages = ceil($results / $results_per_page);
    $current_page = 1;
    // determine whit page number the visitor is currently on
    if (!isset($_GET['page'])){
        $current_page = 1;
    }
    else{
        $current_page = $_GET['page'];
    }

    // determine the SQL limit starting number for the results on the displaying page
    $start_limit = ($current_page - 1) * $results_per_page;

    // retrieve data from the database
    $query = $user->run_query("SELECT media_name FROM media WHERE username=:username
                    ORDER BY upload_time DESC LIMIT $start_limit , $results_per_page");
    $query->bindParam(":username", $username, PDO::PARAM_STR);
    $query->execute();
    if($query->rowCount() < 1)
        $empty_gallery = "This is where recently posted images by the user will appear";
    else
        $pictures = $query->fetchAll();

?>

<!doctype <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Gallery: <?php print($_SESSION['user_session']);?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/style.css" />
</head>
<body>

    <div class="container">
    <header class="site-header">
        <a href="../index.php"><h1>Camagru</h1></a>
    </header>
    
    <?php include "navigation.php"?>

        <div id="error">
            <?php
                if(isset($error_msg))
                {
                    ?>
                    <div class="error-alert">
                        <?php foreach($error_msg as $error) echo($error); ?>
                    </div>
                    <?php
                }
                else if (isset($success_msg))
                {
                    ?>
                    <div class="alert-info">
                        <?php echo $success_msg ?>
                    </div>
                    <?php
                }
                ?>
        </div>

        <label>Media section</label>
        <div class="media-section">
            <?php        
                if(isset($delete_fail))
                    echo "<div class='error-alert'>$delete_fail.</div>";
                else if (isset($delete_success))   
                    echo "<div class='alert-info'>$delete_success.</div>";   
            ?>
            <div class="grid-container">
                <?php
                    if (!empty($pictures)){
                        foreach($pictures as $pic)
                        {
                            ?>
                                <div class='grid-item'>
                                    <?php echo "<img src='../media/images/". $pic['media_name'] . "'>"; ?>
                                    <form name="delete-image-form" method="GET">
                                        <input type="hidden" id="image" name="image_data" value="<?php echo $pic['media_name'];?>">
                                        <button type="submit" class="delete-btn" name="delete-btn" value="OK">Delete</button>
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
    </div>
    <div class="page_numbers">
    <?php  // display links to pages
      for($page=1; $page <= $number_of_pages; $page++)
        echo "<a href='gallery.php?page=" . $page . "'>" . $page . "</a>";
    ?>
  </div>
    <?php include_once "footer.php"; ?>
</body>
</html>