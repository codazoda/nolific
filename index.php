<?php

require 'BasicAuth.php';
require 'PagesDb.php';

class App {

    private $config = [
        'userFile' => '../users.ini'
    ];
    public $db;
    public $last = 0; // The last page ID

    public function __construct()
    {
        // Authenticate all requests
        $this->authenticate();
        // Instanciate the database
        $this->db = new PagesDb;
        // Grab the last page in the DB
        $this->last = $this->lastPage();
        // Route this request
        $this->route();
    }

    /**
     * Authenticate a request using the BasicAuth class
     */
    private function authenticate() {
        // Instantiate the class for HTTP Basic Authentication
        $basic = new BasicAuth($this->config['userFile']);
        // Make every request require authorization
        if (!$basic->auth()) {
            die;
        }
    }

    /**
     * Route a request based on the URI
     */
    private function route() {
        // Split the URI into pieces
        $uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        // Route the request
        switch($uriSegments[1]) {
            case 'page':
                $this->handlePage($uriSegments[2]);
                break;
            case 'search':
                $this->handleSearch();
                break;
            case 'save':
                $this->handleSave($_POST['page'], $_POST['text']);
                break;
            case 'new':
                $this->handleNew();
                break;
            case 'find':
                $this->handleFind($_GET['search']);
                break;
            default:
                $this->handlePage($this->last['id']);
        }
    }

    /**
     * Save the page data that was passed into the DB
     */
    private function handleSave($pageId, $pageText) {
        $now = date('Y-m-d');
        // Grab the first 150 characters as a potential title
        $potentialTitle = substr($pageText, 0, 150);
        // Strip everything up to the first newline
        $potentialTitle = strstr($potentialTitle, "\n" , true);
        // Trim white space and #'s from the ends of the string
        $pageTitle = trim($potentialTitle, "# \n\r\t\v\x00");
        // If the title is empty, set it to Untitled
        if (empty($pageTitle)) {
            $pageTitle = "Untitled";
        }
        $statement = $this->db->prepare('UPDATE `pages` SET `text` = :text, `title` = :title, `edited` = :now WHERE `id` = :id');
        $statement->bindValue(':text', $pageText, SQLITE3_TEXT);
        $statement->bindValue(':title', $pageTitle, SQLITE3_TEXT);
        $statement->bindValue(':now', $now, SQLITE3_TEXT);
        $statement->bindValue(':id', $pageId, SQLITE3_INTEGER);
        $result = $statement->execute();
    }

    /**
     * Load the requested page from the database and show the edit page
     */
    private function handlePage($pageId) {
        // Query the DB for the page information
        $statement = $this->db->prepare('SELECT `id`, `title`, `text` FROM `pages` WHERE `id` = :id');
        $statement->bindValue(':id', $pageId, SQLITE3_INTEGER);
        $result = $statement->execute();
        $page = $result->fetchArray(SQLITE3_ASSOC);
        // Set some variables for the template based on the values from the DB
        $id = $page['id'];
        $title = $page['title'];
        $text = $page['text'];
        $prev = $page['id'] - 1;
        $next = $page['id'] + 1;
        // Template variable to wrap the prev page to the last page if we're already at the first page
        if ($prev < 1) {
            $prev = $this->last['id'];
        }
        // Template variable to wrap the next page to the first page if we're already at the last page
        if ($next > $this->last['id']) {
            $next = 1;
        }
        // Pull in the editor UI
        require 'edit.html';
    }

    /**
     * Handle a request for a new page to be added
     */
    private function handleNew() {
        // If the last page is not blank, create a new blank page
        if ($this->last['text'] != '') {        
            // Format todays date so we can write it into the DB
            $now = date('Y-m-d');
            // Query to insert a new blank page with todays created date
            $statement = $this->db->prepare('INSERT INTO `pages` (`title`, `text`, `created`) VALUES ("Untitled", "", :now)');
            $statement->bindValue(':now', $now, SQLITE3_TEXT);
            $result = $statement->execute();
        }
        // Redirect back to the main page which automatically switches to the newest page
        header("Location: /");
    }

    /**
     * Handle a request for the search page
     */
    private function handleSearch() {
        // Pull in the search UI
        require 'search.html';
    }

    /**
     * Handle a request to find search results
     */
    private function handleFind($search) {
        // Query for matching documents
        $statement = $this->db->prepare('SELECT `id`, `title`, SUBSTR(`text`, 1, 80) AS `fragment` FROM `pages` WHERE `text` LIKE :search ORDER BY `id` DESC');
        $statement->bindValue(':search', "%{$search}%", SQLITE3_TEXT);
        $result = $statement->execute();
        // Loop through the results
        while ($page = $result->fetchArray(SQLITE3_ASSOC)) {
            echo "<p><a href=\"/page/{$page['id']}\">{$page['title']}</strong> ({$page['id']})</a><br>{$page['fragment']}</p>";
        }
    }

    private function handleComponent($component) {
        switch($component) {
            case 'menu':
                include 'components/menu.html';
                break;
            default:
        }
    }

    /**
     * Fetch the last page (highest page id) from the database and return an array
     * containing the id and text body.
     */
    private function lastPage(): array {
        // Query for the newest document id
        $statement = $this->db->prepare('SELECT `id`, `text` FROM `pages` ORDER BY `id` DESC LIMIT 1');
        $result = $statement->execute();
        $page = $result->fetchArray(SQLITE3_ASSOC);
        // Grab the last page ID so we know how to wrap navigation
        return $page;
    }
}

$app = new App;
