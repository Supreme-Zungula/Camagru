<?php
$err = 1;
?>
<!Doctype <!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Page Title</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" media="screen" href="./css/main.css" />
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
			<button type="text"><a href="">Logout</a></button>
		</nav>
		<div class="form_div">
			<label for="signUp_form">Sign up form:</label>
			<form name="signUp_form" method="POST" action="./public/sign_up.php" onsubmit="return validateForm()">
				<input type="text" name="firstname" pattern="[a-zA-Z\-]+" title="Must have letters" required="required" placeholder="Enter your first name">
				<?php if ($err == 0)echo"<span class=\"error\"></span>";?>
				<input type="text" name="lastname" pattern="[a-zA-Z\-]+" title="Must have letters" required="required" placeholder="Enter your last name">
				<?php if ($err == 0)echo"<span class=\"error\"></span>";?>
				<input type="text" name="username" pattern="\w+" title="Can only have letters, digits and underscores"
						required="required" placeholder="Enter a username">
				<?php if ($err == 0)echo"<span class=\"error\"></span>";?>
				<input type="email" name="email" title="Invalid email address pattern" required="required"  placeholder="example@email.com">
				<?php if ($err == 0)echo"<span class=\"error\"></span>";?>
				<input type="password" name="password" minlength="6" pattern="(?=\S*\d)(?=\S*[a-z])(?=\S*[A-Z])\S*"
						title="Must be at least 6 character and have uppercase, lowercase letters and digits" required="required" placeholder="Enter your password">
				<?php if ($err == 0)echo"<span class=\"error\"></span>";?>
				<input type="password" name="confirmPassword" minlength="6" pattern="(?=\S*\d)(?=\S*[a-z])(?=\S*[A-Z])\S*"
						required="required" placeholder="Re-enter your password">
				<?php if ($err == 0)echo"<span class=\"error\"></span>";?>
				<button type="submit" name="submit" value="OK">Sign up</button>
			</form>
		</div>
	</div>
</body>
</html>
