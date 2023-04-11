<?php
$servername = "localhost";
$user = "root";
$pass= "";
$db = "FoodEntryDB";
        
        // Create connection
        $conn = mysqli_connect($servername, $user, $pass, $db);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
?>
