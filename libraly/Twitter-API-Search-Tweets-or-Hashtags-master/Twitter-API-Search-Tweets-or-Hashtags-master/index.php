<?php 
//include "../../libraly sentiment php/phpInsight-master/examples/demo.php";
include "twitteroauth/twitteroauth.php";

$consumer_key = "vzTp7UU6ejSntct4Sf0aVb7Jq";
$consumer_secret = "8mA9xUlxaMmzko5SS5JUvhtfE8xPSlpI9VJ8faYVejnvui2paw";
$access_token = "3242458940-4bWarL4VyAsNaf2DbUD3SC7IPLTgUoicJWieGtm";
$access_token_secret = "xb2p1mVCBHE2EhKYNsQkXFCyjMt4Hh6N1ZrWCfZbXWSZK";

$twitter = new TwitterOAuth($consumer_key,$consumer_secret,$access_token,$access_token_secret);
$text = "lalaland";
$languages ="en"; //th
$result_type = "recent";//mixed recent popular
$url = 'https://api.twitter.com/1.1/search/tweets.json?q='.$text.'&result_type='.$result_type.'&count=100&lang='.$languages;
$tweets = $twitter->get($url);
$c =1;
$Rt;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "twitter";

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Twitter API SEARCH</title>
</head>
<body>

<?php $count=0;  foreach ($tweets->statuses as $key => $tweet) { ?>

    <?php 
    
    $name=$tweet->user->name;
    $nameencode=utf8_encode($name);


    $text=$tweet->text;
    $tweetclean = $text;
    $tweetclean = preg_replace('/#([\w-]+)/i', '', $tweetclean); // @someone
    $tweetclean = preg_replace('/@([\w-]+)/i', '', $tweetclean); // #tag
    $tweetclean = preg_replace('/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/', '', $tweetclean);
    $tweetcleanencode=utf8_encode($tweetclean);
    $search2 = array('\'');
    $tweetclean2 = str_replace($search2,"\'", $tweetclean);
    

    if(ereg("^RT",$text)==1){};
    if(ereg("^RT",$text)==0){
    echo "$c ";$c++;
    //echo "<br>";
    echo $name." ==> ";
    //$strings = $tweetclean;
    //echo "String: $strings"."/wwww";
    $test[$count]=$tweetclean;
    $count++;
    echo $tweetclean."<br>";

    $search  = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9','0','.',' ',';',':','-','_','!',"—","\"","'",",","@","?","=","…","&","‘","’","(",")","|","+","�","•","/","™","%","“","”","}","{","[","]","$","«","»");

    $replace = "";
    $emoji = str_replace($search,$replace, $tweetclean);
    $emojiencode = utf8_encode($emoji);
    echo $emoji."<br>";

    // $conn = new mysqli($servername, $username, $password, $dbname);
    // $sql = "INSERT INTO lalaland (name,tweet,emoji)
    // VALUES ('$name','$tweetclean2','$emoji')";
    // if ($conn->query($sql) === TRUE) {
    // echo "New record created successfully"."<br>";
    // } else {
    // echo "Error: " . $sql . "<br>" . $conn->error."<br>";
    // }
    // $conn->close();

     } ;

    ?>
    
    
<?php }  ?>
  

</body>
</html>