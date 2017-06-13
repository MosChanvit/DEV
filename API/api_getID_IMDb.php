<?php
error_reporting(0);

include 'ConnectDB.php';

$response = array();
// echo "test";
if(isset($_POST['nameMovie'])) {
$movieName = $_POST["nameMovie"];

$sql = "SELECT AllComment_pos FROM moviename WHERE Name_Movie_IMDB LIKE '%$movieName%'";
$result = mysqli_query($conn, $sql);
 
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $response = array("status" => "TRUE", "ID_IMDb" => $row["AllComment_pos"]);
    }
} else {
    $response["status"] = "none";
}

} else {

	$response["status"] = FALSE;
	$response["errorMsg"] = "Required parameters nameMovie";
}

	echo json_encode(array("AllComment_pos"=>$response));

mysqli_close ($conn);

?>