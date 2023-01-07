<?php


class Queue
{
    /*
        Queue structure:
        $queue = [
            "playerCredential" => [
                "connection" => "connection",
                "roomSize" => "roomSize",
            ],
        ];
    */
    private static array $queue = [];

    public function getQueue(): array
    {
        return self::$queue;
    }

    // To check if a player is in the queue
    public function isPlayerInQueueWithCredential($playerCredential)
    {
        if (array_key_exists($playerCredential, self::$queue)) {
            // return the player credential and the player connection
            return self::$queue[$playerCredential];
        } else {
            return False;
        }
    }

    // To check if it has at least one player in the queue
    public function isPlayerInQueue(): bool
    {
        if (count(self::$queue) > 0) {
            return True;
        } else {
            return False;
        }
    }

    // To push a player in the queue
    public function pushPlayerInQueue($playerCredential, $playerConnection, $roomSize): void
    {
        self::$queue[$playerCredential]["connection"] = $playerConnection;
        self::$queue[$playerCredential]["roomSize"] = $roomSize;
    }

    // To check if the number of players in the queue waiting for a room with a same specific room size is equal to the room size they want
    public function arePlayersInQueueWithSameRoomSize($roomSize)
    {
        $playersWithSameSpecificRoomSize = [];
        foreach (self::$queue as $playerCredential => $player) {
            if ($player["roomSize"] === $roomSize) {
                array_push($playersWithSameSpecificRoomSize, $playerCredential);
            }
            if (count($playersWithSameSpecificRoomSize) === $roomSize-1) {
                return $playersWithSameSpecificRoomSize;
            }
        }
        return False;
    }

    // Create a function to remove a player from the queue
    public function removePlayerFromQueue($playerCredential): void
    {
        unset(self::$queue[$playerCredential]);
    }

}