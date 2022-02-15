<?php

// A util to create a user hash

require 'BasicAuth.php';

$basic = new BasicAuth('../users.ini');

echo $basic->hash('admin') . "\n";
