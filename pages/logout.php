<?php
	require_once('../config/class.user.php');
	require_once('session.php');
	$user_logout = new USER();
	
	if(isset($_GET['logout']) && $_GET['logout']== true)
	{
		$user_logout->logout();
		$user_logout->redirect('../index.php');
	}
