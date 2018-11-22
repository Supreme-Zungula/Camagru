<?php
    require_once "../config/class.user.php";
    include_once "session.php";
    
    $user = new USER();
    $username = $_SESSION['user_session'];
   
	/* retrieves recent images uploaded by the user */
 	$query = $user->run_query("SELECT media_name FROM media WHERE username=:username
                            ORDER BY upload_time DESC LIMIT 6");
    $query->bindParam(":username", $username, PDO::PARAM_STR);
    $query->execute();
    $user_pics = $query->fetchAll();
    if($query->rowCount() < 1)
    {
        $photo_msg = "Welcome! You currently have no pictures uploaded<br />" .
                "Click choose file or webcam to upload your pictures";
    }
    /* This handles uploading of pictures from from the device's internal storage */
    if (isset($_POST["upload-btn"]) && !empty($_FILES['fileToUpload']['name']))
    {
        $unique_id = uniqid();
        $target_dir = "../media/images/";
        $uploadOk = 1;
        $target_file = $target_dir . $unique_id . basename($_FILES["fileToUpload"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $is_image = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        
        if ($is_image === false)
        {
            $error_msg = "File is not an image.";
            $uploadOk = 0;
        }
        // Allow certain file formats
        $allow_types = array('jpg','png','jpeg','gif');
        if(!in_array($imageFileType, $allow_types))
        {
            $error_msg  = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        else {
            // if everything is ok, try to upload file
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                if ($user->upload_file($username, $unique_id . $_FILES['fileToUpload']['name'])){
                    $success_msg = "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
                    $user->redirect('upload.php');
                }
                else{
                    $error_msg = "Sorry, an error occured while uploading your file. Try again";
                }
            }
            else {
                $error_msg = "Sorry, there was an error uploading your file.";
            }
        }
    }
    else{
        $error_msg = "No file chosen";
    }

    /* This part will handle uploading of pictures taken with the webcam */
    if (isset($_POST['save-btn']))
    {
        $data = $_POST['image_data'];
        $img = explode(",", $data);
        $img = base64_decode($img[1]);
        
        $image_dir = "../media/images/";
        $username = $_SESSION['user_session'];
        $image_id = "webcam_" . uniqid();
        if (!file_exists($image_dir))
        {
            mkdir($image_dir);
        }
        $filename = $image_id . '.jpeg';
        if(file_put_contents($image_dir . $filename, $img))
        {
            if($user->upload_file($username, $filename)){
                $success_msg = "$filename: has been successfully uploaded";
                $user->redirect('upload.php');
            }
            else{
                $error_msg = "There was error while uploading the file. Try again";
            }
        }
        else{
            $error_msg = "An error occured while uploading your file. Try again";
        }
    }
?>

<!doctype <!DOCTYPE html>
<html>
        <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Upload: <?php print($_SESSION['user_session']);?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" media="screen" href="../css/style.css" />
        </head>
        <body>
        <div class="container">
        <header class="site-header">
            <a href="../index.php"><h1>Camagru</h1></a>
        </header>

        <?php include "navigation.php"?>
        <script>
           
        </script>

        <div class="upload-section">
            <form name="upload-image-form" enctype="multipart/form-data" method="POST">
                <?php        
                    if(isset($error_msg) && isset($_POST['upload-btn']))
                        echo "<div class='error-alert'>$error_msg.</div>";
                    else if (isset($success_msg) && isset($_POST['upload-btn']))   
                        echo "<div class='alert-info'>$success_msg.</div>";   
                ?>
                <label for="upload-image-form" >Upload Image</label>
                <input type="file" class='form-control' name="fileToUpload">
                <button type="submit" name="upload-btn" value="OK">Upload</button>
            </form><hr />
        </div>
        
        <div class="snap-section">
            <label for="stickers">Click on a sticker to add to your image below</label>
            <div id="stickers-container">
                <img id="sticker1" class="grid-item sticker" src="../media/stickers/Hair_1.png" onclick="add_sticker(src)">
                <img id="sticker2" class="grid-item sticker" src="../media/stickers/Hair_2.png" onclick="add_sticker(src)">
                <img id="sticker3" class="grid-item sticker" src="../media/stickers/problem-bro.svg" onclick="add_sticker(src)">
                <img id="sticker5" class="grid-item sticker" src="../media/stickers/beard-silhouette.png" onclick="add_sticker(src)">
                <img id="sticker7" class="grid-item sticker" src="../media/stickers/hipster-sunglasses.png" onclick="add_sticker(src)">
                <img id="sticker9" class="grid-item sticker" src="../media/stickers/sunglasses.png" onclick="add_sticker(src)">
                <img id="sticker9" class="grid-item sticker" src="../media/stickers/gold-frame.png" onclick="add_sticker(src)">
                <img id="sticker9" class="grid-item sticker" src="../media/stickers/brick-frame.png" onclick="add_sticker(src)">
                <img id="sticker9" class="grid-item sticker" src="../media/stickers/frame.png" onclick="add_sticker(src)">
                <br />
            </div>

            <label>Snap section</label><br />
            <video autoplay id="video"></video>
            <canvas id="canvas" ></canvas><br />
            <button id="capture-btn">Capture </button>
            <form name="upload-cam-image-form" method="POST">
                <input type="hidden" id="image" name="image_data">
                <button type="submit" id="save-btn" name="save-btn" value="OK">Save</button>
            </form>
            <p id="test"></p>
        </div><hr/>

        <label>Recent photos</label>
        <div class="media-section">
            <div class="grid-container">
                <?php
                    if (isset($photo_msg))
                        echo "<h1>" . $photo_msg . "</h1>";
                    else{
                        foreach($user_pics as $pic)
                        {
                            ?>
                                <div class="grid-item">
                                    <?php echo "<img src='../media/images/". $pic['media_name'] . "'>";?>
                                </div>
                            <?php
                        }
                    }
                ?>
            </div>  
        </div>
        <?php include_once "footer.php";?>
        <script src="../js/snap.js"></script>
        </body>
</html>