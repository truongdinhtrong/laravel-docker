<?php
$r = new Redis();
$r->connect('redis-svc', 6379); 
$r->auth('redis@_123');
echo "\nServer is running -------: ".$redis->ping("OK");
