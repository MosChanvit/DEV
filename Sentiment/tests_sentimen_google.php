<?php
ini_set("max_execution_time", 0);
include "ConnectDB.php";

function API_Natural_Language_Sentiments($content){

	$content= addslashes($content);

	$Natural_Language_API_key="AIzaSyD4XJzJH_QQmqN46c9IWrAXUKCv6ENAuns";

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => 
		"https://language.googleapis.com/v1/documents:analyzeSentiment?key=".$Natural_Language_API_key."",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 120,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST=> false,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => "{\"encodingType\":\"UTF8\",
		\"document\": {
			\"type\": \"PLAIN_TEXT\",
			\"content\": \"$content\"}}",
			CURLOPT_HTTPHEADER => array('Content-Type: text/plain')
			));

	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);

	if ($err) {
		echo "cURL Error #:" . $err;
	} else {

	  //return $response;

		$json_array = json_decode($response);
		foreach ($json_array as $key => $value) {
			if($key=="documentSentiment"){
				//echo $key.":"."<br>";
				foreach ($value as $key2 => $value2) {
					//echo $key2.":  ".$value2."<br>";
				}
			}else if($key=="language"){

				//echo $key.":"."<br>";
				//echo $value."<br>";

			}else if($key=="sentences"){

				//echo $key.":"."<br>";

				$sentement=array();
				$sentement[0] = $value[0]->text->content;
				$sentement[1] = $value[0]->text->beginOffset;
				$sentement[2] =	$value[0]->sentiment->magnitude;
				$sentement[3] = $value[0]->sentiment->score;
				//echo $content[0];
				//print_r($sentement);
				
			}
		}
		return  $sentement[3];
 	
	}

}
//echo API_Natural_Language_Sentiments("I hate you.");

$sentiment = "";

$sql = "SELECT * FROM commenttwitter";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

	while($row = $result->fetch_assoc()) {
		echo floatval(API_Natural_Language_Sentiments($row["CommentTwitter"]))."<br>";

		if(floatval(API_Natural_Language_Sentiments($row["CommentTwitter"])) > 0){

			$sentiment= "Pos";
			$sql_update = "UPDATE commenttwitter SET Sentiment = '".$sentiment."'
			WHERE ID_CommentTwitter = '".$row["ID_CommentTwitter"]."' ";


		}else if(floatval(API_Natural_Language_Sentiments($row["CommentTwitter"])) < 0){

			$sentiment= "Neg";
			$sql_update = "UPDATE commenttwitter SET Sentiment = '".$sentiment."'
			WHERE ID_CommentTwitter = '".$row["ID_CommentTwitter"]."' ";


		}elseif (floatval(API_Natural_Language_Sentiments($row["CommentTwitter"])) == 0) {

			$sentiment= "Neu";
			$sql_update = "UPDATE commenttwitter SET Sentiment = '".$sentiment."'
			WHERE ID_CommentTwitter = '".$row["ID_CommentTwitter"]."' ";

		}

		if ($conn->query($sql_update) === TRUE) {
			echo "Record updated successfully ";
		} else {
			echo "Error updating record: " . $conn->error;
		}

	}
} else {
	echo "0 results";
}

$conn->close();

?>