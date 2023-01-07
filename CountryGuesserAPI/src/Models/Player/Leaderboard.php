<?php

namespace CountryGuesser\Models\Player;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Models\Database\DatabaseConnection;
use PDO;

/**
    Leaderboard class
    Class to manage the leaderboard
*/
class Leaderboard
{
    
    private object $databaseConnect;
    private object $db;

    /**
        Constructor for the Leaderboard class.
        This method creates a new DatabaseConnection object and gets the database connection.
    */
    public function __construct()
    {
        $this->databaseConnect = new DatabaseConnection();
        $this->db = $this->databaseConnect->getDbConnection();
    }

    /**
        getGamesWon method
        Gets the number of games won by a player.
    
        @param int $playerId The ID of the player to get games won for.
        @return int The number of games won by the player.
     */
    private function getGamesWon(int $playerId): int
    {
        $query = $this->db->prepare("SELECT COUNT(*) FROM playersGames WHERE winner_id = :player_id");
        $query->execute([
            "player_id" => $playerId
        ]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result["COUNT(*)"];
    }

    /**
        getGamesPlayed method
        Gets the number of games played by a player.

        @param int $playerId The ID of the player to get games played for.
        @return int The number of games played by the player.
    */
    private function getGamesPlayed(int $playerId): int
    {
        $query = $this->db->prepare("SELECT COUNT(*) FROM playersGamesParticipants WHERE player_id = :player_id");
        $query->execute([
            "player_id" => $playerId
        ]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result["COUNT(*)"];
    }

    /**
        Checks if a player is in the leaderboard.

        @param int $playerId The ID of the player to check for.
        @return bool True if the player is in the leaderboard, false otherwise.
    */
    private function checkIfPlayerIsInLeaderboard(int $playerId): bool
    {
        $query = $this->db->prepare("SELECT COUNT(*) FROM playersLeaderboard WHERE player_id = :player_id");
        $query->execute([
            "player_id" => $playerId
        ]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result["COUNT(*)"] > 0;
    }

    /**
        updatePlayerStats method
        Updates the stats of a player in the leaderboard.

        @param int $playerId The ID of the player to update the stats for.
        @return void
    */
    private function updatePlayerStats(int $playerId): void
    {
        $query = $this->db->prepare("UPDATE playersLeaderboard SET games_won = :games_won, games_played = :games_played WHERE player_id = :player_id");
        $query->execute([
            "games_won" => $this->getGamesWon($playerId),
            "games_played" => $this->getGamesPlayed($playerId),
            "player_id" => $playerId
        ]);
    }

    /**
        addPlayerToLeaderboard method
        Adds a player to the leaderboard.

        @param int $playerId The ID of the player to add to the leaderboard.
        @return void
    */
    private function addPlayerToLeaderboard(int $playerId): void
    {
        $query = $this->db->prepare("INSERT INTO playersLeaderboard (player_id, games_won, games_played) VALUES (:player_id, :games_won, :games_played)");
        $query->execute([
            "player_id" => $playerId,
            "games_won" => $this->getGamesWon($playerId),
            "games_played" => $this->getGamesPlayed($playerId)
        ]);
    }

    /**
        updateLeaderboard method
        Updates the leaderboard.

        @param int $playerId The ID of the player to update the leaderboard for.
        @return void
    */
    public function updateLeaderboard(int $playerId): void
    {
        if ($this->checkIfPlayerIsInLeaderboard($playerId)) {
            $this->updatePlayerStats($playerId);
        } else {
            $this->addPlayerToLeaderboard($playerId);
        }
    }

    /**
        getLeaderboard method
        Gets the leaderboard.

        @return string The leaderboard in JSON format.
    */
    public function getLeaderboard(): string
    {
        // Get the leaderdboard and associate the player ID with the player nickname
        $query = $this->db->prepare("SELECT playersLeaderboard.player_id, playersLeaderboard.games_won, playersLeaderboard.games_played, players.nickname FROM playersLeaderboard INNER JOIN players ON playersLeaderboard.player_id = players.player_id ORDER BY games_won DESC");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($result);
    }

    /**
        getLeaderboardPosition method
        Gets the position of a player in the leaderboard.

        @param int $playerId The ID of the player to get the position for.
        @return int The position of the player in the leaderboard.
    */
    public function getLeaderboardPosition(int $playerId): int
    {
        $query = $this->db->prepare("SELECT COUNT(*) FROM playersLeaderboard WHERE games_won > (SELECT games_won FROM playersLeaderboard WHERE player_id = :player_id)");
        $query->execute([
            "player_id" => $playerId
        ]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result["COUNT(*)"] + 1;
    }

    /**
        getLeaderboardStats method
        Gets the stats of a player in the leaderboard.

        @param int $playerId The ID of the player to get the stats for.
        @return string The stats of the player in the leaderboard in JSON format.
    */
    public function getLeaderboardStats(int $playerId): string
    {
        $query = $this->db->prepare("SELECT games_won, games_played FROM playersLeaderboard WHERE player_id = :player_id");
        $query->execute([
            "player_id" => $playerId
        ]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return json_encode($result);
    }
}