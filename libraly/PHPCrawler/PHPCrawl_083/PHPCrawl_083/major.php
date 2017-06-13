
<?php

// It may take a whils to crawl a site ...
set_time_limit(10000);

// Inculde the phpcrawl-mainclass
include("libs/PHPCrawler.class.php");

// Extend the class and override the handleDocumentInfo()-method 
class MyCrawler extends PHPCrawler 
{
  function handleDocumentInfo($DocInfo) 
  {

  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "Movie";

    $str = $DocInfo->url;
    // Just detect linebreak for output ("\n" in CLI-mode, otherwise "<br>").
    if (PHP_SAPI == "cli") $lb = "\n";
    else{ 
      $lb ="";
    // Print the URL and the HTTP-status-Code
    if(ereg("http://www.imdb.com/title/", $str)) {
    $lb = "<br />";

    if($str != "http://www.imdb.com/title/") {
   
    // print_r (explode("www.majorcineplex.com/movie/",$str));

    /*URL*/
    $url=$DocInfo->url;
    $content = file_get_contents($url);
    $first_step_IDMovie = explode('<link rel="canonical" href="http://www.imdb.com/title/', $content);
    $second_step_IDMovie = explode('/" />',$first_step_IDMovie[1]);
    $text_IDMovie = $second_step_IDMovie[0];
    $textIDMovie = strip_tags($text_IDMovie);
    echo "ID_Movie : $textIDMovie <br>";

     
    /*NameMovie*/
    $first_step_NameMovie = explode('<title>', $content);
    $second_step_NameMovie = explode(' - IMDb</title>',$first_step_NameMovie[1]);
    $text_NameMovie = $second_step_NameMovie[0];
    $textNameMovie = strip_tags($text_NameMovie);
    echo "Name_Movie : $textNameMovie <br>";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$conn -> set_charset("utf8");
  $sql1 = "SELECT * FROM movie where ID_Movie_IMDB = '".$textIDMovie."'";
  $objQuery = mysqli_query($conn, $sql1);
  $objResult = mysqli_fetch_array($objQuery);

  if($objResult) {
    echo "มีข้อมูลอยู่แล้ว <br>";
  } 
  else {
  $sql2 = "INSERT INTO movie (ID_Movie_IMDB,Name_Movie_IMDB)
           VALUES ('".$textIDMovie."', '".$textNameMovie."')";

  if (mysqli_query($conn, $sql2)) {
      echo "บันทึกข้อมูลลงดาต้าเบสเรียบร้อย <br><br>";
  } else {
      echo "Error: ".$sql2."<br><br>".mysqli_error($conn);
  }

  }

mysqli_close($conn);

  }
    }
  }
    //echo $str;
    // Print the refering URL
    //echo "Referer-page: ".$DocInfo->referer_url.$lb;
    
    // Print if the content of the document was be recieved or not
    // if ($DocInfo->received == true)
    //   echo "Content received: ".$DocInfo->bytes_received." bytes".$lb;
    // else
    //   echo "Content not received".$lb; 
    
    // Now you should do something with the content of the actual
    // received page or file ($DocInfo->source), we skip it in this example 
    
    echo $lb;
    
    flush();
  } 
}

// Now, create a instance of your class, define the behaviour
// of the crawler (see class-reference for more options and details)
// and start the crawling-process.

$crawler = new MyCrawler();

// URL to crawl
//$crawler->setURL("http://www.sfcinemacity.com//index.php/th/movie-detail/The-Boss-Baby");
$crawler->setURL("http://www.imdb.com/chart/boxoffice");


// Only receive content of files with content-type "text/html"
$crawler->addContentTypeReceiveRule("#text/html#");

// Ignore links to pictures, dont even request pictures
$crawler->addURLFilterRule("#\.(jpg|gif|png|pdf|jpeg|css|js)$# i");

// Store and send cookie-data like a browser does
$crawler->enableCookieHandling(true);

$crawler->addURLFollowRule("#^http://www.imdb.com/title/tt.# i");
// Set the traffic-limit to 1 MB (in bytes,
// for testing we dont want to "suck" the whole site)
//$crawler->setTrafficLimit(100000 * 1024);
$crawler->setCrawlingDepthLimit(1);

// Thats enough, now here we go
$crawler->go();

// At the end, after the process is finished, we print a short
// report (see method getProcessReport() for more information)
$report = $crawler->getProcessReport();

if (PHP_SAPI == "cli") $lb = "\n";
else $lb = "<br />";
    
echo "Summary:".$lb;
echo "Links followed: ".$report->links_followed.$lb;
echo "Documents received: ".$report->files_received.$lb;
echo "Bytes received: ".$report->bytes_received." bytes".$lb;
echo "Process runtime: ".$report->process_runtime." sec".$lb; 
?>

</body>
</html>