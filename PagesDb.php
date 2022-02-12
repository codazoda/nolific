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
        // Set the DB filename to user.sqlite in the data directory
        $dbFile = 'data/' . $_SERVER['PHP_AUTH_USER'] . '.sqlite';
        // Check if the database file exists
        if (!file_exists($dbFile)) {
            // Copy an empty database over for this user
            copy('pages.sqlite', 'data/' . $_SERVER['PHP_AUTH_USER'] . '.sqlite');
        }
        // Open the database file
        $this->open($dbFile);
    }
}
