<?php

use Workerman\Worker;

require_once __DIR__ . "/../vendor/autoload.php";
require "Rooms.php";
require "Queue.php";
require "API.php";

$rooms = new Rooms();
$queue = new Queue();
$api = new API();

/*
TYPE OF MESSAGES SERVER CAN SEND :
- INFORMATION
    - inQueue : the player is in the queue
    - roomFound : the player has found a room
    - roomCreated : the room has been created
    - roomFull : the room is full
    - roomDeleted : the room has been deleted
    - gameCreated : the game has been created
    - removedFromQueue : the player has been removed from the queue
    - removedFromRoom : the player has been removed from the room

- DATA
    - roomId : the room id
    - roomSize : the room size
    - gameId : the game id
    - roundId : the round id

- ERROR
    - aPlayerLeft : a player has left the room

*/

$ws_worker = new Worker("websocket://0.0.0.0:7777");

// 1 processes
$ws_worker->count = 1;

/*
    GAME MANAGEMENT
*/

// Emitted when new connection come
$ws_worker->onConnect = function ($connection) {
    $connection->onWebSocketConnect = function($connection)
    {   
        global $rooms;
        global $queue;
        global $api;

        $currentPlayerCredential = $_GET["playerCredential"];
        $roomSize = $_GET["roomSize"];
        $maxRounds = $_GET["maxRounds"];

        if(empty($roomSize) || intval($roomSize) < 1){
            echo "\nRoom size is empty\n";
            $connection->close();
        }

        if(empty($maxRounds) || intval($maxRounds) < 1 || intval($maxRounds) % intval($roomSize) !== 1){
            echo "Max rounds is empty or max rounds modulo room size is not equal to 1 or max rounds is < 1\n";
            $connection->close();
        }

        if(empty($currentPlayerCredential) || $currentPlayerCredential === "undefined" || $currentPlayerCredential === "null"){
            echo "Player credential is empty\n";
            $connection->close();
        }

        if(!$api->getPlayerData($currentPlayerCredential)){
            echo "Player credential does not exist\n";
            $connection->close();
        }else{
            $roomId = $rooms->searchRoom($roomSize);
            $connection->playerCredential = $currentPlayerCredential;
            $connection->roomSize = $roomSize;
            $connection->roomId = $roomId;

            $arePlayersInQueueWithSameRoomSize = $queue->arePlayersInQueueWithSameRoomSize($roomSize);

            if($arePlayersInQueueWithSameRoomSize){
                // Create a new room
                $roomId = $rooms->createRoom($roomSize, $maxRounds);

                // Add the room id to the connection of the player
                $connection->roomId = $roomId;

                // Add the current player to the room
                $rooms->addPlayerToRoom($roomId, $currentPlayerCredential, $connection);

                // Move all players we found in the queue with this specific room size to the room we just created
                foreach($arePlayersInQueueWithSameRoomSize as $playerCredential){
                    // Select the connection of the player we found in the queue
                    $playerConnection = $queue->getQueue()[$playerCredential]["connection"];

                    // Add the room id to the connection of the player we found in the queue
                    $playerConnection->roomId = $roomId;

                    // Add the player we found in the queue to the room
                    $rooms->addPlayerToRoom($roomId, $playerCredential, $playerConnection);

                    // Remove the player we found in the queue from the queue
                    $queue->removePlayerFromQueue($playerCredential);
                }

                // Check if the room is full
                if ($rooms->isRoomFull($roomId)) {
                    // Create a game
                    $rooms->setGameId($roomId, $api->createGame());

                    // Create a round
                    $rooms->createRound($roomId);

                    // Add the players to the game participants
                    foreach($rooms->getPlayersInRoom($roomId) as $playerCredential => $playerConnection){
                        $api->addGameParticipant($rooms->getGameId($roomId), $api->getPlayerData($playerCredential)["player_id"]);
                    }

                    // Send the room id to the players
                    $rooms->sendToRoom($roomId, json_encode(array("type" => "information", "informationType" => "roomCreated", "message" => "Room created")));
                    $rooms->sendToRoom($roomId, json_encode(array("type" => "information", "informationType" => "roomFull", "message" => "Room full")));
                    $rooms->sendToRoom($roomId, json_encode(array("type" => "information", "informationType" => "gameCreated", "message" => "Game created")));
                    $rooms->sendToRoom($roomId, json_encode(array("type" => "data", "roomId" => $roomId, "gameId" => $rooms->getGameId($roomId), "roomSize" => $roomSize)));
                }else{
                    $rooms->sendToRoom($roomId, json_encode(array("type" => "information", "informationType" => "roomCreated", "message" => "Room created")));
                    $rooms->sendToRoom($roomId, json_encode(array("type" => "information", "informationType" => "waitingPlayers", "message" => "Waiting for other players")));
                    $rooms->sendToRoom($roomId, json_encode(array("type" => "data", "roomId" => $roomId, "roomSize" => $roomSize)));
                }
            }else if($roomId){
                // We found a room

                // Add the room id to the connection of the player
                $connection->roomId = $roomId;

                // Add the player to the room
                $rooms->addPlayerToRoom($roomId, $currentPlayerCredential, $connection);
                if ($rooms->isRoomFull($roomId)) {
                    // Create a game
                    $rooms->setGameId($roomId, $api->createGame());

                    // Create a round
                    $rooms->createRound($roomId);

                    // Add the players to the game participants
                    foreach($rooms->getPlayersInRoom($roomId) as $playerCredential => $playerConnection){
                        $api->addGameParticipant($rooms->getGameId($roomId), $api->getPlayerData($playerCredential)["player_id"]);
                    }

                    // Send the room id to the players
                    $rooms->sendToRoom($roomId, json_encode(array("type" => "information", "informationType" => "roomFound", "message" => "Room found")));
                    $rooms->sendToRoom($roomId, json_encode(array("type" => "information", "informationType" => "roomFull", "message" => "Room full")));
                    $rooms->sendToRoom($roomId, json_encode(array("type" => "information", "informationType" => "gameCreated", "message" => "Game created")));
                    $rooms->sendToRoom($roomId, json_encode(array("type" => "data", "roomId" => $roomId, "gameId" => $rooms->getGameId($roomId), "roomSize" => $roomSize)));
                }else{
                    $rooms->sendToRoom($roomId, json_encode(array("type" => "information", "informationType" => "roomFound", "message" => "Room found")));
                    $rooms->sendToRoom($roomId, json_encode(array("type" => "information", "informationType" => "waitingPlayers", "message" => "Waiting for other players")));
                    $rooms->sendToRoom($roomId, json_encode(array("type" => "data", "roomId" => $roomId, "roomSize" => $roomSize)));
                }
            }

            if (!$roomId && !$queue->arePlayersInQueueWithSameRoomSize($roomSize)){
                // push the player in the queue
                $queue->pushPlayerInQueue($currentPlayerCredential, $connection, $roomSize);
                // Send the player a message to wait
                $connection->send(json_encode(array("type" => "information", "informationType" => "inQueue", "message" => "Waiting for a room to join")));        
            }
            
            // Send the room id to the player
            echo "\n\n\n################\n\n" . date('l d m Y h:i:s') . "\n\nQUEUE : \n";
            for ($i=0; $i < count($queue->getQueue()); $i++) { 
                echo "ROOM SIZE : " . $queue->getQueue()[array_keys($queue->getQueue())[$i]]["roomSize"] . " - PLAYER CREDENTIAL : " . array_keys($queue->getQueue())[$i] . "\n";
            }
        
            echo "\n\nROOMS : \n";
            for($i=0; $i < count($rooms->getRooms()); $i++){
                echo "ROOM ID : " . array_keys($rooms->getRooms())[$i] . " - ROOM SIZE : " . $rooms->getRooms()[array_keys($rooms->getRooms())[$i]]["roomSize"] . " - GAME ID : " . $rooms->getRooms()[array_keys($rooms->getRooms())[$i]]["gameId"] . " - PLAYERS : ";
                for($j=0; $j < count($rooms->getRooms()[array_keys($rooms->getRooms())[$i]]["players"]); $j++){
                    echo array_keys($rooms->getRooms()[array_keys($rooms->getRooms())[$i]]["players"])[$j] . " ";
                }
                echo "\n";
            }
            echo "\n################\n\n\n";
        }

    };
    
};

