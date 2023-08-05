<?php
class Config {
    private $host;
    private $db;
    private $user;
    private $password;

    public function __construct($host, $db, $user, $password) {
        $this->host = $host;
        $this->db = $db;
        $this->user = $user;
        $this->password = $password;
    }

    public function getHost() {
        return $this->host;
    }

    public function getDb() {
        return $this->db;
    }

    public function getUser() {
        return $this->user;
    }

    public function getPassword() {
        return $this->password;
    }
}
