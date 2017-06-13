<?php

class MyCrawler extends PHPCrawler {
  function handleDocumentInfo($DocInfo) {

    $str = $DocInfo->url;
    if (stripos("$str","reviews?ref_=") == false) {

      $url=$DocInfo->url;
      //echo "$url<br>";
      $content = file_get_contents($url);
      $content = strip_tags($content,"<p>,<h2>,<b>");

      $i = 0;
      $N = substr_count($content, "<h2>");
      $content = strchr ( $content, "<h2>" );
      $content = str_replace("<p><b>*** This review may contain spoilers ***</b></p>","",$content);
      $start = strpos($content, "<h2>");
      $stop = strpos($content, "</p>")+4;

      $objFopen = fopen("CommentIMDb.txt", 'a');
      while ($i < $N) {
        

        $Titlestart = strpos($content, "<h2>");
        $Titlestop = strpos($content, "</h2>")+5;
        $Titletext = substr($content, $Titlestart, $Titlestop-$Titlestart);
        $Titletext = strip_tags($Titletext);
        $Titletextforpen = "<StartTitletext>$Titletext<EndTitletext>";
        //echo "TitleComment : $Titletext <br>";

        $Commentstart = strpos($content, "<p>");
        $Commentstop = strpos($content, "</p>")+4;
        $Commenttext = substr($content, $Commentstart,$Commentstop-$Commentstart);
        $Commenttext = strip_tags($Commenttext);
        $Commenttextforpen = "<StartCommenttext>$Commenttext<EndCommenttext>";
        //echo "Comment : $Commenttext <br>";
        //////////////////////////////////////////////////////////////////////////////

        $strText = "$Titletextforpen  $Commenttextforpen ";
        //$strText = "$Titletextforpen<br> \r\n";
        fwrite($objFopen, $strText);        

        //////////////////////////////////////////////////////////////////////////////
        $start = strpos($content, "<h2>");
        $stop = strpos($content, "</p>")+4;
        $text = substr($content, $start, $stop-$start);
        $content = str_replace($text,"",$content);
        /////////////////////////////////////////////////////////////////////////////
        $i++;
      }
    }
  }
}
?>