<?php

set_time_limit(1000000);


require("ConnectDB.php");

$sql_publicationslist = "SELECT  * FROM publicationslist_rottentomatoes";
$publicationslist = $conn->query($sql_publicationslist);

$i = 0;

//loop publication
while($publication = $publicationslist->fetch_assoc()) {
	//if($i==2){break;}

	$Name_Publications = $publication['Name_Publications'];
	$ID_Link = $publication['ID_Link'];
	$TagStart = $publication['TagStart'];
	$TagEnd = $publication['TagEnd'];
                      //echo "$Name_Publications $ID_Link $TagStart $TagEnd <br>";



	//echo "############## $Name_Publications $ID_Link ##############<br>";

	$url_Publications = "https://www.rottentomatoes.com/"."$ID_Link";

	$content = file_get_contents( "$url_Publications" );
	$count = substr_count($content, '<div class="col-sm-1 col-xs-2 table-cell-border clearfix">');
	$j='1';
	$content = strstr($content, '<div class="col-sm-1 col-xs-2 table-cell-border clearfix">'.$j.'</div>');

	//loop count list Riview in publication
	while($j<=$count){

		$content = strstr($content, '<div class="col-sm-1 col-xs-2 table-cell-border clearfix">'.$j.'</div>');

		$startm = strpos($content, '/m/');
		$stopm = strpos($content, '" class="movie-link">');
		$NameMovie = substr($content, $startm, $stopm-$startm);
		$NameMovie = str_replace("/m/","",$NameMovie);
		$NameMovie = str_replace(" ","",$NameMovie);
		$NameMovie = strip_tags($NameMovie);
		echo "NameMovie : $NameMovie <br>";

		$startm = strpos($content, '/critic/');
		$stopm = strpos($content, '" class="package-body-text">');
		$NameCritic = substr($content, $startm, $stopm-$startm);
		$NameCritic = str_replace("/critic/","",$NameCritic);
			//echo "NameCritic : $NameCritic<br>";

		$startm = strpos($content, '<a class="unstyled articleLink" href="');
		$stopm = strpos($content, '"target="');
		$LinkReview = substr($content, $startm, $stopm-$startm);
		$LinkReview = str_replace('<a class="unstyled articleLink" href="',"",$LinkReview);
			//echo "LinkReview : $LinkReview<br><br>";

		$sql_moviename_rottentomatoes = "SELECT  * FROM moviename_rottentomatoes";
		$moviename_rottentomatoes = $conn->query($sql_moviename_rottentomatoes);


        //loop moviename rottentomatoes
		while($moviename_rottentomatoe = $moviename_rottentomatoes->fetch_assoc()) {

			$Name_Movie_rottentomatoes_DB = $moviename_rottentomatoe['Name_Movie_rottentomatoes'];
			//$Name_Movie_rottentomatoes_DB = str_replace(" ","",$Name_Movie_rottentomatoes_DB);
			$ID_Movie_rottentomatoes = $moviename_rottentomatoe['ID_Movie_rottentomatoes'];


			if(strcmp($NameMovie,$Name_Movie_rottentomatoes_DB)==0){
				echo "$ID_Movie_rottentomatoes $Name_Movie_rottentomatoes_DB <br>$LinkReview<br>";

				$Blong = file_get_contents( "$LinkReview" );
				$first_step = explode( $TagStart , $Blong );
				$second_step = explode($TagEnd , $first_step[1] );
				$Review = $second_step[0];
				$Review = strip_tags($Review);
				//echo "$Review<br><br><br>";

				$Review = addslashes ( $Review );

				$sqlcheck = "SELECT * FROM Review_Critic where Review = '".$Review."'";
				$objQuery = mysqli_query($conn, $sqlcheck);
				$obj = mysqli_fetch_array($objQuery);


				if($obj) {
					//echo "มีReviewอยู่แล้ว <br>";
				} 
				else {
					$sqlINSERT = "INSERT INTO Review_Critic (Review,LinkReview,ID_Movie_rottentomatoes)
					VALUES ('".$Review."','".$LinkReview."','".$ID_Movie_rottentomatoes."')";

					if (mysqli_query($conn, $sqlINSERT)) {
						//echo "บันทึกข้อมูลลงดาต้าเบสเรียบร้อย <br>";
					} else {
						//echo "Error: ".mysqli_error($conn)."<br>";
					}
				}

				echo "#####################################################<br>";
			}
		}
		$j++;
	}
	$i++;
	echo "+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++<br>";
}
mysqli_close($conn);

?>


