<?php

$DEBUG=false;

// Retrieve Session variables
session_start();
$userID = $_SESSION["userID"];

// Setup connection to database  
$servername = "localhost";
$user = "root";
$pass= "";
$db = "FoodEntryDB"; /*** TODO: CSW Note - Should have these database entry values as shared SESSION global variables since used in multiple PHP files. ***/
$conn = mysqli_connect($servername, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user's recommended values data from the "recommended_values" table in the "FoodEntry" database
$query_recommended_values = "SELECT * FROM recommended_values WHERE userID = $userID";
$result_recommended_values = mysqli_query($conn, $query_recommended_values);
$row_recommended_values = mysqli_fetch_assoc($result_recommended_values);

// Fetch user's data from the "users" table in the "FoodEntry" database
$query_user_data = "SELECT * FROM users WHERE id = $userID";
$result_user_data = mysqli_query($conn, $query_user_data);
$row_user_data = mysqli_fetch_assoc($result_user_data);

// Close the database connection
$conn->close();

function calcCaloriesPerDay($ActivityLevel, $Gender, $WeightInKilograms, $HeightInCentimeters, $Age) {
  /***
  From: https://www.healthline.com/nutrition/how-to-count-macros#step-by-step
  In order to determine your overall calorie needs, you can either use a simple online calculator or the Mifflin-St. Jeor equation:
      Males: calories/day = 10 x weight (kilograms, or kg) + 6.25 x height (centimeters, or cm) – 5 x age (years) + 5
      Females: calories/day = 10 x weight (kg) + 6.25 x height (cm) – 5 x age (years) – 161
  Then, multiply your result by an activity factor — a number that represents different levels of activity (7):
      Sedentary: x 1.2 (limited exercise)
      Lightly active: x 1.375 (light exercise less than 3 days per week)
      Moderately active: x 1.55 (moderate exercise most days of the week)
      Very active: x 1.725 (hard exercise every day)
      Extra active: x 1.9 (strenuous exercise two or more times per day)
  ***/
  switch ($ActivityLevel) {
    case 'LowActivity':       // Sedentary
        $ActivityFactor = 1.2;
        break;
    case 'MedLowActivity':    // Lightly active
        $ActivityFactor = 1.375;
        break;
    case 'MediumActivity':    // Moderately active
        $ActivityFactor = 1.55;
        break;
    case 'MedHighActivity':   // Very active
        $ActivityFactor = 1.725;
        break;
    case 'HighActivity':      // Extra active
        $ActivityFactor = 1.9;
        break;
    default:
        $ActivityFactor = 1.55; // Go with "MediumActivity" by default.
        break;
    }
  if($Gender == "Female") {
      $CaloriesPerDay = round((10 * $WeightInKilograms + 6.25 * $HeightInCentimeters - 5 * $Age + 5)*$ActivityFactor);
  }
  else { // Gender at birth was Male
      $CaloriesPerDay = round((10 * $WeightInKilograms + 6.25 * $HeightInCentimeters - 5 * $Age - 161)*$ActivityFactor);
  }

  /*** TODO: Need to put in an equation here to reduce the number of calories per day for weight loss, or increase for weight gain ***/
  
  /*
  if($DEBUG)
    echo '<div class="alert alert-danger" role="alert">You should consume $CaloriesPerDay calories per day!</div>';
  */

  return($CaloriesPerDay);
} // end function calcCaloriesPerDay()

/****
 TODO - Change my functions to utilize these equations for calculating calories and macros. Need to ask CESAR if this is correct however first.

 How to calculate calories and macro needs based on user input:

BEE formula for calorie needs based on person’s attributes: 

https://www.k-state.edu/paccats/Contents/Nutrition/PDF/Needs.pdf 

Parts of formula:  

Weight in kg (convert from pounds that the user enters) 

Height in cm (convert from ft + inches that the user enters)  

Age in years 

Sex 

Activity Level (Sedentary, Average, High) 

Goal: lose weight, maintain, gain weight  

Use these fields to calculate calorie needs via BEE formula and remember to multiply by multiplier if 	activity level is average (* 1.2) or high (* 1.5). 

Lose weight: reduce calorie intake by 20% 

Maintain weight: no adjustment  

Gain weight: add 10% calories to overall intake 

 

Macronutrients: 

Fat needs: 30% of calories 

Protein: .8g per kg (kg, not pounds) of body weight. Convert to calories: 1g protein = 4 calories 

Carbs: remainder of calories 
 */



function calcMacros($CaloriesPerDay) {
  /*** From https://www.healthline.com/nutrition/how-to-count-macros#benefits:
  Here’s an example of how to calculate macronutrients for a 2,000-calorie diet consisting of 40% carbs, 30% protein, and 30% fat
  Carbs:
    4 calories per g
    40% of 2,000 calories = 800 calories of carbs per day
    Total g of carbs allowed per day = 800/4 = 200 g
  Proteins:
    4 calories per g
    30% of 2,000 calories = 600 calories of protein per day
    Total grams of protein allowed per day = 600/4 = 150 g
  Fats:
    9 calories per g
    30% of 2,000 calories = 600 calories of protein per day
    Total grams of fat allowed per day = 600/9 = 67 g
  In this scenario, your ideal daily intake would be 200 g of carbs, 150 g of protein, and 67 g of fat.
  ***/
  $pctCarbs = 0.4;
  $calsPerCarb = 4;
  $pctProteins = 0.3;
  $calsPerProtein = 4;
  $pctFats = 0.3;
  $calsPerFat = 9;

  $carbsPerDay = $CaloriesPerDay*$pctCarbs/$calsPerCarb;
  $proteinsPerDay = $CaloriesPerDay*$pctProteins/$calsPerProtein;
  $fatsPerDay = $CaloriesPerDay*$pctFats/$calsPerFat;

  /*
  if($DEBUG) {
    echo '<div class="alert alert-danger" role="alert">You should consume $carbsPerDay grams of Carbohydrates per day!</div>';
    echo '<div class="alert alert-danger" role="alert">You should consume $proteinsPerDay grams of Proteins per day!</div>';
    echo '<div class="alert alert-danger" role="alert">You should consume $fatsPerDay grams of Fats per day!</div>';
  }
*/

  $assocArrayKeyStats = array('GramsCarbsPerDay' => $carbsPerDay, 'GramsProteinsPerDay' => $proteinsPerDay, 'GramsFatsPerDay' => $fatsPerDay);
  return($assocArrayKeyStats);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get form data
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $password_confirm = $_POST['password_confirm'];
  $activitylevel = $_POST['activitylevel'];
  $goal = $_POST['goal'];
  $gender = $_POST['gender'];
  $age = $_POST['age'];
  $heightinches = $_POST['heightinches'];
  $weightlbs = $_POST['weightlbs'];
  $lastname = $_POST['lastname'];
  $firstname = $_POST['firstname'];
  $weightkgs = $weightlbs / 2.205;
  $heightmeters = $heightinches * 0.0254;

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

      // Hash the password
      //$password = password_hash($password, PASSWORD_DEFAULT);

      // Insert the user into the user table
      $sql = "UPDATE users SET username='$username', password='$password', email='$email', firstname='$firstname', lastname='$lastname', weight='$weightkgs', height='$heightmeters', age='$age', gender='$gender', activity_level='$activitylevel', goal='$goal' WHERE users.id='$userID'";

      if ($conn->query($sql) === FALSE) {
        echo '<div class="alert alert-danger" role="alert">Error: Could not UPDATE users table in FoodEntryDB. Error info - ' . $conn->error . '</div>';
      }

      // auto-incremented id value created by the database after user insert (after new user registration) 
      $autoIncrementedIDReturnedFromDB = mysqli_insert_id($conn);

      $caloriesPerDay = calcCaloriesPerDay($activitylevel, $gender, $weightkgs, (100*$heightmeters), $age);
      $assocArrayKeyStats = calcMacros($caloriesPerDay);
      $GramsCarbsPerDay = $assocArrayKeyStats['GramsCarbsPerDay'];
      $GramsProteinsPerDay = $assocArrayKeyStats['GramsProteinsPerDay'];
      $GramsFatsPerDay = $assocArrayKeyStats['GramsFatsPerDay'];

      $sql = "UPDATE recommended_values SET fats='$GramsCarbsPerDay', carbs='$GramsProteinsPerDay', proteins='$GramsFatsPerDay', calories='$caloriesPerDay' WHERE recommended_values.userID='$userID'";

      echo '<div class="alert alert-danger" role="alert">GOT THRU 2 QUERIES FOR UserID: ' . $userID .' </div>';
     
      if ($conn->query($sql) === TRUE) {
        header('Location: edit_user_data.php');
      }
      else {
        echo '<div class="alert alert-danger" role="alert">Error: Could not UPDATE recommendedValues table in FoodEntryDB. Error info - ' . $conn->error . '</div>';
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
	<title>Healthy Bytes - Edit/Change Your User Data</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
	<style type="text/css">
		.container {
			margin-top: 50px;
		}
		table {
			margin-top: 50px;
			width: 100%;
		}
	</style>
  </head>
  <body>
    <!-- Navigation Bar -->
    <?php include 'navbar.php'; ?>

    <div class="form-container">
      <h3><center>Edit Your User Account Attributes</center></h3>
      <br/>
      <h5><em>Overwrite current textfield values with desired new data, or leave unchanged if correct</em></h5>
      <form action="edit_user_data.php" method="post">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" class="form-control" id="username" name="username" value="<?php echo $row_user_data['username']; ?>" size="255" required>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" class="form-control" id="email" name="email" value="<?php echo $row_user_data['email']; ?>" size="255" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password" value="<?php echo $row_user_data['password']; ?>" size="255" required>
        </div>
        <div class="form-group">
          <label for="password_confirm">Confirm Password</label>
          <input type="password" class="form-control" id="password_confirm" name="password_confirm" value="<?php echo $row_user_data['password']; ?>" size="255" required>
        </div>
        <div class="form-group">
          <label for="firstname">First Name</label>
          <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $row_user_data['firstname']; ?>" size="255">
        </div>    
        <div class="form-group">
          <label for="lastname">Last Name</label>
          <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $row_user_data['lastname']; ?>" size="255">
        </div>
        <div class="form-group">
          <label for="age">Age</label>
          <input type="text" class="form-control" id="age" name="age" value="<?php echo $row_user_data['age']; ?>" size="50">
        </div>
        <div class="form-group">
          <label for="weightlbs">Weight (in pounds)</label>
          <input type="text" class="form-control" id="weightlbs" name="weightlbs" value="<?php echo ($row_user_data['weight'])*2.205; ?>" size="50"> <!-- 1 kg = 2.205 lbs-->
        </div>
        <div class="form-group">
          <label for="heightinches">Height (in inches)</label>
          <input type="text" class="form-control" id="heightinches" name="heightinches" value="<?php echo ($row_user_data['height'])*39.37; ?>" size="255"> <!--1 meter = 39.37 inches-->
        </div>
        <div class="form-group">
          <label for="gender">Gender At Birth </label>
          <select id="gender" name="gender">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
          <em>| &#8827;&#8827; Current Value: <?php echo $row_user_data['gender']; ?></em>
        </div>
        <div class="form-group">
          <label for="goal">Health/Weight Goal </label>
          <select id="goal" name="goal">
            <option value="LoseWeight">Lose Weight</option>
            <option value="MaintainWeight">Maintain Weight</option>
            <option value="GainWeight">Gain Weight</option>
          </select>
          <em>| &#8827;&#8827; Current Value: <?php echo $row_user_data['goal']; ?></em>
        </div>
        <div class="form-group">
          <label for="activitylevel">Activity Level </label>
          <select id="activitylevel" name="activitylevel">
            <option value="LowActivity">Low Activity</option>
            <option value="MedLowActivity">Medium Low Activity</option>
            <option value="MediumActivity">Medium Activity</option>
            <option value="MedHighActivity">Medium High Activity</option>
            <option value="HighActivity">High Activity</option>
          </select>
          <em>| &#8827;&#8827; Current Value: <?php echo $row_user_data['activity_level']; ?></em>
        </div>
        <hr>
        <button type="submit" class="btn btn-primary btn-block">Update User Values</button>
        <hr>
      </form>
    </div>
    <hr>
    <!-- Today's food entries -->
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><u><b><center>Your Current Daily Recommended Values</center></b></u></h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><b><u>Calories</b></u></th>
                                    <th><b><u>Proteins</b></u></th>
                                    <th><b><u>Carbohydrates</b></u></th>
                                    <th><b><u>Fats</b></u></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th><?php echo $row_recommended_values['calories']; ?> kilocalories</th>
                                    <th><?php echo $row_recommended_values['proteins']; ?> grams</th>
                                    <th><?php echo $row_recommended_values['carbs']; ?> grams</th>
                                    <th><?php echo $row_recommended_values['fats']; ?> grams</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr><hr>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                        <script language="JavaScript">
                            document.write("<center><u><b>Your Current User Attributes as last updated ");
                            var todaysDate = new Date();
                            var currentDate = todaysDate.getMonth() + "/" + todaysDate.getDay() + "/" + todaysDate.getFullYear();
                            var currentTime = todaysDate.getHours() + ":" + todaysDate.getMinutes() + ":" + todaysDate.getSeconds();
                            document.write(currentDate + " at " + currentTime + ":</center></u></b>");
                        </script>
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><b><u>User Name</b></u></th>
                                    <th><b><u>Password</b></u></th>
                                    <th><b><u>Email Address</b></u></th>
                                    <th><b><u>First Name</b></u></th>
                                    <th><b><u>Last Name</b></u></th>
                                    <th><b><u>Gender</b></u></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th><?php echo $row_user_data['username']; ?></th>
                                    <th><i>Not shown</i></th>
                                    <th><?php echo $row_user_data['email']; ?></th>
                                    <th><?php echo $row_user_data['firstname']; ?></th>
                                    <th><?php echo $row_user_data['lastname']; ?></th>
                                    <th><?php echo $row_user_data['gender']; ?></th>
                                </tr>
                            </tbody>
                        </table>
                        
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><b><u>Weight</b></u></th>
                                    <th><b><u>Height</b></u></th>
                                    <th><b><u>Age</b></u></th>
                                    <th><b><u>Goal</b></u></th>
                                    <th><b><u>Activity Level</b></u></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th><?php echo round($row_user_data['weight']*2.20462); ?> lbs</th> <!-- For display in US, convert KGs to LBS -->
                                    <th><?php echo round($row_user_data['height']*39.3701); ?> inches</th> <!-- For display in US, convert Meters to Inches -->
                                    <th><?php echo round($row_user_data['age']); ?> years</th>
                                    <th><?php echo $row_user_data['goal']; ?></th>
                                    <th><?php echo $row_user_data['activity_level']; ?></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
</body>
</html>
