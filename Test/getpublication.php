<?php

$content = file_get_contents( "https://www.rottentomatoes.com/source-338" );
$count = substr_count($content, '<div class="col-sm-1 col-xs-2 table-cell-border clearfix">');
$i='1';
$content = strstr($content, '<div class="col-sm-1 col-xs-2 table-cell-border clearfix">'.$i.'</div>');
while($i<=$count){

	$content = strstr($content, '<div class="col-sm-1 col-xs-2 table-cell-border clearfix">'.$i.'</div>');

	$startm = strpos($content, '/m/');
	$stopm = strpos($content, '" class="movie-link">');
	$NameMovie = substr($content, $startm, $stopm-$startm);
	$NameMovie = str_replace("/m/","",$NameMovie);
	$NameMovie = strip_tags($NameMovie);

	echo "NameMovie : $NameMovie<br>";

	$startm = strpos($content, '/critic/');
	$stopm = strpos($content, '" class="package-body-text">');
	$NameCritic = substr($content, $startm, $stopm-$startm);
	$NameCritic = str_replace("/critic/","",$NameCritic);

	echo "NameCritic : $NameCritic<br>";

	$startm = strpos($content, '<a class="unstyled articleLink" href="');
	$stopm = strpos($content, '"target="');
	$LinkReview = substr($content, $startm, $stopm-$startm);
	$LinkReview = str_replace('<a class="unstyled articleLink" href="',"",$LinkReview);
	echo "LinkReview : $LinkReview<br><br>";

	$i++;


}

?>