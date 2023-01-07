<?php

namespace CountryGuesser\Models\Player;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Models\Database\DatabaseConnection;
use PDO;

/**
    PlayerData class
    This class provides a method to get player data for a given identifier.
*/
class PlayerData
{

    private object $databaseConnect;
    private object $db;

    /**
        Constructor for the PlayerData class.
        This method creates a new DatabaseConnection object and gets the database connection.
    */
    public function __construct()
    {
        $this->databaseConnect = new DatabaseConnection();
        $this->db = $this->databaseConnect->getDbConnection();
    }

    /**
        getPlayerData method
        Gets player data for a given identifier.

        @param int|string $identifier The player identifier to search for. Can be the player ID, nickname, email, or credential.
        @return string A JSON string of player data for the given identifier.
    */
    public function getPlayerData(int | string $identifier)
    {
        $query = $this->db->prepare("SELECT player_id, nickname, email, credential FROM players WHERE player_id = :identifier OR nickname = :identifier OR email = :identifier OR credential = :identifier");
        $query->execute([
            "identifier" => $identifier
        ]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return json_encode($result);
    }
    
}
/* 
$dbConnect = new DatabaseConnection();
$db = $dbConnect->getDbConnection();
$playerData = new PlayerData($db);

echo var_dump(json_decode($playerData->getPlayerData('$2y$12$AcwDG9cpoRw0Sc8dmHniUOV98jzgLWAfnv7sR7ycFhGvUkUi2GQv'))); */
