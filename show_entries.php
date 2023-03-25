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
		#myChart {
			top:0;
			bottom: 0;
			left: 0;
			right: 0;
			margin:auto;
		}
		.progress {
			width: 50%;
			top:0;
			bottom: 0;
			left: 0;
			right: 0;
			margin:auto;
		}
		.centeredLabel {
			text-align: center;
			top:0;
			bottom: 0;
			left: 0;
			right: 0;
			margin:auto;
		}
	</style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
</head>
<body>
<?php 
	session_start();
?>

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
			<button type="submit" value="date" name="submit" class="btn btn-default">Submit</button>
				<p class="centeredLabel" style="display: none"><b>Current Calorie Intake</b></p>
				<div class="progress" style="display: none">
					<div id="calorieProgressBar" class="progress-bar progress-bar-info" role="progressbar">
					</div>
				</div>
			<br />
			<canvas id="myChart" style="width:100%;max-width:600px; display: none"></canvas>
		</form>
		
		<?php
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$date = $_POST["date"];

				
			     // Connect to database
                $servername = "localhost";
                $user = "root";
                $pass= "";
                $db = "FoodEntryDB";
				
				if($_POST["submit"] == "edit") {
					$conn = mysqli_connect($servername, $user, $pass, $db);
				
					// Check connection
					if ($conn->connect_error) {
						die("Connection failed: " . $conn->connect_error);
					}
					// Update row in database
					$food = $_POST['food'];
					$calories = $_POST['calories'];
					$protein = $_POST['protein'];
					$carbs = $_POST['carbs'];
					$fats = $_POST['fats'];
					$foodEntryId = $_POST['id'];
					$sql = "UPDATE food_entries SET food='$food', calories='$calories', protein='$protein', carbs='$carbs', fats='$fats' WHERE id='$foodEntryId'";
					$result = $conn->query($sql);

					$conn->close();
				}

				else if($_POST["submit"] == 'delete') {
					$conn = mysqli_connect($servername, $user, $pass, $db);
				
					// Check connection
					if ($conn->connect_error) {
						die("Connection failed: " . $conn->connect_error);
					}
					
					// Delete row in database
					$foodEntryId = $_POST['id'];
					$sql = "DELETE FROM food_entries WHERE id='$foodEntryId'";
					$result = $conn->query($sql);

					$conn->close();
				}
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
				
				if (count($entries) > 0) {
					// display pie chart and progress bar
					?><script>
						$('#myChart').css('display', 'block');
						$('.centeredLabel').css('display', 'block');
						$('.progress').css('display', 'block');
					</script><?php
					
					// Display data in table
					echo "<table class='table table-striped'>";
					echo "<thead>";
					echo "<tr>";
					echo "<th>Date</th>";
					echo "<th>Food</th>";
					echo "<th>Calories</th>";
					echo "<th>Protein</th>";
					echo "<th>Carbs</th>";
					echo "<th>Fats</th>";
					echo "<th>Edit</th>";
					echo "<th>Delete</th>";
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
						?>
						<td>
							<div>
								<button data-toggle="modal" type='button' data-target="#myModal-<?php echo $entry["id"];?>" class='btn btn-warning btn-sm'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span></button>
							</div>
							
						</td>
						<div id="myModal-<?php echo $entry["id"];?>" class="modal fade" tabindex="-1" role="dialog">
							<div class="modal-dialog">
							<!-- Modal content -->
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title">Edit</h4>
								</div>
								<div class="modal-body">
									<form action="" method="post">
										<input type="hidden" class="form-control" value="<?php echo $entry["id"]?>" name="id" required>
										<div class="form-group">
											<label for="date">Enter the date:</label>
											<input type="date" class="form-control" value="<?php echo $entry["date"]?>" name="date" readonly required>
										</div>
										<div class="form-group">
											<label for="food">Food</label>
											<input type="text" class="form-control" value="<?php echo $entry["food"]?>" name="food" placeholder="Food" required>
										</div>
										<div class="form-group">
											<label for="calories">Calories (g)</label>
											<input type="number" class="form-control" value="<?php echo $entry["calories"]?>" placeholder="Calories" name="calories" required>
										</div>
										<div class="form-group">
											<label for="protein">Protein (g)</label>
											<input type="number" class="form-control" value="<?php echo $entry["protein"]?>" placeholder="Protein" name="protein" required>
										</div>
										<div class="form-group">
											<label for="carbs">Carbohydrates (g)</label>
											<input type="number" class="form-control" value="<?php echo $entry["carbs"]?>" placeholder="Carbs" name="carbs" required>
										</div>
										<div class="form-group">
											<label for="fats">Fats (g)</label>
											<input type="number" class="form-control" value="<?php echo $entry["fats"]?>" placeholder="Fats" name="fats" required>
										</div>
										<button type="submit" value="edit" name="submit" class="btn btn-default">Submit</button>
									</form>
								</div>
							</div>
							</div>
						</div>
						<td>
							<div>
								<button data-toggle="modal" type='button' data-target="#deleteModal-<?php echo $entry["id"];?>" class='btn btn-danger btn-sm'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>
							</div>
						</td>
						<div id="deleteModal-<?php echo $entry["id"];?>" class="modal fade" tabindex="-1" role="dialog">
							<div class="modal-dialog">
							<!-- Modal content -->
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title">Delete</h4>
								</div>
								<div class="modal-body">
									<form action="" method="post">
										<input type="hidden" class="form-control" value="<?php echo $entry["id"]?>" name="id" required>
										<input type="hidden" class="form-control" value="<?php echo $entry["date"]?>" name="date" required>
										<p>Are you sure you want to delete this entry?</p>
										<button type="button" data-dismiss="modal" class="btn btn-secondary">No</button>
										<button type="submit" value="delete" name="submit" class="btn btn-danger">Yes</button>
									</form>
								</div>
							</div>
							</div>
						</div>
						<?php
						echo "</tr>";
					
					}
					
					echo "</tbody>";
					echo "</table>";
				}

				// if submit button for date selection was pressed, set session variable for pie chart to use
				if($_POST["submit"] == "date") {
					$_SESSION["date"] = $date;
				}
			}
		?>
	</div>
	<?php
		// Get current calorie intake 
		$conn = mysqli_connect('localhost', 'root', '', 'FoodEntryDB');
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		if(!isset($_SESSION['date'])) {
			// to avoid array key date not found error on first load 
			$_SESSION['date'] = "2023-03-08";
			$selectedDate = $_SESSION['date'];
		}
		else {
			$selectedDate = $_SESSION['date']; // this gets set whenever the user selects a date on the My Food Journal page
		}
		
		$sql = "SELECT SUM(calories) as totalCalories FROM food_entries WHERE date='$selectedDate'"; // add userID later
		$result = $conn->query($sql);
		$caloriesData=$result->fetch_assoc();
		$currentCalories= $caloriesData['totalCalories'];
		$conn->close();
	
		$percentage = (string)(($currentCalories / 2000) * 100);



		 // Pie chart section
		 // Connect to database
		 $servername = "localhost";
		 $user = "root";
		 $pass= "";
		 $db = "FoodEntryDB";

		 // SELECT totalFats
		 $conn = mysqli_connect($servername, $user, $pass, $db);
		 $userID = $_SESSION['userID'];
		 $dateToSelect = $_SESSION['date'];
		 $sql = "SELECT SUM(fats) as totalFats FROM food_entries WHERE userID = '$userID' AND date = '$dateToSelect'";
		 $result = $conn->query($sql);
		 $fatsData=$result->fetch_assoc();
		 $totalFats = $fatsData['totalFats'];
		 $conn->close();
	?>
	<script>
		// Progress bar for calories - change values
		$("#calorieProgressBar").css('width', '<?php echo $percentage?>%');
		$("#calorieProgressBar").html('<?php echo $percentage?>%');

		// Pie chart
		var xValues = ["Fats", "Carbs", "Protein"];
		var yValues = [Number("<?php echo $totalFats; ?>"), 24, 15];
		var barColors = [
			"#b91d47",
			"#00aba9",
			"#e8c3b9",
			];

		new Chart("myChart", {
			type: "pie",
			data: {
				labels: xValues,
				datasets: [{
				backgroundColor: barColors,
				data: yValues
				}]
			},
			options: {
				title: {
				display: true,
				text: "Macronutrient Breakdown"
				}
			}
		});
</script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>	
</body>
</html>

