<?php 
function send_mail($email)
{
	$subject = "Email confirmation";
	$link = "http://localhost:8081/camagru/index.php?id=";
	$message = "Hi\n\nWelcome to Camagru please click on the link below to confirm your email\n";
	mail($email, $subject, $message . $link . hash("whirlpool",$email));
	echo("\nEmail sent");
}
?>