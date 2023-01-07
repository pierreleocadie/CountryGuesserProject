<?php

namespace CountryGuesser\Models\Game;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Models\Database\DatabaseConnection;
use PDO;

/**
    GetGameData class
    Class to retrieve game data from the database.
*/
class GetGameData
{
    private object $databaseConnect;
    private object $db;

    /**
        Constructor for the GetGameData class.
        Connects to the database and creates a PDO connection.
    */
    public function __construct()
    {   
        $this->databaseConnect = new DatabaseConnection();
        $this->db = $this->databaseConnect->getDbConnection();
    }

    /**
        Retrieves game data for a given game ID.
        
        @param int $gameId The ID of the game to retrieve data for.
        @return string A JSON-encoded string containing the game data.
    */
    public function getGameData(int $gameId)
    {   
        $query = $this->db->prepare("SELECT * FROM playersGames 
                                    JOIN playersGamesRounds 
                                    ON playersGames.game_id = playersGamesRounds.game_id 
                                    JOIN playersGamesRoundsData 
                                    ON playersGamesRounds.game_id = playersGamesRoundsData.game_id 
                                    WHERE playersGames.game_id = :game_id 
                                    AND playersGamesRounds.round_id = playersGamesRoundsData.round_id");
        $query->execute([
            "game_id" => $gameId
        ]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($result);
    }
}

/* $game = new GetGameData();
echo $game->getGameData(55); */