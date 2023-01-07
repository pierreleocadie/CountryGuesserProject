<?php

namespace CountryGuesser\Models\Game;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Models\Database\DatabaseConnection;
use PDO;

/**
    InsertRoundData class
    To insert round data into the database.
*/
class InsertRoundData
{
    private object $databaseConnect;
    private object $db;

    /**
        Constructor for the InsertRoundData class.
        Connects to the database and creates a PDO connection.
    */
    public function __construct()
    {   
        $this->databaseConnect = new DatabaseConnection();
        $this->db = $this->databaseConnect->getDbConnection();
    }

    /**
        Inserts round data into the database.
    
        @param int $gameId The ID of the game the round data belongs to.
        @param int $roundId The ID of the round the data belongs to.
        @param int $playerId The ID of the player the data belongs to.
        @param string $playerResponse The response from the player for this round.
        @return void
     */
    public function insertRoundData(int $gameId, int $roundId, int $playerId, string $playerResponse): void
    {
        $query = $this->db->prepare("INSERT INTO playersGamesRoundsData (game_id, round_id, player_id, player_response) VALUES (:game_id, :round_id, :player_id, :player_response)");
        $query->execute([
            "game_id" => $gameId,
            "round_id" => $roundId,
            "player_id" => $playerId,
            "player_response" => $playerResponse
        ]);
    }
}