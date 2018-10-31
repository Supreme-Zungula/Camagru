<?php
	require "database.php";
	require "setup_func.php";

	$server = $DB_DSN;
	$user = $DB_USER;
	$password = $DB_PASSWORD;
	$dbname = $DB_NAME;

	init_database($server, $user, $password, $dbname);
	$query = "CREATE TABLE users(
			id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
   	 		firstname VARCHAR(30) NOT NULL,
    		lastname VARCHAR(30) NOT NULL,
			username VARCHAR(30) NOT NULL,
    		email VARCHAR(50) NOT NULL,
			`password` CHAR(128) NOT NULL,
			reg_date TIMESTAMP,
			confirmed VARCHAR(3) DEFAULT 'NO'
			)";
	query_database($server, $user, $password, $dbname, $query);
?>