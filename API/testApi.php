<?php
// Get cURL resource
function getApi_IDMovie($name_movie) {
$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'http://movieapi.plearnjai.com/api/api_getID_IMDb.php',
    CURLOPT_USERAGENT => 'Codular Sample cURL Request',
    // CURLOPT_HTTPHEADER => 'Content-Type:application/json',
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => array(
        nameMovie => $name_movie
    )
));
// Send the request & save response to $resp
$resp = curl_exec($curl);
$json_array = json_decode($resp, true);

$worker_stats = $json_array['ID_Movie']['ID_IMDb'];
return "$worker_stats";

if(!curl_exec($curl)){
    die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
}

// Close request to clear up some resources
curl_close($curl);

}

echo getApi_IDMovie("wonder");
?>