$ws_worker->onMessage = function($connection, $message)
{
    global $queue;
    global $rooms;
    global $api;

    $roomId = $connection->roomId;
    $messageData = json_decode($message, true);
    if($messageData["type"] == "cancelMultiplayerGame"){

        /* 
            When the player send a cancelMultiplayerGame message, we have theses cases :
            - The player is in the queue
            - The player is in a room
                In this case we have to inform the other players that the player has left the room
                we have to remove other players from the room too and delete the room
            - The player is not in the queue and not in a room
                In this case we will consider that the player is trying to cheat
                so... we will kick him from the server
        */

        // CASE 1 : The player is in the queue
        if($queue->isPlayerInQueueWithCredential($connection->playerCredential)){
            // Remove the player from the queue
            $queue->removePlayerFromQueue($connection->playerCredential);
            // Send a message to the player to inform him that he has been removed from the queue
            $connection->send(json_encode(array("type" => "information", "informationType" => "removedFromQueue", "message" => "You have been removed from the queue")));
        }


        // CASE 2 : The player is in a room
        if($rooms->isPlayerInRoom($connection->playerCredential)){
            // Send a message to the player to inform him that he has been removed from the room
            $connection->send(json_encode(array("type" => "information", "informationType" => "removedFromRoom", "message" => "You have been removed from the room")));
            
            // Remove the player from the room
            $rooms->removePlayerFromRoom($connection->roomId, $connection->playerCredential);
            
            // if the room is not empty send a message to the other players and remove the room
            $rooms->sendToRoom($roomId, json_encode(array("type" => "error", "errorType" => "aPlayerLeft", "message" => "A player has left the room")));
            
            // If a player left a room, delete all data about the game in the database
            $api->deleteGame($rooms->getGameId($roomId));

            // close the connections for the players in the room
            $rooms->closeConnectionForAllPlayersInRoom($roomId);
            $rooms->deleteRoom($roomId);

            
            // DEBUG MESSAGE
            echo "################\n\n\nPLAYER REMOVED FROM ROOM\n\nNUMBER OF ROOMS : " . strval(count($rooms->getRooms())) . "\nPLAYER CREDENTIAL : $connection->playerCredential\nROOM ID : $connection->roomId\n\n\n################\n\n\n";
        }

        // CASE 3 : The player is not in the queue and not in a room
        if(!$queue->isPlayerInQueueWithCredential($connection->playerCredential) && !$rooms->isPlayerInRoom($connection->playerCredential)){
            // Kick the player from the server
            $connection->close();
        }


    }elseif(!empty($rooms->getRooms()[$roomId]["rounds"]) && $messageData["type"] == "roundData"){

        /*  
            When the player send a roundData message, we have to do theses things :
            - Check if the number of rounds is not exceeded the maximum number of rounds
            - Check if the round doesn't already exist
            A roundData message sent by the client is nothing else than the country to guess
        */

        // CASE : The number of rounds exceeded the maximum number of rounds
        if($rooms->getLastRoundId($roomId) > $rooms->getMaxRounds($roomId)){
            // Update the game in the database
            $api->updateGame($rooms->getGameId($roomId));

            // Get the game data
            $gameData = $api->getGameData($rooms->getGameId($roomId));

            // Send a message to the player to inform him that the game is finished
            $rooms->sendToRoom($roomId, json_encode(array("type" => "information", "informationType" => "gameOver", "message" => "Game over", "gameData" => $gameData, "gameWinnerNickname" => $api->getPlayerData($rooms->getWinner($roomId)["winner"])["nickname"], "gameWinnerScore" => $rooms->getWinner($roomId)["score"])));

            // Update the leaderboard
            foreach($rooms->getPlayersInRoom($roomId) as $playerCredential => $playerConnection){
                $api->updateLeaderboard($api->getPlayerData($playerCredential)["player_id"]);
            }

            // Close connections for all players in the room
            $rooms->closeConnectionForAllPlayersInRoom($roomId);

            // Delete the room
            $rooms->deleteRoom($roomId);
        }


        // CASE : The round doesn't already exist
        if(!$api->checkRound($rooms->getGameId($roomId), $rooms->getLastRoundId($roomId))){
            // Set the country to guess for the round
            $rooms->setCountryToGuess($roomId, $messageData["code"]);

            // Create the round in the database
            $api->createRound($rooms->getGameId($roomId), $rooms->getLastRoundId($roomId), $rooms->getCountryToGuess($roomId));
            
            // Send the round id to the players
            $rooms->sendToRoom($roomId, json_encode(array("type" => "information", "informationType" => "roundCreated", "message" => "Round created", "roundId" => $rooms->getLastRoundId($roomId), "name" => $messageData["name"], "code" => $messageData["code"], "flag" => $messageData["flag"], "latLng" => $messageData["latLng"])));

            // DEBUG MESSAGE
            echo "\n\n\nRoom ID : " . $roomId . "\nRoom size : " . $rooms->getRooms()[$roomId]["roomSize"] . "\nPlayers : " . count($rooms->getRooms()[$roomId]["players"]) . "\nRound created\nRound ID : " . $rooms->getLastRoundId($roomId) . "\nRound country to guess : " . $messageData["name"] . "\nCode country to guess : " . $messageData["code"] . "\n\n\n";
        }


        
    }elseif(!empty($rooms->getRooms()[$roomId]["rounds"]) && $messageData["type"] == "playerResponse"){

        /*  
            When the player send a playerResponse message, we have to do theses things :
            - Check if it's the right answer
            - If it's not the right anwser, insert the player response in the database and send a message to the player to inform him that he has the wrong answer
        */

        // Get the player data to get his player id to insert it in the database
        $playerData = $api->getPlayerData($connection->playerCredential);

        // Insert the player response in the database
        $api->insertRoundData($rooms->getGameId($roomId), $rooms->getLastRoundId($roomId), $playerData["player_id"], $messageData["playerResponse"]);

        if($messageData["playerResponse"] === $rooms->getCountryToGuess($roomId)){

            // Set the player as the round winner
            $rooms->setRoundWinner($roomId, $connection->playerCredential);

            // Set the other players as the round losers
            $rooms->setRoundLosers($roomId, $connection->playerCredential);

            // Send a message in the room to inform the players that the round is over with the country to guess and the next round id, the round winner nickname
            $rooms->sendToRoom($roomId, json_encode(array("type" => "information", "informationType" => "roundOver", "message" => "Round over", "roundCountryToGuess" => $rooms->getCountryToGuess($roomId), "nextRoundId" => $rooms->getNextRoundId($roomId), "roundWinnerNickname" => $playerData["nickname"])));

            if($rooms->getLastRoundId($roomId) == $rooms->getMaxRounds($roomId)){
                // Update the game in the database
                $api->updateGame($rooms->getGameId($roomId));

                // Get the game data
                $gameData = $api->getGameData($rooms->getGameId($roomId));

                // Send a message to the player to inform him that the game is finished
                $rooms->sendToRoom($roomId, json_encode(array("type" => "information", "informationType" => "gameOver", "message" => "Game over", "gameData" => $gameData, "gameWinnerNickname" => $api->getPlayerData($rooms->getWinner($roomId)["winner"])["nickname"], "gameWinnerScore" => $rooms->getWinner($roomId)["score"])));

                // Update the leaderboard
                foreach($rooms->getPlayersInRoom($roomId) as $playerCredential => $playerConnection){
                    $api->updateLeaderboard($api->getPlayerData($playerCredential)["player_id"]);
                }

                // Close connections for all players in the room
                $rooms->closeConnectionForAllPlayersInRoom($roomId);

                // Delete the room
                $rooms->deleteRoom($roomId);
            }

            // Create a new round
            $rooms->createRound($roomId);
        }else{
            // If the player has the wrong answer send a message to the player to inform him that he has the wrong answer
            $connection->send(json_encode(array("type" => "information", "informationType" => "wrongAnswer", "message" => "Wrong answer")));
        }

    }
};

