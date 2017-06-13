<?php
ini_set("max_execution_time", 0);
//set_time_limit(10000);

if (PHP_SAPI != 'cli') {
	echo "<pre>";
}

require("ConnectDB.php");

require("../libraly/Lib_Sentiment/phpInsight-master/autoload.php");
$sentiment = new \PHPInsight\Sentiment();

////////////////////////////////////////////////////////////////////////////////
$sql_CommentTweet = "SELECT  * FROM commenttwitter where Sentiment =''";
$DBCommentTweets = $conn->query($sql_CommentTweet);

//loop CommentTweets
while($CommentTweet= $DBCommentTweets->fetch_assoc()) {

	$ID_CommentTwitter = $CommentTweet['ID_CommentTwitter'];
	$CommentTwitter = $CommentTweet['CommentTwitter'];
	$ID_Movie = $CommentTweet['ID_Movie'];
	$Sentiment = $CommentTweet['Sentiment'];

	//if($ID_CommentTwitter>10){break;}


	$class = $sentiment->categorise($CommentTwitter);
	//echo "$ID_CommentTwitter\n";
	//echo "String: $CommentTwitter\n";
	//echo "Dominant: $class\n";

	
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

	$sqlstampsentiment = "UPDATE  commenttwitter SET  Sentiment = 'YES'
	WHERE ID_CommentTwitter = '$ID_CommentTwitter'";
	mysqli_query($conn,$sqlstampsentiment);
	
}
////////////////////////////////////////////////////////////////////////////////
// $sql_commentimdb = "SELECT  * FROM commentimdb where Sentiment =''";
// $DBcommentimdbs = $conn->query($sql_commentimdb);

// //loop CommentTweets
// while($DBcommentimdb= $DBcommentimdbs->fetch_assoc()) {

// 	$ID_CommentIMDb = $DBcommentimdb['ID_CommentIMDb'];
// 	$CommentIMDb = $DBcommentimdb['CommentIMDb'];
// 	$Sentiment = $DBcommentimdb['Sentiment'];
// 	$ID_Movie = $DBcommentimdb['ID_Movie'];


// 	$class = $sentiment->categorise($CommentIMDb);

// 	echo "$ID_CommentIMDb\n";
// 	echo "String: $CommentIMDb\n";
// 	echo "Dominant: $class\n\n";


// 	$sqlsentiment = "UPDATE  commentimdb SET  Sentiment = '$class'
// 	WHERE ID_CommentIMDb = '$ID_CommentIMDb'";
// 	mysqli_query($conn,$sqlsentiment);	
// }

/////////////////////////////////////////////////////////////////////////////






mysqli_close($conn);
?>