<?php

namespace CountryGuesser\Models\Game;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Models\Database\DatabaseConnection;
use PDO;

/**
    CreateRound class
    This class contains a method for creating a new round in the database.
*/
class CreateRound
{
    private object $databaseConnect;
    private object $db;

    /**
        Constructor for the CreateRound class
        This method creates a new DatabaseConnection object and gets the database connection.
    */
    public function __construct()
    {   
        $this->databaseConnect = new DatabaseConnection();
        $this->db = $this->databaseConnect->getDbConnection();
    }
    
    /**
        createRound method
        This method creates a new round in the database.
        
        @param int $gameId The ID of the game the round belongs to
        @param int $roundId The ID of the round
        @param string $response The player's response to the round
    */
    public function createRound(int $gameId, int $roundId, string $response)
    {
        $query = $this->db->prepare("INSERT INTO playersGamesRounds (game_id, round_id, response) VALUES (:game_id, :round_id, :response)");
        $query->execute([
            "game_id" => $gameId,
            "round_id" => $roundId,
            "response" => $response
        ]);
    }

}