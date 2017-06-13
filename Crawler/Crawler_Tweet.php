<?php 

function removeEmojis( $string ) {
	$string = str_replace( "?", "{%}", $string );
	$string  = mb_convert_encoding( $string, "ISO-8859-1", "UTF-8" );
	$string  = mb_convert_encoding( $string, "UTF-8", "ISO-8859-1" );
	$string  = str_replace( array( "?", "? ", " ?" ), array(""), $string );
	$string  = str_replace( "{%}", "?", $string );
	return trim( $string );
}

require("../libraly/Twitter-API-Search-Tweets-or-Hashtags-master/Twitter-API-Search-Tweets-or-Hashtags-master/twitteroauth/twitteroauth.php");
require("ConnectDB.php");


$sql = "SELECT * FROM moviename";
$result = $conn->query($sql);

     // startGetComment MovieAll DB
while($row = $result->fetch_assoc())  {
    $consumer_key = "vzTp7UU6ejSntct4Sf0aVb7Jq";
    $consumer_secret = "8mA9xUlxaMmzko5SS5JUvhtfE8xPSlpI9VJ8faYVejnvui2paw";
    $access_token = "3242458940-4bWarL4VyAsNaf2DbUD3SC7IPLTgUoicJWieGtm";
    $access_token_secret = "xb2p1mVCBHE2EhKYNsQkXFCyjMt4Hh6N1ZrWCfZbXWSZK";
    $twitter = new TwitterOAuth($consumer_key,$consumer_secret,$access_token,$access_token_secret);

    $Name_Movie_IMDB = $row['Name_Movie_IMDB'];
    $ID_Movie = $row['ID_Movie'];
    $Limit_Crawler_Tweet = $row['Limit_Crawler_Tweet'];



    if($Limit_Crawler_Tweet<200){

        $search  = array(' ','(2017)','(2016)');

        $text = str_replace($search,"",$Name_Movie_IMDB);

        echo "##### Word search/tweets : $text #####<br><br>";

        $languages ="en"; //th
        $result_type = "recent";//mixed recent popular
        $url = 'https://api.twitter.com/1.1/search/tweets.json?q='.$text.'&result_type='.$result_type.'&count=100&lang='.$languages;
        $tweets = $twitter->get($url);
        $count=0;

        foreach ($tweets->statuses as $key => $tweet) {

          $name=$tweet->user->name;
          $nameencode=utf8_encode($name);

          $text=$tweet->text;
          $tweetclean = $text;
          $tweetclean = preg_replace('/#([\w-]+)/i', '', $tweetclean); // @someone
          $tweetclean = preg_replace('/@([\w-]+)/i', '', $tweetclean); // #tag
          $tweetclean = preg_replace('/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/', '', $tweetclean);


          $tweetclean = removeEmojis("$tweetclean");
          $tweetcleanencode=utf8_encode($tweetclean);
          $tweetclean = addslashes (  $tweetclean);
          if(ereg("^RT",$text)==1){};
          if(ereg("^RT",$text)==0){
          //echo "$c ";$c++;
          //echo "<br>";
             // echo "Name Twitter : ".$name."<br>";

              $name = addslashes (  $name);
           //$strings = $tweetclean;
           //echo "String: $strings"."/wwww";
              $test[$count]=$tweetclean;
              $count++;
             // echo "Tweet : ".$tweetclean."<br><br>";

              $search  = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9','0','.',' ',';',':','-','_','!',"—","\"","'",",","@","?","=","…","&","‘","’","(",")","|","+","�","•","/","™","%","“","”","}","{","[","]","$","«","»");

              $replace = "";
              $emoji = str_replace($search,$replace, $tweetclean);
              $emojiencode = utf8_encode($emoji);



                 // In Sert DB & Check connection
              $conn = mysqli_connect($servername, $username, $password, $dbname);
              if (!$conn) {
                  die("Connection failed: " . mysqli_connect_error());
              }
              $conn -> set_charset("utf8");
              $sql1 = "SELECT * FROM commenttwitter where CommentTwitter = '".$tweetclean."'";
              $objQuery = mysqli_query($conn, $sql1);
              $objResult = mysqli_fetch_array($objQuery);

              if($objResult) {
                  //echo "มีข้อมูล CommentTwitter เเล้ว <br>";
              } 
              else {

                  $sql2 = "INSERT INTO commenttwitter (Name_UserTwitter,CommentTwitter,ID_Movie)
                  VALUES ('".$name."', '".$tweetclean."', '".$ID_Movie."')";

                  if (mysqli_query($conn, $sql2)) {
                     echo "บันทึกข้อมูลลงดาต้าเบสเรียบร้อย <br><br>";
                 } else {
                     echo "Error: ".$sql2."<br><br>".mysqli_error($conn);
                 }
             }
         }
     }

     $Limit_Crawler_Tweet+=1;
     $sqlLimit = "UPDATE  moviename SET  Limit_Crawler_Tweet = '$Limit_Crawler_Tweet'
     WHERE ID_Movie = '$ID_Movie'";
     mysqli_query($conn,$sqlLimit);
     mysqli_close($conn);
     echo "=============================================== <br><br>";
 }

}
?>