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
        // Route dthe request
        switch($uriSegments[1]) {
            case 'save':
                $this->handleSave($_POST['page'], $_POST['text']);
                die; // TODO: Remove this when fall through is okay
                break;
            case 'page':
                $this->handlePage($uriSegments[2]);
                die; // TODO: Remove this when fall through is okay
                break;
            case 'new':
                $this->handleNew();
                die; // TODO: Remove this when fall through is okay
                break;
            default:
                $this->handlePage($this->last);
        }
    }

    /**
     * Save the page data that was passed into the DB
     */
    private function handleSave($pageId, $pageText) {
        $now = date('Y-m-d');
        $statement = $this->db->prepare('UPDATE `pages` SET `text` = :text, `edited` = :now WHERE `id` = :id');
        $statement->bindValue(':text', $pageText, SQLITE3_TEXT);
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
            $prev = $this->last;
        }
        // Template variable to wrap the next page to the first page if we're already at the last page
        if ($next > $this->last) {
            $next = 1;
        }
        // Pull in the editor UI
        require 'edit.html';
    }

    /**
     * Handle a request for a new page to be added
     */
    private function handleNew() {
        // Format todays date so we can write it into the DB
        $now = date('Y-m-d');
        // Query to insert a new blank page with todays created date
        $statement = $this->db->prepare('INSERT INTO `pages` (`title`, `text`, `created`) VALUES ("Untitled", "", :now)');
        $statement->bindValue(':now', $now, SQLITE3_TEXT);
        $result = $statement->execute();
        // Redirect back to the main page which automatically switches to the newest page
        header("Location: /");
    }

    /**
     * Fetch the last page (highest page id) from the database and return it.
     */
    private function lastPage(): int {
        // Query for the newest document id
        $statement = $this->db->prepare('SELECT `id`, `title`, `text` FROM `pages` ORDER BY `id` DESC LIMIT 1');
        $result = $statement->execute();
        $page = $result->fetchArray(SQLITE3_ASSOC);
        // Grab the last page ID so we know how to wrap navigation
        return $page['id'];
    }
}

$app = new App;
