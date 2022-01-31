<?php

/**
 * A class for HTTP Basic Authentication
 */
class BasicAuth {

    public function __construct($passwordFile)
    {
        // If the password file exists, use it
        if (file_exists($passwordFile)) {
            $this->users = parse_ini_file($passwordFile);
        } else {
            // Use admin:admin if there is no password file
            $this->users = [
                "admin" => "$2y$10$25JAucKk7DHulMJvBPTEcusQ4IV6G0/8YhKyaXbhFAxjJOBjCv.6e"
            ];
        }
    }

    // Authenticate the user
    public function auth() {
        // If the username and password are not empty
        if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
            error_log("Not empty");
            // If this user ID exists in our array
            if (array_key_exists($_SERVER['PHP_AUTH_USER'], $this->users)) {
                error_log("ID exists");
                // Grab the hash for this user from the array
                $storedHash = $this->users[$_SERVER['PHP_AUTH_USER']];
                // Verify the password against the hash
                if (password_verify($_SERVER['PHP_AUTH_PW'], $storedHash)) {
                    error_log("Password verified");
                    // Verified
                    return true;
                }
            }
        }
        // Send back the header requesting authentication
        header('WWW-Authenticate: Basic realm="Example Service"');
        header('HTTP/1.0 401 Unauthorized');
        // Some text to display if the user hits cancel
        echo 'Unauthorized';
        // Return false because the user isn't authenticated
        return false;
    }

    // Bcrypt the password and return a hash
    public function hash($password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        return $hash;
    }

}
