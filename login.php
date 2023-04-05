<?php
$servername = "localhost";
$user = "root";
$pass= "";
$db = "FoodEntryDB";
$error_message = '';

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
        
        header('Location: dashboard.php');
    } else {
        // Login failed
        $error_message = 'Invalid username or password';
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
      <?php if ($error_message): ?>
        <div class="alert alert-danger" role="alert">
          <?php echo $error_message; ?>
        </div>
      <?php endif; ?>
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
