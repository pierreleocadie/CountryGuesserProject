<?php

namespace CountryGuesser\Lib;

require_once __DIR__ . "/../../vendor/autoload.php";

use CountryGuesser\Models\DatabaseConnection;

/**
    CheckIfDataExist class
    This class checks if a value exists in a database table.

    @param PDO $db A PDO object for connecting to the database
*/
class CheckIfDataExist
{
    private object $db;

    /**
        Constructor for the CheckIfDataExist class

        @param PDO $db A PDO object for connecting to the database
    */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
        checkIfDataExist method
        This method checks if a value exists in a specified field in a database table.

        @param string $field The field to check in the database table
        @param string $dbTable The name of the database table
        @param string $value The value to search for in the field
        @return bool True if the value exists in the field, False if it does not
    */
    public function checkIfDataExist(string $field, string $dbTable, string $value): bool
    {
        $query = $this->db->prepare("SELECT * FROM $dbTable WHERE $field = :$field");
        $query->execute([
            $field => $value
        ]);
        $result = $query->fetch();
        return !! $result;
    }

}