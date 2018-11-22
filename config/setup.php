<?php
    require "database.php";
    
    $DB_DSN = "mysql:host=localhost";
    $DB_NAME = "Camagru_DB";
    $DB_USER = "root";
    $DB_PASSWORD = "abc123";
    
    function init_database($server, $user, $password, $dbname)
    {
        try
        {
            $conn = new PDO($server, $user, $password);
            $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
            echo("Database successfully created <br />");
            $conn = null;
        }
        catch(PDOException $ex)
        {
            echo("ERROR: failed to create database " . $ex->getMessage());
        }
    }

    function add_table_to_database($table_query)
    {
        try
        {
            $db = new Database();
            $db->dbConnection();
            $db->conn;
            $db->conn->exec($table_query);
            echo("Table added <br />");
        }
        catch(PDOException $ex)
        {
            echo("ERROR: could not perform query " . $ex->getMessage());
        }
    }

    $users_table = "CREATE TABLE IF NOT EXISTS`users`(
        `user_id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
        firstname VARCHAR(30) NOT NULL,
        lastname VARCHAR(30) NOT NULL,
        username VARCHAR(30) NOT NULL,
        email VARCHAR(50) NOT NULL,
        `password` VARCHAR(100) NOT NULL,
        notifications VARCHAR(3) DEFAULT 'Yes',
        confirmed VARCHAR(3) DEFAULT 'NO',
        reg_date TIMESTAMP
        )";

    $media_table = "CREATE TABLE IF NOT EXISTS `media`(
        media_id INT(6) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(30) NOT NULL, 
        media_name TEXT NOT NULL,
        upload_time TIMESTAMP
        )";
    $comments_table = "CREATE TABLE IF NOT EXISTS `comments`(
        comment_id INT(6) AUTO_INCREMENT PRIMARY KEY,
        media_id INT(6) NOT NULL,
        username VARCHAR(30) NOT NULL,
        comment TEXT NOT NULL,
        comment_date TIMESTAMP
        )";
    $likes_table = "CREATE TABLE IF NOT EXISTS `likes`(
        like_id INT(6) AUTO_INCREMENT PRIMARY KEY,
        media_id INT(6) NOT NULL,
        username VARCHAR(30) NOT NULL,
        like_date TIMESTAMP
        )";
    init_database($DB_DSN, $DB_USER, $DB_PASSWORD, $DB_NAME);
    add_table_to_database($users_table);
    add_table_to_database($media_table);
    add_table_to_database($comments_table);
    add_table_to_database($likes_table);

?>