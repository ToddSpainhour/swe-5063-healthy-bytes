<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get form data
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $password_confirm = $_POST['password_confirm'];

  // Validate form data
  if (empty($username)) {
    echo '<div class="alert alert-danger" role="alert">Username is required</div>';
  } else if (empty($email)) {
    echo '<div class="alert alert-danger" role="alert">Email is required</div>';
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo '<div class="alert alert-danger" role="alert">Invalid email address</div>';
  } else if (empty($password)) {
    echo '<div class="alert alert-danger" role="alert">Password is required</div>';
  } else if (!preg_match("#[A-Z]+#", $password) || !preg_match("#[\W]+#", $password) || !preg_match("#[0-9]+#", $password)) {
    echo '<div class="alert alert-danger" role="alert">Password must contain at least 1 uppercase letter, 1 special character, and 1 number!</div>';
  } else if ($password != $password_confirm) {
    echo '<div class="alert alert-danger" role="alert">Password confirmation does not match</div>';
  } else {

    // Connect to database
    $servername = "localhost";
    $user = "root";
    $pass= "";
    $db = "FoodEntryDB";

    $conn = mysqli_connect($servername, $user, $pass, $db);
    
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    // Check if username or email already exists
    $sql = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      echo '<div class="alert alert-danger" role="alert">Username or email already exists</div>';
    } else {
      // Hash the password
      //$password = password_hash($password, PASSWORD_DEFAULT);

      // Insert the user into the user table
      $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
      if ($conn->query($sql) === TRUE) {
        header('Location: login.php');
      } else {
        echo '<div class="alert alert-danger" role="alert">Error: ' . $conn->error . '</div>';
      }
    }

    // Close the database connection
    $conn->close();
  }
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register Page</title>
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
      <h3>Create Your Account</h3>
      <form action="register.php" method="post">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
          <label for="password_confirm">Confirm Password</label>
          <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Register</button>
      </form>
      <div class="text-center mt-3">
        Already have an account? <a href="login.php">Login</a>
      </div>
    </div>
  </body>
</html>
