#!/usr/bin/php -q
<?php
if (!file_exists("config.php")) die("FILE config.php NOT FOUND");
include "config.php";
$dados = json_decode(file_get_contents("http://search.twitter.com/search.json?tag=jera&rpp=100&since_id=".file_get_contents(getcwd().'/last_id')));
$tweets = array_reverse($dados->results);
echo "\n".sizeof($tweets).' tweets found!';
if (!empty($tweets)) {
	foreach ($tweets as $k => $tweet) {
		 if (substr($tweet->text, 0, 3) != "RT ") {
			if (in_array($tweet->from_user_id, $white_list))
				tweet($tweet->from_user, $tweet->text);
			else if ($tweet->from_user_id != 126665619) 
				error_log("\n[@{$tweet->from_user}] {$tweet->text} ({$tweet->created_at})", 3, 'posts.log');
		}
	}
	$last = array_pop($tweets);
	$last_id = $last->id;
	$file = fopen('last_id', 'w');
	fwrite($file, $last->id);
	fclose($file);       
}

function tweet($user, $status) {
	$tweetUrl = 'http://www.twitter.com/statuses/update.xml';
	$status = "RT @".$user.": ".$status;	
	$posted = 0;
	$curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $tweetUrl);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "status=$status");
    curl_setopt($curl, CURLOPT_USERPWD, USERNAME.":".PASSWORD);

	while ($posted == 0) {		
		$result = curl_exec($curl);
		$resultArray = curl_getinfo($curl);
		if ($resultArray['http_code'] == 200) $posted = 1;
		else print_r($resultArray);
		sleep(3);
	}
	echo "\nPosted successfully!";
	curl_close($curl);
	sleep(3);
}
?>

