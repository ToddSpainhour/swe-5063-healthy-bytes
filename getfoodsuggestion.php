<?php
    session_start();
    include 'db_connect.php';

    $query = $_REQUEST["query"];
    $userID = $_SESSION["userID"];
    $sql = "SELECT food FROM food_entries WHERE userID = $userID AND food LIKE '$query%'";
    $queryResult = $conn->query($sql);
    $arrayOfQueryResults = array();

    while($row = mysqli_fetch_array($queryResult)) {
        $arrayOfQueryResults[] = $row;
  }

    $queryResultsInJsonFormat = json_encode($arrayOfQueryResults);
    echo $queryResultsInJsonFormat;
?>