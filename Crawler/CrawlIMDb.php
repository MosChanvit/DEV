<?php

ini_set('max_execution_time', 300);

set_time_limit(1000000);

//$IDMovieIMDb = "tt2398241";
$crawler = new MyCrawler();

// URL to crawl
$crawler->setURL("http://www.imdb.com/title/"."$IDMovieIMDb"."/reviews?ref_=0");

// Only receive content of files with content-type "text/html"
$crawler->addContentTypeReceiveRule("#text/html#");

// Ignore links to pictures, dont even request pictures
$crawler->addURLFilterRule("#\.(jpg|gif|png|pdf|jpeg|css|js)$# i");

// Store and send cookie-data like a browser does
$crawler->enableCookieHandling(true);

$crawler->addURLFollowRule("#^http://www.imdb.com/title/"."$IDMovieIMDb"."/reviews.*[0-9$]# i");

$crawler->setCrawlingDepthLimit(10);

// Thats enough, now here we go
$crawler->go();

// At the end, after the process is finished, we print a short
// report (see method getProcessReport() for more information)
$report = $crawler->getProcessReport();

?>