<?php

// Open the sqlite db
class PagesDb extends SQLite3
{
    function __construct()
    {
        // Open my personal file if it exists (this keeps my data out of the repo)
        if (file_exists('joeldare.sqlite')) {
            $dbFile = 'joeldare.sqlite';
        } else {
            $dbFile = 'pages.sqlite';
        }
        // Open the database file
        $this->open($dbFile);
    }
}
