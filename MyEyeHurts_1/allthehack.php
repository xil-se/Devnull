<?php

$talksurl  = "http://halfnarp.events.ccc.de/-/talkpreferences?format=json";
$req_url   = "http://halfnarp.events.ccc.de/-/talkpreferences/public/%s?format=json";
$talks_array = json_decode(file_get_contents($talksurl), true);


$pophashes = array();

$hashes = file("db.txt");


$users = array();

if(isset($_GET["users"]))
	$users = explode(",", $_GET["users"]);

foreach($hashes as $hash){
	list($user, $hasho) = explode(" ", $hash);

	if(count($users) > 0){
		if(!in_array($user, $users))
			continue;
	}

	$json = json_decode(file_get_contents(sprintf($req_url, trim($hasho))), true);
	$pophashes = array_merge($pophashes, $json["talk_ids"]);
}

$pophashes = array_count_values($pophashes);
arsort($pophashes);


$maxval = array_values($pophashes)[0];

foreach($pophashes as $k=>$v){
	foreach($talks_array as $talk){
		if($k == $talk["event_id"]){
			$color = "rgb(0, ". (255-round(($v / $maxval) * 127)) . ", 0)";
			$textcolor = "rgb(255, ". (255-(255-round(($v / $maxval) * 127))) . ", 255)";
			$textcolor = "#000";
			echo "<div style='background-color:$color; color:$textcolor; font-weight:bold;'>";
			echo $v, " : <i>", $talk["track_name"], "</i> -- ", $talk["title"], "\n";
			echo "</div>";
			continue;
		}
	}
}

?>
