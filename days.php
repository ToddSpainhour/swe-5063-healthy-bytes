<!DOCTYPE html>
<html>
<head>
	<title>Healthy Bytes</title>
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

<nav class="navbar navbar-default">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">Healthy Bytes</a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li class="active"><a href="days.php">Add Entry</a></li>
					<li><a href="show_entries.php">Show Entries</a></li>
				</ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>

	<div class="container">
		<h1>Food Entries</h1>
		<form action="days.php" method="post">
			<div class="form-group">
				<label for="date">Enter the date:</label>
				<input type="date" class="form-control" id="date" name="date" required>
			</div>
			<div class="form-group">
				<label for="food">Food</label>
				<input type="text" class="form-control" id="food" name="food" required>
			</div>
			<div class="form-group">
				<label for="calories">Calories (g)</label>
				<input type="number" class="form-control" id="calories" name="calories" required>
			</div>
			<div class="form-group">
				<label for="protein">Protein (g)</label>
				<input type="number" class="form-control" id="protein" name="protein" required>
			</div>
			<div class="form-group">
				<label for="carbs">Carbohydrates (g)</label>
				<input type="number" class="form-control" id="carbs" name="carbs" required>
			</div>
			<div class="form-group">
				<label for="fats">Fats (g)</label>
				<input type="number" class="form-control" id="fats" name="fats" required>
			</div>
			<button type="submit" class="btn btn-default">Submit</button>
		</form>
		
		<?php
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$date = $_POST["date"];
				$food = $_POST["food"];
				$calories = $_POST["calories"];
				$protein = $_POST["protein"];
				$carbs = $_POST["carbs"];
				$fats = $_POST["fats"];
				
        // Connect to database
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
				

				// Insert data into database
				//$integer_string = strval($_SESSION['userID']);
				$sql = "INSERT INTO food_entries (date, food, calories, protein, carbs, fats) VALUES ('$date', '$food', '$calories', '$protein', '$carbs', '$fats')";
				
				if ($conn->query($sql) === TRUE) {
					echo "New record created successfully";
				} else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
				// Save the last insert ID
				$last_id = $conn->insert_id;
        $conn->close();	      

        //Display data in table
				$entries = array();
				
				$entry = array(
					"id" => $last_id,
					"date" => $date,
					"food" => $food,
					"calories" => $calories,
					"protein" => $protein,
					"carbs" => $carbs,
					"fats" => $fats
				);
				
				array_push($entries, $entry);
				
				echo "<table class='table table-striped'>";
				echo "<thead>";
				echo "<tr>";
				echo "<th>Date</th>";
				echo "<th>Food</th>";
				echo "<th>Calories</th>";
				echo "<th>Protein</th>";
				echo "<th>Carbs</th>";
				echo "<th>Fats</th>";
				echo "</tr>";
				echo "</thead>";
				echo "<tbody>";
				
				foreach ($entries as $entry) {
					echo "<tr>";
					echo "<td>" . $entry["date"] . "</td>";
					echo "<td>" . $entry["food"] . "</td>";
					echo "<td>" . $entry["calories"] . "</td>";
					echo "<td>" . $entry["protein"] . "</td>";
					echo "<td>" . $entry["carbs"] . "</td>";
					echo "<td>" . $entry["fats"] . "</td>";
          echo "<td><a href='days.php?id=" . $entry["id"] . "'>Undo</a></td>";
          echo "</tr>";
				}
				
				echo "</tbody>";
				echo "</table>";
			}
			if(isset($_GET["id"])) {
				// Connect to database
				$servername = "localhost";
				$user = "root";
				$pass= "";
				$db = "FoodEntryDB";
				$conn = mysqli_connect($servername, $user, $pass, $db);
			
				// Check connection
				if ($conn->connect_error) {
					die("Connection failed: " . $conn->connect_error);
				}
				
				$idToDelete = $_GET["id"];
				$sql = "DELETE FROM food_entries WHERE id='$idToDelete'";
				if ($conn->query($sql) === TRUE) {
					echo "Last insert was undone";
				} else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
				$conn->close();
			}
		?>
	</div>
	
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
