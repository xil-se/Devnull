<?php

	$talksurl  = "http://halfnarp.events.ccc.de/-/talkpreferences?format=json";
	$req_url   = "http://halfnarp.events.ccc.de/-/talkpreferences/public/%s?format=json";
	$talks_array = json_decode(file_get_contents($talksurl), true);


	$pophashes = array();

	$hashes = [
		"a981edc25c17f332f4d365df496cf290ae2af3b8f37998f7c34cd0a3bfa30a4b",
		"c32c4c6703a68dba1f12c5615128d88380c28db2adc74aa21c902dbd6407f848",
		"a7798f9ed51cc8a06649516c12f11e766ec65c1f0eeb2952c7624433a296feaa"
	];


	foreach($hashes as $hash){
		$json = json_decode(file_get_contents(sprintf($req_url, $hash)), true);
		$pophashes = array_merge($pophashes, $json["talk_ids"]);
	}

	$pophashes = array_count_values($pophashes);
	arsort($pophashes);


	$maxval = array_values($pophashes)[0];
	
	foreach($pophashes as $k=>$v){
		foreach($talks_array as $talk){
			if($k == $talk["event_id"]){


				$color = "rgb(0, ". (255-round(($v / $maxval) * 127)) . ", 0)";
				echo "<div style='background-color:$color; color:#CCCCCC; font-weight:bold;'>";
				echo $v, " : <i>", $talk["track_name"], "</i> -- ", $talk["title"], "\n";
				echo "</div>";
				continue;
			}
		}
	}

?>
