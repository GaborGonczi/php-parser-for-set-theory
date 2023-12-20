<?php
namespace app\server\classes;

/**
* Config class represents the configuration for connecting to a database.
* 
* This class has properties for the host, database name, user name, and password.
* 
* @package app\server\classes
*/
class Config {

    /**
    * The host name of the database server.
    * 
    * @var string
    */
    private $host;

    /**
    * The name of the database.
    * 
    * @var string
    */
    private $db;

    /**
    * The user name for accessing the database.
    * 
    * @var string
    */
    private $user;

    /**
    * The password for accessing the database.
    * 
    * @var string
    */
    private $password;

    /**
    * Constructor for the Config class.
    * 
    * @param string $host The host name of the database server.
    * @param string $db The name of the database.
    * @param string $user The user name for accessing the database.
    * @param string $password The password for accessing the database.
    */
    public function __construct($host, $db, $user, $password) {
        $this->host = $host;
        $this->db = $db;
        $this->user = $user;
        $this->password = $password;
    }

    /**
    * Get the host name of the database server.
    * 
    * @return string The host name.
    */
    public function getHost() {
        return $this->host;
    }

    /**
    * Get the application's database name.
    * 
    * @return string The database name.
    */
    public function getDb() {
        return $this->db;
    }

    /**
    * Get the user of the database server.
    * 
    * @return string The user.
    */
    public function getUser() {
        return $this->user;
    }

    /**
    * Get the password of the database server.
    * 
    * @return string The password.
    */
    public function getPassword() {
        return $this->password;
    }
}
