<?php

// Open the sqlite db
class PagesDb extends SQLite3
{
    function __construct()
    {
        // If there's an environment variable set for the user
        if (!empty(getenv('NOLIFIC_USER'))) {
            $user = getenv('NOLIFIC_USER');
        } else {
            $user = 'nolific';
        }
        // If there's an environment variable set use it as the data path
        if (!empty(getenv('NOLIFIC_DATA'))) {
            $dataDir = getenv('NOLIFIC_DATA');
        } else {
            $dataDir = 'data';
        }
        // Check if a data directory exists
        if(!is_dir($dataDir)){
            // Create it
            mkdir('data', 0755);
        }
        // Set the DB filename to user.sqlite in the data directory
        $dbFile = $dataDir . '/' . $user . '.sqlite';
        // Check if the database file exists
        if (!file_exists($dbFile)) {
            // Copy an empty database over for this user
            copy('pages.sqlite', $dataDir . '/' . $user . '.sqlite');
        }
        // Make a couple backups
        $dayOfWeek = date('l');
        $yearAndMonth = date('Y-F');
        copy($dbFile, "{$dataDir}/{$user}-{$dayOfWeek}.sqlite");
        copy($dbFile, "{$dataDir}/{$user}-{$yearAndMonth}.sqlite");
        // Open the database file
        $this->open($dbFile);
    }
}
