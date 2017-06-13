<?php
$ID_Link = "tt3896198";
$url_Publications = "http://www.imdb.com/title/"."$ID_Link";
$content = file_get_contents( "$url_Publications" );
$start = strpos($content, '<div class="metacriticScore score_favorable titleReviewBarSubItem">');
$stop = strpos($content, '<div class="titleReviewBarSubItem">');
$metacriticScore = substr($content, $start, $stop-$start);
echo "metacriticScore : $metacriticScore<br>";
?>

