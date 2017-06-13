<?php
ini_set("max_execution_time", 0);
//set_time_limit(10000);

require("ConnectDB.php");

require("../libraly/Lib_Sentiment/phpInsight-master/autoload.php");
$sentiment = new \PHPInsight\Sentiment();


include '../libraly/PHP_Classifier/PHP-Classifier-master/autoload.php';
$tokenizer = new HybridLogic\Classifier\Basic;
$classifier = new HybridLogic\Classifier($tokenizer);
require("../libraly/PHP_Classifier/PHP-Classifier-master/train_character.php");
require("../libraly/PHP_Classifier/PHP-Classifier-master/train_soundtrack.php");
require("../libraly/PHP_Classifier/PHP-Classifier-master/train_story.php");

///////////////////////////////////////////////////////////////////////////////////////////////
$sql_commentimdb = "SELECT  * FROM commentimdb where Sentiment =''";
$DBcommentimdbs = $conn->query($sql_commentimdb);

//loop CommentTweets
while($DBcommentimdb= $DBcommentimdbs->fetch_assoc()) {

	$ID_CommentIMDb = $DBcommentimdb['ID_CommentIMDb'];
	$CommentIMDb = $DBcommentimdb['CommentIMDb'];
	$Sentiment = $DBcommentimdb['Sentiment'];
	$ID_Movie = $DBcommentimdb['ID_Movie'];

	//if($ID_CommentIMDb>10){break;}

	$class = $sentiment->categorise($CommentIMDb);

	echo "$ID_CommentIMDb\n";
	echo "String: $CommentIMDb\n";
	echo "Dominant: $class<br>";
	if($class == "pos"){
		$sql_AllComment = "UPDATE moviename SET AllComment_pos = AllComment_pos + 1 where ID_Movie ='".$ID_Movie."'";
	}
	else if($class == "neg"){
		$sql_AllComment = "UPDATE moviename SET AllComment_neg = AllComment_neg + 1 where ID_Movie ='".$ID_Movie."'";
	}
	else if($class == "neu"){
		$sql_AllComment = "UPDATE moviename SET AllComment_neu = AllComment_neu + 1 where ID_Movie ='".$ID_Movie."'";
	}
	mysqli_query($conn,$sql_AllComment);

	$sentences = explode(".", $CommentIMDb);
	foreach($sentences as $sentence)
	{


		$sentencelen = strlen ( $sentence );
		if($sentencelen > 10){

			$class = $sentiment->categorise($sentence);
			echo "$sentence !!!!!!!!!! $class <br>";

			$groups = $classifier->classify($sentence);
			foreach($groups as $group => $groups_value) {
				$groupsentiment = $group;
				break;
			}

			if ($groupsentiment=="story") {
				if ($class == "pos") {
					$sql_Story = "UPDATE moviename SET Story_pos = Story_pos + 1 where ID_Movie ='".$ID_Movie."'";
				}
				else if ($class == "neg") {
					$sql_Story = "UPDATE moviename SET Story_neg = Story_neg + 1 where ID_Movie ='".$ID_Movie."'";
				}
				else if ($class == "neu") {
					$sql_Story = "UPDATE moviename SET Story_neu = Story_neu + 1 where ID_Movie ='".$ID_Movie."'";
				}
				mysqli_query($conn,$sql_Story);

			}
			else if ($groupsentiment=="character") {
				if ($class == "pos") {
					$sql_character = "UPDATE moviename SET Character_pos = Character_pos + 1 where ID_Movie ='".$ID_Movie."'";
				}
				else if ($class == "neg") {
					$sql_character = "UPDATE moviename SET Character_neg = Character_neg + 1 where ID_Movie ='".$ID_Movie."'";
				}
				else if ($class == "neu") {
					$sql_character = "UPDATE moviename SET Character_neu = Character_neu + 1 where ID_Movie ='".$ID_Movie."'";
				}
				mysqli_query($conn,$sql_character);
			}
			elseif ($groupsentiment=="soundtrack") {
				if ($class == "pos") {
					$sql_soundtrack = "UPDATE moviename SET Soundtrack_pos = Soundtrack_pos + 1 where ID_Movie ='".$ID_Movie."'";
				}
				else if ($class == "neg") {
					$sql_soundtrack = "UPDATE moviename SET Soundtrack_neg = Soundtrack_neg + 1 where ID_Movie ='".$ID_Movie."'";
				}
				else if ($class == "neu") {
					$sql_soundtrack = "UPDATE moviename SET Soundtrack_neu = Soundtrack_neu + 1 where ID_Movie ='".$ID_Movie."'";
				}
				mysqli_query($conn,$sql_soundtrack);
			}

		} 
	}
	echo "#############################<br>";

	$sqlsentiment = "UPDATE  commentimdb SET  Sentiment = 'Yes'
	WHERE ID_CommentIMDb = '$ID_CommentIMDb'";
	mysqli_query($conn,$sqlsentiment);	
}

/////////////////////////////////////////////////////////////////////////////

mysqli_close($conn);
?>