<?php

namespace CountryGuesser\Models\Game;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Models\Database\DatabaseConnection;
use PDO;

/**
    CheckRound class
    This class contains a method for checking if a round exists in the database.
*/
class CheckRound
{
    private object $databaseConnect;
    private object $db;

    /**
        Constructor for the CheckRound class
        This method creates a new DatabaseConnection object and gets the database connection.
    */
    public function __construct()
    {   
        $this->databaseConnect = new DatabaseConnection();
        $this->db = $this->databaseConnect->getDbConnection();
    }

    /**
        checkRound method
        This method checks if a round exists in the database.

        @param int $gameId The ID of the game the round belongs to
        @param int $roundId The ID of the round
        @return string A JSON encoded string indicating if the round exists (true) or not (false)
    */
    public function checkRound($gameId, $roundId): string
    {
        $query = $this->db->prepare("SELECT * FROM playersGamesRounds WHERE game_id = :game_id AND round_id = :round_id");
        $query->execute([
            "game_id" => $gameId,
            "round_id" => $roundId
        ]);
        $round = $query->fetch(PDO::FETCH_ASSOC);
        return json_encode(!! $round);
    }
}

/* $game = new CheckRound();
echo $game->checkRound(30, 1); */