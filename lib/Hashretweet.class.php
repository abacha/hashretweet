<?php
require 'Configuration.class.php';
require 'Twitter.class.php';
require 'Rest.class.php';

class Hashretweet {

	private $twitter;
	private $config;

	function __construct($config) {
		$this->verifyConfig();
		$this->verifyLog();
		$this->config = $config;
		$this->twitter = new Twitter($config->USERNAME, $config->PASSWORD);
	}

	private function verifyConfig() {
		if (!file_exists(getcwd()."/lib/config.inc")) die("FILE config.inc.php NOT FOUND");
	}

	private function verifyLog() {
		if (!file_exists("tweets.log")) touch("tweets.log");
	}

	function read($tag) {
		$url = "http://search.twitter.com/search.json?tag=".$tag."&rpp=100&since_id=".file_get_contents('last_id');
		$dados = Rest::at($url)->getJson();
		$this->log(sizeof($dados->results).' tweets found!');
		return array_reverse($dados->results);
	}

	function retweet($tag) {
		$tweets = $this->read($tag);
		foreach ($tweets as $k => $tweet) {
			$posted = false;
			if ((in_array($tweet->from_user_id, $this->config->WHITELIST)) && (substr($tweet->text, 0, 3) != "RT ") && ($tweet->from_user_id != $this->config->USERID)) {
				$this->tweet($tweet->from_user, $tweet->text);
				$posted = true;
			}
			$this->logTweet($tweet, $posted);
		}
		$last = array_pop($tweets);
		$this->saveLastId($last->id);
	}

	private function tweet($user, $status) {
		$timeout = 0;
		do {
			$success = $this->twitter->update("RT @".$user.": ".$status);
			$success = true;
			if ($timeout >= 10) $success = true;
			$timeout++;
		} while (!$success);
		return true;
	}

	function saveLastId($last_id) {
		$file = fopen(getcwd().'/last_id', 'w');
		fwrite($file, $last_id);
		fclose($file);
	}

	function logTweet($tweet, $posted)	{
		return $this->log((($posted) ? '(!) ':'')."@{$tweet->from_user}: {$tweet->text} ", date('Y-m-d H:i:s', strtotime($tweet->created_at)));
	}

	private function log($text, $date = null) {
		if (empty($date)) $date = date('Y-m-d H:i:s');
		error_log("\n[{$date}] {$text}", 3, 'tweets.log');
	}

	public function setTwitter($twitter) {
		return $this->twitter = $twitter;	
	}

	public function getTwitter() {
		return $this->twitter;
	}
}

class HashretweetMock extends Hashretweet {
	function __construct() {}
}

?>
