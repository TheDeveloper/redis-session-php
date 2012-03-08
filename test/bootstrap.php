<?php
//ini_set('session.serialize_handler', 'wddx');
require_once(__DIR__.'/../modules/predis/lib/Predis/Autoloader.php');
Predis\Autoloader::register();
require_once(__DIR__.'/../redis-session.php');

$redis = new Predis\Client();
array_map(function($key) use (&$redis) {
  //var_dump($redis->get($key));
  $redis->del($key);
},$redis->keys("session:php:*"));
RedisSession::start();
$_SESSION['test'] = "ohai";
$_SESSION['md'] = array('test2' => array('multidimensional' => 'array'));
$_SESSION['more'] = new stdClass;
