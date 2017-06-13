<?php echo '<pre>';

include './autoload.php';
 
$tokenizer = new HybridLogic\Classifier\Basic;
$classifier = new HybridLogic\Classifier($tokenizer);

require("train_character.php");
require("train_soundtrack.php");
require("train_story.php");

$groups = $classifier->classify('As what I\'ve seen so far on my 29 year old life span.');
print_r($groups);

echo "<br>";

foreach($groups as $group => $groups_value) {
    echo "Key=" . $group ;
    break;
}
