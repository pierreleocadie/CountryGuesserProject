<?php

namespace CountryGuesser\Models\Game;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Models\Database\DatabaseConnection;
use PDO;

/**
    UpdateGame class
    Class to update game data in the database.
*/
class UpdateGame
{
    private object $databaseConnect;
    private object $db;

    /**
        Constructor for the UpdateGame class.
        Creates a new DatabaseConnection object and gets the database connection.
    */
    public function __construct()
    {   
        $this->databaseConnect = new DatabaseConnection();
        $this->db = $this->databaseConnect->getDbConnection();
    }

    /** 
        updateGame method
        Updates game data in the database for a given game ID.

        @param int $gameId The ID of the game to update data for.
     */
    public function updateGame(int $gameId): void
    {
        // Query to retrieve round data for the given game ID.
        $query = $this->db->prepare("SELECT * FROM playersGamesRoundsData 
                                    JOIN playersGamesRounds 
                                    ON playersGamesRoundsData.game_id = playersGamesRounds.game_id 
                                    WHERE playersGamesRoundsData.game_id = :game_id 
                                    AND playersGamesRoundsData.round_id = playersGamesRounds.round_id");
        $query->execute([
            "game_id" => $gameId
        ]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        // Initialize variables to store game data
        $players = [];
        $rounds = [];
        $roundsResponses = [];
        $playersResponses = [];

        // Iterate over round data and store relevant information
        foreach ($result as $row) {
            $players[$row["player_id"]] = 0;
            $rounds[$row["round_id"]] = 0;
            $roundsResponses[$row["round_id"]] = $row["response"];
            $playersResponses[$row["player_id"]][$row["round_id"]] = $row["player_response"];
        }

        // Iterate over player responses and increment player score if their response matches the round response
        foreach ($playersResponses as $playerId => $playerResponses) {
            foreach ($playerResponses as $roundId => $playerResponse) {
                if ($playerResponse === $roundsResponses[$roundId]) {
                    $players[$playerId]++;
                }
            }
        }

        // Determine the winner and number of rounds played
        $winnerId = array_search(max($players), $players);
        $nbRounds = count($rounds);

        // Update game data in the database
        $query = $this->db->prepare("UPDATE playersGames SET winner_id = :winner_id, nb_rounds = :nb_rounds WHERE game_id = :game_id");
        $query->execute([
            "winner_id" => $winnerId,
            "nb_rounds" => $nbRounds,
            "game_id" => $gameId
        ]);

    }
}