<?php

namespace CountryGuesser\Models\Game;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Models\Database\DatabaseConnection;
use PDO;

/**
    CreateGame class
    This class contains a method for creating a new game in the database.
*/
class CreateGame
{
    private object $databaseConnect;
    private object $db;

    /**
        Constructor for the CreateGame class
        This method creates a new DatabaseConnection object and gets the database connection.
    */
    public function __construct()
    {   
        $this->databaseConnect = new DatabaseConnection();
        $this->db = $this->databaseConnect->getDbConnection();
    }

    /**
        createGame method
        This method creates a new game in the database and returns the ID of the game.
        
        @return string A JSON encoded string containing the ID of the game
    */
    public function createGame()
    {
        $query = $this->db->prepare("INSERT INTO playersGames (winner_id, nb_rounds) VALUES (NULL, NULL)");
        $query->execute();
        $gameId = $this->db->lastInsertId();
        return json_encode(["game_id" => intval($gameId)]);
    }

}