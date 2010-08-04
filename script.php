<?php
require 'lib/Hashretweet.class.php';
require 'lib/config.inc';

$hashretweet = new Hashretweet(new Configuration($config));
$hashretweet->retweet('jera');

?>
