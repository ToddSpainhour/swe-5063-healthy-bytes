<!DOCTYPE html>
<html>
<head>
	<title>Healthy Bytes</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
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

<?php
		session_start();
		$userID = $_SESSION["userID"];
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
				$sql = "INSERT INTO food_entries (date, food, calories, protein, carbs, fats, userID) VALUES ('$date', '$food', '$calories', '$protein', '$carbs', '$fats', '$userID')";
				
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


	<div class="container">
		<h1>Food Entries</h1>
		<form action="days.php" method="post">
			<div class="form-group">
				<label for="date">Enter the date:</label>
				<input type="date" class="form-control" id="date" name="date" required>
			</div>

			<div class="form-group">
				<label for="food">Food</label>
				<!-- <input type="text" class="form-control" id="food" name="food" datalist="foodAutoCompleteInput" onkeyup="myAutoCompleteFunction(this.value)" required> -->
				<input type="text" class="form-control" id="food" name="food"  list="dataListOfFoodItems" onkeyup="getFoodAutoSuggestions(this.value)" required>
				<datalist id="dataListOfFoodItems"> </datalist>
			</div>

			<!-- food autosuggestion code below -->
			<script>
				const getFoodAutoSuggestions = (userEnteredValue) => {
					if(userEnteredValue.length == 0){
						return;
					} else {
						var xmlhttp = new XMLHttpRequest();
						xmlhttp.onreadystatechange = function() {
							if (this.readyState == 4 && this.status == 200) {
								let queryResultsReturnedFromPHP = xmlhttp.response;
								let queryResultsAsJavaScriptObject = JSON.parse(queryResultsReturnedFromPHP);

								dataListOfFoodItems = document.getElementById("dataListOfFoodItems");
								let domString = "";

								for(i = 0; i < queryResultsAsJavaScriptObject.length; i++){
									domString += `<option value="${queryResultsAsJavaScriptObject[i].food}" />`;
								}

								dataListOfFoodItems.innerHTML = domString;
							};
						}
						xmlhttp.open("GET", "getfoodsuggestion.php?query=" + userEnteredValue, true);
						xmlhttp.send();
					}
				}
			</script>
			<!-- end of food autosuggestion code -->

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
		

	</div>
	
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
