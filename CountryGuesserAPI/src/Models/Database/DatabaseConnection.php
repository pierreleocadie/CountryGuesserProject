<?php

namespace CountryGuesser\Models\Database;

require_once __DIR__ . "/../../../vendor/autoload.php";

use PDO;

/**
    DatabaseConnection class
    This class handles the connection to the database.
    It has a method for getting the database connection.
*/
class DatabaseConnection
{
    private object $db;
    private string $HOST = "HOST";
    private string $USER = "USER";
    private string $PASSWORD = "PASSWORD";
    private string $DATABASE = "CountryGuesserDB";
    private string $ENCODING = "utf8";

    /**
        Constructor for the DatabaseConnection class
        This method creates a new PDO object for connecting to the database.
    */
    public function __construct()
    {
        $this->db = new PDO("mysql:host=" . $this->HOST . ";dbname=" . $this->DATABASE . ";charset=" . $this->ENCODING, $this->USER, $this->PASSWORD);
    }

    /**
        getDbConnection method
        This method returns the database connection.
        
        @return object The database connection
    */
    public function getDbConnection(): object
    {
        return $this->db;
    }
}

/* $dbConnect = new DatabaseConnection();
$db = $dbConnect->getDbConnection(); */