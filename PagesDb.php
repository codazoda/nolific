<?php

// Open the sqlite db
class PagesDb extends SQLite3
{
    function __construct()
    {
        // Check if a data directory exists
        if(!is_dir('data')){
            // Create it
            mkdir('data', 0755);
        }
        // If there's an environment variable set use it as the data path
        if (!empty(getenv('NOLIFIC_DATA'))) {
            $dataDir = getenv('NOLIFIC_DATA');
        } else {
            $dataDir = 'data';
        }
        // Set the DB filename to user.sqlite in the data directory
        $dbFile = $dataDir . '/' . $_SERVER['PHP_AUTH_USER'] . '.sqlite';
        // Check if the database file exists
        if (!file_exists($dbFile)) {
            // Copy an empty database over for this user
            copy('pages.sqlite', $dataDir . '/' . $_SERVER['PHP_AUTH_USER'] . '.sqlite');
        }
        // Make a couple backups
        $dayOfWeek = date('l');
        $yearAndMonth = date('Y-F');
        copy($dbFile, "{$dataDir}{$_SERVER['PHP_AUTH_USER']}-{$dayOfWeek}.sqlite");
        copy($dbFile, "{$dataDir}{$_SERVER['PHP_AUTH_USER']}-{$yearAndMonth}.sqlite");
        // Open the database file
        $this->open($dbFile);
    }
}
