#!/usr/bin/php -q
<?php
if (!file_exists("config.php")) die("FILE config.php NOT FOUND");
include "config.php";
$last_id = file_get_contents(getcwd().'/last_id');
$json = file_get_contents("http://search.twitter.com/search.json?tag=jera&rpp=100&since_id=".$last_id);
$dados = json_decode($json);
$tweets = array_reverse($dados->results);
echo sizeof($tweets).' tweets found!';
if (!empty($tweets)) {
	foreach ($tweets as $k => $tweet) {		
		if (in_array($tweet->from_user_id, $white_list) && substr($tweet->text, 0, 3) != "RT ")
			tweet($tweet->from_user, $tweet->text);
		else if ($tweet->from_user_id != 126665619) 
			error_log("\n[@{$tweet->from_user}] {$tweet->text} ({$tweet->created_at})", 3, 'posts.log');
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
        curl_setopt($curl, CURLOPT_URL, "$tweetUrl");
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "status=$status");
        curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");

	while ($posted == 0) {
		echo $posted;
		$result = curl_exec($curl);
		$resultArray = curl_getinfo($curl);
		if ($resultArray['http_code'] == 200) $posted = 1;
		sleep(3);
	}
	curl_close($curl);
	sleep(3);
}
?>

