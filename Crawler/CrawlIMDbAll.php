<?php 
ini_set('max_execution_time', 300);

set_time_limit(1000000);

require("ConnectDB.php");
require("../libraly/PHPCrawler/PHPCrawl_083/PHPCrawl_083/libs/PHPCrawler.class.php");
require("MyCrawler.php");

$sql = "SELECT  * FROM moviename";
$result = $conn->query($sql);

// startGetComment MovieAll DB
$i = 0;
while($row = $result->fetch_assoc()) {
	if($i==10){break;}


	$ID_Movie = $row['ID_Movie'];
	$IDMovieIMDb = $row['ID_Movie_IMDB'];
	$NameMovieIMDB = $row['Name_Movie_IMDB'];
	$reviewcount_IMDb = $row['reviewcount_IMDb'];
	echo "####### $NameMovieIMDB #######<br>";



	$contentreviewCount = file_get_contents("http://www.imdb.com/title/"."$IDMovieIMDb"."/");
	$checkUserRiview = substr_count($contentreviewCount, '<span itemprop="reviewCount">');

	if($checkUserRiview==2){

		$contentreviewCount = file_get_contents("http://www.imdb.com/title/"."$IDMovieIMDb"."/");
		$first_step = explode( '<span itemprop="reviewCount">' , $contentreviewCount );
		$second_step = explode(" user" , $first_step[1] );
		$reviewCountcheck = $second_step[0];
		$count = $reviewCountcheck;

		require("ConnectDB.php");
		$conn -> set_charset("utf8");
		$sql = "UPDATE  moviename SET reviewcount_IMDb = '$count'
		WHERE ID_Movie= '$ID_Movie'";
		mysqli_query($conn,$sql);
		if (mysqli_query($conn, $sql)) {echo "บันทึกจำนวน : $count <br>";} 
		else {echo "Error: ".$sql."<br><br>".mysqli_error($conn);}
		mysqli_close($conn);

		if($reviewCountcheck >= $reviewcount_IMDb ){

			if($count<=10){
				$content = file_get_contents("http://www.imdb.com/title/"."$IDMovieIMDb"."/reviews?ref_=0");
				$content = strip_tags($content,"<p>,<h2>,<b>");

				$i = 1;
				$N = substr_count($content, "<h2>");
				$content = strchr ( $content, "<h2>" );
				$content = str_replace("<p><b>*** This review may contain spoilers ***</b></p>","",$content);
				$start = strpos($content, "<h2>");
				$stop = strpos($content, "</p>")+4;

				while ($i <= $N) {

					//echo "$i ";

					$Titlestart = strpos($content, "<h2>");
					$Titlestop = strpos($content, "</h2>")+4;
					$Titletext = substr($content, $Titlestart, $Titlestop-$Titlestart);
					$Titletext = strip_tags($Titletext);

					//echo "TitleComment : $Titletext <br>";

					$Commentstart = strpos($content, "<p>");
					$Commentstop = strpos($content, "</p>")+3;
					$Commenttext = substr($content, $Commentstart,$Commentstop-$Commentstart);
					$Commenttext = strip_tags($Commenttext);

					//echo "Comment : $Commenttext <br>";

					$start = strpos($content, "<h2>");
					$stop = strpos($content, "</p>")+4;
					$text = substr($content, $start, $stop-$start);
					$content = str_replace($text,"",$content);

					$search = array("'",'"');
					$Titletext = str_replace($search,"''", $Titletext);
					$Commenttext  = str_replace($search,"''", $Commenttext);

					require("ConnectDB.php");
					$conn -> set_charset("utf8");
				     //Check MEMBERNo for dupplicate 		
					$check = "SELECT * FROM commentimdb  WHERE  CommentIMDb = '".$Commenttext."'";
				     $result1 = mysqli_query($conn,$check) ;//or die(mysqli_error());
				     $num=mysqli_num_rows($result1); 
				     if($num > 0){echo "comment repeat<br>";}
				     else{	
				     	$sql = "INSERT INTO commentimdb (Title_CommentIMDb, CommentIMDb,ID_Movie)
				     	VALUES ('".$Titletext."', '".$Commenttext."', '".$ID_Movie."')";
				     	if (mysqli_query($conn, $sql)) {
				     		echo "บันทึกข้อมูลลงดาต้าเบสเรียบร้อย <br><br>";
				     	} 
				     	else {
				     		//echo "Error: ".$sql."<br><br>".mysqli_error($conn);
				     	}
				     }
				     mysqli_close($conn);

				     $i++;
				 }
				}
				elseif($count>10){
					require("CrawlIMDb.php");

					$content = file_get_contents( "CommentIMDb.txt" );
	                //echo "$content<br>";

					$countStartTitletext = substr_count($content, "<StartTitletext>");
					//echo "$countStartTitletext ";

					$C = substr_count($content, "<StartCommenttext>");
					//echo "$C<br>";

					$j=1;


					while ($j <= $count) {

						//echo "$ ";


						$Titlestart = strpos($content, "<StartTitletext>");
						$Titlestop = strpos($content, "<EndTitletext>")+14;
						$Titletext = substr($content, $Titlestart, $Titlestop-$Titlestart);
						$Titletext = strip_tags($Titletext);
						//$Titletextforpen = "<StartTitletext>$Titletext<EndTitletext>";
						//echo "TitleComment : $Titletext <br>";

						$Commentstart = strpos($content, "<StartCommenttext>");
						$Commentstop = strpos($content, "<EndCommenttext>")+16;
						$Commenttext = substr($content, $Commentstart,$Commentstop-$Commentstart);
						$Commenttext = strip_tags($Commenttext);
						//$Commenttextforpen = "<StartCommenttext>$Commenttext<EndCommenttext>";
						//echo "Comment : $Commenttext <br><br>";

						$search = array("'",'"');
						$Titletext = str_replace($search,"''", $Titletext);
						$Commenttext  = str_replace($search,"''", $Commenttext);

						require("ConnectDB.php");
						$conn -> set_charset("utf8");
				        //Check MEMBERNo for dupplicate 		
						$check = "SELECT * FROM commentimdb  WHERE  CommentIMDb = '".$Commenttext."'";
				        $result1 = mysqli_query($conn,$check) ;//or die(mysqli_error());
				        $num=mysqli_num_rows($result1); 
				        if($num > 0){
				        //echo "มีความคิดเห็นนี้เเล้ว<br>";
				        }
				        else{	
				        	$sql = "INSERT INTO commentimdb (Title_CommentIMDb, CommentIMDb,ID_Movie)
				        	VALUES ('".$Titletext."', '".$Commenttext."', '".$ID_Movie."')";
				        	if (mysqli_query($conn, $sql)) {
				        		//echo "บันทึกข้อมูลลงดาต้าเบสเรียบร้อย <br><br>";
				        	} 
				        	else {
				        		//echo "Error: ".$sql."<br><br>".mysqli_error($conn);
				        	}
				        }
				        mysqli_close($conn);

				        $start = strpos($content, "<StartTitletext>");
				        $stop = strpos($content, "<EndCommenttext>")+16;
				        $text = substr($content, $start, $stop-$start);
				        $content = str_replace($text,"",$content);

				        $j++;
				    }


				    $objFopen = fopen("CommentIMDb.txt",'w+');
				    $strText = "";
				    fwrite($objFopen, $strText);


				}
			}
		}

		else{
			require("ConnectDB.php");
			$conn -> set_charset("utf8");
			$sql = "UPDATE  moviename SET reviewcount_IMDb = '-1'
			WHERE ID_Movie= '$ID_Movie'";
			mysqli_query($conn,$sql);
			if (mysqli_query($conn, $sql)) {
				//echo "บันทึกจำนวน : No <br>";
			} else {
				//echo "Error: ".$sql."<br><br>".mysqli_error($conn);
			}
			mysqli_close($conn);
		}

		$i++;
		//echo "<br>";

	}

	?>