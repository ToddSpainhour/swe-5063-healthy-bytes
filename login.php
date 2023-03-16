<?php
$servername = "localhost";
$user = "root";
$pass= "";
$db = "FoodEntryDB";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
  
    // Connect to the database
    $conn = mysqli_connect($servername, $user, $pass, $db);
  
    // Check if the username and password are valid
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) == 1) {

        session_start();
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['userID'] = $result->fetch_assoc()['id'];
        
      //  $_SESSION['userID'] = $userID;
        header('Location: days.php');
      } else {
        // Login failed
        echo '<div class="alert alert-danger" role="alert">Invalid username or password</div>';
      }
    }

    ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
   <style>
      .form-container {
        width: 400px;
        margin: 0 auto;
        padding: 30px;
        background-color: #f7f7f7;
        border-radius: 10px;
        box-shadow: 0 0 10px #ccc;
      }
      h3 {
        text-align: center;
        margin-bottom: 30px;
      }
    </style>
  </head>
  <body>
    <div class="form-container">
      <h3>Macro Tracker Login</h3>
      <form action="login.php" method="post">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
      </form>
      <div class="text-center mt-3">
        Don't have an account? <a href="register.php">Register</a>
      </div>
    </div>
  </body>
</html>


<!-- // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//   $username = $_POST['username'];
//   $password = $_POST['password'];

//   // Connect to the database
//   $conn = new mysqli($servername, $username, $password, $db);

//   // Create a Database
//   $sql = "CREATE TABLE users (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     username VARCHAR(50) NOT NULL,
//     password VARCHAR(255) NOT NULL
//   )";
  
//   if (mysqli_query($conn, $sql)) {
//     echo "Table users created successfully";
//   } else {
//     echo "Error creating table: " . mysqli_error($conn);
//   }  
 
//   // Insert login information into database
//   $username = $_POST['username'];
//   $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

//   $sql = "INSERT INTO users (username, password)
//   VALUES ('$username', '$password')";

//   if (mysqli_query($conn, $sql)) {
//    echo "New record created successfully";
//  } else {
//    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
// }
//} -->