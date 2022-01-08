<?php

require 'BasicAuth.php';

// Instantiate the class for HTTP Basic Authentication
$basic = new BasicAuth('../users.ini');
// Make every request require authorization
if (!$basic->auth()) {
    die;
}

// Open the sqlite db
class PagesDB extends SQLite3
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
// Instantiate a copy of the DB
$db = new PagesDB;

// Split the URI into pieces
$uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

switch($uriSegments[1]) {
    case 'save':
        savePage($db, $_POST['page'], $_POST['text']);
        die;
        break;
    default:
}

function savePage($db, $pageId, $pageText) {
    $now = date('Y-m-d');
    $statement = $db->prepare('UPDATE `pages` SET `text` = :text, `edited` = :now WHERE `id` = :id');
    $statement->bindValue(':text', $pageText, SQLITE3_TEXT);
    $statement->bindValue(':now', $now, SQLITE3_TEXT);
    $statement->bindValue(':id', $pageId, SQLITE3_INTEGER);
    $result = $statement->execute();
}

// Query for the newest document (we almost always need this)
$statement = $db->prepare('SELECT `id`, `title`, `text` FROM `pages` ORDER BY `id` DESC LIMIT 1');
$result = $statement->execute();
$page = $result->fetchArray(SQLITE3_ASSOC);
// Grab the last page ID so we know how to wrap navigation
$last = $page['id'];

// If a page is passed, load it's body
if (isset($_REQUEST['page'])) {
    // If the page value is "new" create a new page and redirect back
    if ($_REQUEST['page'] === 'new') {
        // We want a new page
        $now = date('Y-m-d');
        $statement = $db->prepare('INSERT INTO `pages` (`title`, `text`, `created`) VALUES ("Untitled", "", :now)');
        $statement->bindValue(':now', $now, SQLITE3_TEXT);
        $result = $statement->execute();    
        // Redirect back again
        header("Location: /");
    }
    $statement = $db->prepare('SELECT `id`, `title`, `text` FROM `pages` WHERE `id` = :id');
    $statement->bindValue(':id', $_REQUEST['page'], SQLITE3_INTEGER);
    $result = $statement->execute();
    $page = $result->fetchArray(SQLITE3_ASSOC);
} else {
    // Query for the newest document
    $statement = $db->prepare('SELECT `id`, `title`, `text` FROM `pages` ORDER BY `id` DESC LIMIT 1');
    $result = $statement->execute();
    $page = $result->fetchArray(SQLITE3_ASSOC);
}

$id = $page['id'];
$title = $page['title'];
$text = $page['text'];
$prev = $page['id'] - 1;
$next = $page['id'] + 1;

// Wrap the prev page to the last page if we're already at the first page
if ($prev < 1) {
    $prev = $last;
}

// Wrap the next page to the first page if we're already at the last page
if ($next > $last) {
    $next = 1;
}

require 'edit.html';
