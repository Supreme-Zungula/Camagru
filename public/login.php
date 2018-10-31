<?php
	session_start();

	function user_exists($username){
		require "../config/database.php";

		try
		{
			$pdo = new PDO("$DB_DSN;dbname=$DB_NAME", $DB_USER, $DB_PASSWORD);
			$stmt = $pdo->prepare("SELECT username, `password` FROM users WHERE username=?");
			$stmt->execute([$username]); 
			$user =$stmt->fetch();
			return ($user);
		}
		catch(PDOException $ex)
		{
			echo("ERROR: Connection failed ". $ex.getMessage());
		}
	}
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$username = strtolower($_POST['username']);
		$password = hash("whirlpool", $_POST['password']);
		$user = user_exists($username);
		if ($user)
		{
			if ($user['password'] != $password)
				echo("Incorrect password");
			else
				header("location:../index.php");
		}
		else
			echo("You are not registered");
	}
?>
<!Doctype <!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Login</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" media="screen" href="../css/main.css" />
	<script src=""></script>
</head>
<body>
	<div class="container">
		<header id="site_header"><h1>Camagru</h1></header>
		<nav id="tabs">
			<button type="text"><a href="">Home</a></button>
			<button type="text"><a href="">Gallery</a></button>
			<button type="text"><a href="">Upload</a></button>
			<button type="text"><a href="">Profile</a></button>
		</nav>
		<form name="loginForm" action="./login.php" method="POST">
			<label for="login">Login</label>
			<input type="text" name="username" value="" pattern="\w+" required="required" placeholder="Enter your username">
			<input type="password" name="password" value="" minlength="6" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*"
			 		placeholder="Enter your password" required>
			<button type="submit" value="OK">OK</button>
		</form>
	</div>	
</body>
</html>