// Emitted when connection closed
$ws_worker->onClose = function ($connection){
    global $rooms;
    global $queue;
    global $api;
    if(isset($connection->playerCredential)){
        // If the player is in the queue remove him from the queue
        if($queue->isPlayerInQueueWithCredential($connection->playerCredential)){
            // Send a message to the player to inform him that he has been removed from the queue
            echo "################\n\n\nPLAYER REMOVED FROM QUEUE\n\nPLAYER CREDENTIAL : $connection->playerCredential\n";
            $queue->removePlayerFromQueue($connection->playerCredential);
            echo "NUMBER OF PLAYERS IN QUEUE : " . strval(count($queue->getQueue())) . "\n\n\n################\n\n\n";
        }
        // If the player is in a room remove him from the room
        if($rooms->isPlayerInRoom($connection->playerCredential)){
            $roomId = $connection->roomId;
            // If last round id is inferior to max rounds it means that the game is not finished so we have to delete the game in the database
            if($rooms->getLastRoundId($roomId) < $rooms->getMaxRounds($roomId)){
                $api->deleteGame($rooms->getGameId($roomId));
            }
            echo "################\n\n\nPLAYER REMOVED FROM ROOM\n\nPLAYER CREDENTIAL : $connection->playerCredential\nROOM ID : $roomId";
            $rooms->removePlayerFromRoom($roomId, $connection->playerCredential);
            echo "\nNUMBER OF ROOMS : " . strval(count($rooms->getRooms())) . "\n\n\n################\n\n\n";
            // if the room is not empty send a message to the other players and remove the room
            $rooms->sendToRoom($connection->roomId, json_encode(array("type" => "error", "errorType" => "aPlayerLeft", "message" => "A player has left the room")));
            // close the connections for the players in the room
            $rooms->closeConnectionForAllPlayersInRoom($roomId);
            $rooms->deleteRoom($roomId);
            echo "################\n\n\nROOM REMOVED\n\nROOM ID : $roomId\n\n\n################\n\n\n";
        }
    }
};

// Run worker
Worker::runAll();