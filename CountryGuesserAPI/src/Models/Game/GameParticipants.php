<?php

namespace CountryGuesser\Models\Game;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Models\Database\DatabaseConnection;
use PDO;

/**
    GameParticipants class
    This class provides a method to add a player to a game.
*/
class GameParticipants
{

    private object $databaseConnect;
    private object $db;

    /**
        Constructor for the GameParticipants class.
        This method creates a new DatabaseConnection object and gets the database connection.
    */
    public function __construct()
    {
        $this->databaseConnect = new DatabaseConnection();
        $this->db = $this->databaseConnect->getDbConnection();
    }

    /**
        addGameParticipant method
        Adds a player to a game into the database.

        @param int $gameId The ID of the game to add the player to.
        @param int $playerId The ID of the player to add to the game.
        @return void
    */
    public function addGameParticipant(int $gameId, int $playerId): void
    {
        $query = $this->db->prepare("INSERT INTO playersGamesParticipants (game_id, player_id) VALUES (:game_id, :player_id)");
        $query->execute([
            "game_id" => $gameId,
            "player_id" => $playerId
        ]);
    }

}