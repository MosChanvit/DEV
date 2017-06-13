<?php

$content = file_get_contents( "https://www.rottentomatoes.com/browse/in-theaters/" );

$start = strpos($content, '{"@type":');
$stop = strpos($content, '}</script>');
$text = substr($content, $start, $stop-$start);
$text = strip_tags($text);

$count = substr_count($text, "/m/");
$i=1;
//echo "$count<br>";
///////////////////////////////////////////////////////////////////////////////////////

while($i<=$count){

	$startm = strpos($text, "/m/");
	$stopm = strpos($text, '"}');
	$NameMovie = substr($text, $startm, $stopm-$startm);
	$NameMovie = str_replace("/m/","",$NameMovie);


	$text = str_replace($NameMovie,"",$text);
	$text = strstr ($text, ",{" );
	$text = strstr ($text, "{" );
	$NameMovie = str_replace("/m/","",$NameMovie);

	echo "Name_Movie : $NameMovie <br>";




	require("ConnectDB.php");

	$conn -> set_charset("utf8");
	$sql1 = "SELECT * FROM moviename_rottentomatoes where Name_Movie_rottentomatoes = '".$NameMovie."'";
	$objQuery = mysqli_query($conn, $sql1);
	$objResult = mysqli_fetch_array($objQuery);

	if($objResult) {
		echo "มีชื่อหนังอยู่แล้ว <br><br>";
	} 
	else {
		$sql2 = "INSERT INTO moviename_rottentomatoes (Name_Movie_rottentomatoes)
		VALUES ('".$NameMovie."')";

		if (mysqli_query($conn, $sql2)) {
			echo "บันทึกข้อมูลลงดาต้าเบสเรียบร้อย <br><br>";
		} else {
			echo "Error: ".$sql2."<br><br>".mysqli_error($conn);
		}

	}

	mysqli_close($conn);

	$i++;
}
?>