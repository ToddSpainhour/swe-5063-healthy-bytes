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
					<li><a href="days.php">Add Entry</a></li>
					<li class="active"><a href="show_entries.php">Show Entries</a></li>
				</ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>

	<div class="container">
		<h1>My Food Journal</h1>
		<form action="show_entries.php" method="post">
			<div class="form-group">
				<label for="date">Enter the date:</label>
				<input type="date" class="form-control" id="date" name="date" required>
			</div>
			<button type="submit" class="btn btn-default">Submit</button>
		</form>
		
		<?php
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$date = $_POST["date"];
				
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
				
				// Select data from database
				$sql = "SELECT * FROM food_entries WHERE date='$date'";
				$result = $conn->query($sql);
				
				$entries = array();
				
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$entry = array(
							"id" => $row["id"],
							"date" => $row["date"],
							"food" => $row["food"],
							"calories" => $row["calories"],
							"protein" => $row["protein"],
							"carbs" => $row["carbs"],
							"fats" => $row["fats"]
						);
						
						array_push($entries, $entry);
					}
				} else {
					echo "No entries found for the selected date.";
				}
				
				$conn->close();
				
				// Display data in table
				if (count($entries) > 0) {
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
						echo "</tr>";
					}
					
					echo "</tbody>";
					echo "</table>";
				}
			}
		?>
	</div>
	
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>

