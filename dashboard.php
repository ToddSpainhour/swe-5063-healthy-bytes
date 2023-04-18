<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

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

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
} 
$userID = $_SESSION['userID'];

// Fetch user's recommended values
$query_recommended_values = "SELECT * FROM recommended_values WHERE userID = $userID";
$result_recommended_values = mysqli_query($conn, $query_recommended_values);
$row_recommended_values = mysqli_fetch_assoc($result_recommended_values);

// Fetch today's food entries
date_default_timezone_set('America/New_York');
$date_today = date("Y-m-d");
$query_food_entries = "SELECT * FROM food_entries WHERE userID = $userID AND date = '$date_today'";
$result_food_entries = mysqli_query($conn, $query_food_entries);
$food_entries = mysqli_fetch_all($result_food_entries, MYSQLI_ASSOC);

$total_calories = 0;
$total_protein = 0;
$total_carbs = 0;
$total_fats = 0;

foreach ($food_entries as $entry) {
    $total_calories += $entry['calories'];
    $total_protein += $entry['protein'];
    $total_carbs += $entry['carbs'];
    $total_fats += $entry['fats'];
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style type="text/css">
        .container {
            margin-top: 50px;
        }
        table {
            margin-top: 50px;
            width: 100%;
        }
        #chart {
            width: 100%;
            height: 400px;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<?php include 'navbar.php'; ?>


 <div class="container">
    <div class="row">
        <!-- Recent Entries -->
        <div class="col-lg-8 col-md-7 col-sm-12">
            <h2>Recent Entries</h2>
      <table class="table table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Food</th>
                <th>Calories</th>
                <th>Protein</th>
                <th>Carbs</th>
                <th>Fats</th>
            </tr>
        </thead>
        <tbody>
            
            <?php
            $result = mysqli_query($conn, "SELECT * FROM food_entries WHERE userID = $userID ORDER BY date DESC");
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<td>' . $row['date'] . '</td>';
                    echo '<td>' . $row['food'] . '</td>';
                    echo '<td>' . $row['calories'] . '</td>';
                    echo '<td>' . $row['protein'] . '</td>';
                    echo '<td>' . $row['carbs'] . '</td>';
                    echo '<td>' . $row['fats'] . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="6">No recent entries found</td></tr>';
            }
            ?>
        </tbody>
      </table>
 </div>
   
   
  <!-- Nutrition summary -->
  <div class="col-lg-4 col-md-5 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Nutrition Summary</h3>
                </div>
                
                <div class="card-body">
                    <ul class="list-group list-group-flush"> 
                        <li class="list-group-item">Calories: <?php echo $total_calories; ?> / <?php echo $row_recommended_values['calories']; ?></li>
                        <li class="list-group-item">Protein: <?php echo $total_protein; ?> / <?php echo $row_recommended_values['proteins']; ?>g</li>
                        <li class="list-group-item">Carbs: <?php echo $total_carbs; ?> / <?php echo $row_recommended_values['carbs']; ?>g</li>
                        <li class="list-group-item">Fats: <?php echo $total_fats; ?> / <?php echo $row_recommended_values['fats']; ?>g</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
        
        
        
<!-- Today's food entries -->
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Today's Food Entries</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Food Name</th>
                                <th>Calories</th>
                                <th>Protein</th>
                                <th>Carbs</th>
                                <th>Fats</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($food_entries as $entry) { ?>
                                <tr>
                                    <td><?php echo $entry['food']; ?></td>
                                    <td><?php echo $entry['calories']; ?> kcal</td>
                                    <td><?php echo $entry['protein']; ?> g</td>
                                    <td><?php echo $entry['carbs']; ?> g</td>
                                    <td><?php echo $entry['fats']; ?> g</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <a href="days.php" class="btn btn-primary">Add Food Entry</a>
                </div>
            </div>
        </div>
    </div>
</div>
        
        <!-- Plotly.js -->
        <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
        
        
        
<!-- Progress Chart -->
<div class="container">
    <div class="row">
        <!-- Progress chart -->
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Progress Chart</h3>
                </div>
                <div class="panel-body">
                    <div id="chart"></div>
                </div>
            </div>
        </div>
    </div>
</div>
 
 
 <script>
                // Get the data for the chart
                var calories_goal = <?php echo $row_recommended_values['calories']; ?>;
                var calories_today = <?php echo $total_calories; ?>;
                var protein_goal = <?php echo $row_recommended_values['proteins']; ?>;
                var protein_today = <?php echo $total_protein; ?>;
                var carbs_goal = <?php echo $row_recommended_values['carbs']; ?>;
                var carbs_today = <?php echo $total_carbs; ?>;
                var fats_goal = <?php echo $row_recommended_values['fats']; ?>;
                var fats_today = <?php echo $total_fats; ?>;

                // Define the data for the chart
                var data = [{
                        x: ['Calories', 'Protein', 'Carbs', 'Fats'],
                        y: [calories_today, protein_today, carbs_today, fats_today],
                        type: 'bar',
                        name: 'Today',
                        marker: {
                            color: 'rgba(255, 153, 0, 0.7)',
                            line: {
                                color: 'rgba(255, 153, 0, 1.0)',
                                width: 1
                            }
                        }
                    },
                    {
                        x: ['Calories', 'Protein', 'Carbs', 'Fats'],
                        y: [calories_goal, protein_goal, carbs_goal, fats_goal],
                        type: 'bar',
                        name: 'Goal',
                        marker: {
                            color: 'rgba(102, 178, 255, 0.7)',
                            line: {
                                color: 'rgba(102, 178, 255, 1.0)',
                                width: 1
                            }
                        }
                    }
                ];

                // Define the layout for the chart
                var layout = {
                    barmode: 'group',
                    title: 'Nutrition Progress',
                    xaxis: {
                        tickfont: {
                            size: 14,
                            color: 'rgb(107, 107, 107)'
                        }
                    },
                    yaxis: {
                        title: 'Amount',
                        titlefont: {
                            size: 16,
                            color: 'rgb(107, 107, 107)'
                        },
                        tickfont: {
                            size: 14,
                            color: 'rgb(107, 107, 107)'
                        }
                    },
                    legend: {
                        x: 0,
                        y: 1.0,
                        bgcolor: 'rgba(255, 255, 255, 0)',
                        bordercolor: 'rgba(255, 255, 255, 0)'
                    },
                    barmode: 'group',
                    bargap: 0.15,
                    bargroupgap: 0.1
                };

                // Plot the chart
                Plotly.newPlot('chart', data, layout);
            </script>

  
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
</body>
</html>
                       
