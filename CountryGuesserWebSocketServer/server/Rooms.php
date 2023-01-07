<?php

class Rooms
{
    /*
        Rooms structure:
        $rooms = [
            "roomId" => [
                "roomSize" => "roomSize",
                "players" => [
                    "playerCredential" => "connection",
                ],
                "rounds" => [
                    "roundId" => [
                        "roundWinner" => "playerCredential",
                        "roundLosers" => [
                            "playerCredential",
                        ],
                        "roundCountryToGuess" => "countryName",
                    ],
                ],
                "maxRounds" => "maxRounds",
                "gameId" => "gameId",
            ],
        ];
    */
    private static array $rooms = [];

    public function getRooms(): array
    {
        return self::$rooms;
    }

    // To create a room
    public function createRoom(int $roomSize, int $maxRounds): string
    {   
        $roomId = uniqid();
        self::$rooms[$roomId]["roomSize"] = $roomSize;
        self::$rooms[$roomId]["players"] = array();
        self::$rooms[$roomId]["rounds"] = array();
        self::$rooms[$roomId]["maxRounds"] = $maxRounds;
        self::$rooms[$roomId]["gameId"] = NULL;
        return $roomId;
    }

    // To set gameId to a room
    public function setGameId(string $roomId, int $gameId): void
    {
        self::$rooms[$roomId]["gameId"] = $gameId;
    }

    // To create a round
    public function createRound(string $roomId): void
    {
        self::$rooms[$roomId]["rounds"][] = array(
            "roundId" => 1,
            "roundWinner" => NULL,
            "roundLosers" => array(),
            "roundCountryToGuess" => NULL,
        );
    }

    // To check if at least one room exists
    public function isRoomExist(): bool
    {
        if (count(self::$rooms) > 0) {
            return True;
        } else {
            return False;
        }
    }

    // To add a player to a room
    public function addPlayerToRoom(string $roomId, string $playerCredential, object $playerConnection): void
    {
        self::$rooms[$roomId]["players"][$playerCredential] = $playerConnection;
    }

    // To check if a room is full
    public function isRoomFull(string $roomId): bool
    {
        if (count(self::$rooms[$roomId]["players"]) >= self::$rooms[$roomId]["roomSize"]) {
            return True;
        } else {
            return False;
        }
    }

    // This function which will search for a room with a specific size to join
    public function searchRoom(int $roomSize): string | bool
    {
        foreach (self::$rooms as $roomId => $room) {
            if ($room["roomSize"] === $roomSize && !$this->isRoomFull($roomId)) {
                return $roomId;
            }
        }
        return False;
    }

    // To remove a player from a room
    public function removePlayerFromRoom(string $roomId, string $playerCredential): void
    {
        unset(self::$rooms[$roomId]["players"][$playerCredential]);
    }

    // To send a message to all players in a room
    public function sendToRoom(string $roomId, string $message): void
    {
        foreach (self::$rooms[$roomId]["players"] as $playerCredential => $playerConnection) {
            $playerConnection->send($message);
        }
    }

    // To check if a room is empty
    public function isRoomEmpty(string $roomId): bool
    {
        if (count(self::$rooms[$roomId]["players"]) === 0) {
            return True;
        } else {
            return False;
        }
    }

    // To check if a room exists
    public function isRoomExistWithId(string $roomId): bool
    {
        if (array_key_exists($roomId, self::$rooms)) {
            return True;
        } else {
            return False;
        }
    }

    // To check if a player is in which room he is
    public function isPlayerInRoom(string $playerCredential): string | bool
    {
        foreach (self::$rooms as $roomId => $room) {
            if (array_key_exists($playerCredential, $room["players"])) {
                return $roomId;
            }
        }
        return False;
    }

    // This function will return the winnner of the game
    public function getWinner(string $roomId): array
    {
        $playersScore = array();
        foreach (self::$rooms[$roomId]["rounds"] as $round){
            if (array_key_exists($round["roundWinner"], $playersScore)) {
                $playersScore[$round["roundWinner"]] += 1;
            } else {
                $playersScore[$round["roundWinner"]] = 1;
            }
        }
        $winner = array_keys($playersScore, max($playersScore));
        $winnerScore = max($playersScore);
        return array("winner" => $winner[0], "score" => $winnerScore);
    }

    // To delete a room
    public function deleteRoom(string $roomId): void
    {
        unset(self::$rooms[$roomId]);
    }

    // To set the country to guess in a round
    public function setCountryToGuess(string $roomId, string $countryName): void
    {
        self::$rooms[$roomId]["rounds"][count(self::$rooms[$roomId]["rounds"]) - 1]["roundCountryToGuess"] = $countryName;
    }

    // To close connection for all players in a room
    public function closeConnectionForAllPlayersInRoom(string $roomId): void
    {
        foreach (self::$rooms[$roomId]["players"] as $playerCredential => $playerConnection) {
            $playerConnection->close();
        }
    }

    // To get the country to guess in a round
    public function getCountryToGuess(string $roomId): string
    {
        return self::$rooms[$roomId]["rounds"][count(self::$rooms[$roomId]["rounds"]) - 1]["roundCountryToGuess"];
    }

    // To set the winner of a round
    public function setRoundWinner(string $roomId, string $playerCredential): void
    {
        self::$rooms[$roomId]["rounds"][count(self::$rooms[$roomId]["rounds"]) - 1]["roundWinner"] = $playerCredential;
    }

    // To get the game id of a room
    public function getGameId(string $roomId): int
    {
        return self::$rooms[$roomId]["gameId"];
    }

    // To get the last round id of a room
    public function getLastRoundId(string $roomId): int
    {
        return count(self::$rooms[$roomId]["rounds"]);
    }

    // Set round losers
    public function setRoundLosers(string $roomId, string $winnerCredential): void
    {
        foreach (self::$rooms[$roomId]["players"] as $playerCredential => $playerConnection) {
            if ($playerCredential !== $winnerCredential) {
                self::$rooms[$roomId]["rounds"][count(self::$rooms[$roomId]["rounds"]) - 1]["roundLosers"][] = $playerCredential;
            }
        }
    }

    // Get the next round id
    public function getNextRoundId(string $roomId): int
    {
        return count(self::$rooms[$roomId]["rounds"]) + 1;
    }

    // To get the max number of rounds 
    public function getMaxRounds(string $roomId): int
    {
        return self::$rooms[$roomId]["maxRounds"];
    }


    // To get players in a room
    public function getPlayersInRoom(string $roomId): array
    {
        return self::$rooms[$roomId]["players"];
    }
}