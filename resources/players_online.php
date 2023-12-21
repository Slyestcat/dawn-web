<?php
$fp = fopen(dirname( __FILE__ ) . '/players.txt', 'c+');

$count  = (int) fread($fp, filesize(dirname( __FILE__ ) . '/players.txt'));
$remove = (isset($_GET['remove']) ? -1 : 0);
$add    = (isset($_GET['add']) ? 1 : 0);

$players = (int) (isset($_GET['players']) ? $_GET['players'] : ($count + $remove + $add));
ftruncate($fp, 0);
fseek($fp, 0);
fwrite($fp, $players);

fclose($fp);

echo $players;
?>