<?php

namespace CountryGuesser\Models\Game;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Models\Database\DatabaseConnection;
use PDO;

/**
    DeleteGame class
    This class is used to delete a game and all of its data from the database.
*/
class DeleteGame
{
    private object $databaseConnect;
    private object $db;

    /**
        Constructor for the DeleteGame class.
        This method creates a new DatabaseConnection object and gets the database connection.

        @param int $gameId The ID of the game to delete.
    */
    public function __construct(int $gameId)
    {   
        $this->databaseConnect = new DatabaseConnection();
        $this->db = $this->databaseConnect->getDbConnection();
        $this->deleteGame($gameId);
    }

    /**
        deleteGame method
        Deletes the game data from the playersGamesRounds table.

        @param int $gameId The ID of the game to delete.
        @return void
    */
    private function deleteGame(int $gameId): void
    {
        $query = $this->db->prepare("DELETE FROM playersGamesRounds WHERE game_id = :game_id");
        $query->execute([
            "game_id" => $gameId
        ]);
    }
}