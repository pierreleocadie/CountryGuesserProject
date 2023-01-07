<?php

class API
{
    private static string $rootUrl = "https://api.countryguesser.deletesystem32.fr"; 

    public function createGame()
    {
        $curl = curl_init(self::$rootUrl . "/game/create");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        $data = curl_exec($curl);
        if($data === false)
        {
            return '\nCurl error: ' . curl_error($curl) . '\n';
        }
        else
        {
            $data = json_decode($data, true);
            return $data["game_id"];
        }
        curl_close($curl);
    }

    public function updateGame(int $gameId)
    {
        $dataToSend = array(
            "game_id" => $gameId
        );
        $dataToSend = json_encode($dataToSend);
        $curl = curl_init(self::$rootUrl . "/game/update");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataToSend);
        $data = curl_exec($curl);
        if($data === false)
        {
            echo '\nCurl error: ' . curl_error($curl) . '\n';
        }
        curl_close($curl);
    }

    public function createRound(int $gameId, int $roundId, string $response)
    {   
        $dataToSend = array(
            "game_id" => $gameId,
            "round_id" => $roundId,
            "response" => $response
        );
        $dataToSend = json_encode($dataToSend);
        $curl = curl_init(self::$rootUrl . "/game/round/create");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataToSend);
        $data = curl_exec($curl);
        if($data === false)
        {
            echo '\nCurl error: ' . curl_error($curl) . '\n';
        }
        curl_close($curl);
    }

    public function insertRoundData(int $gameId, int $roundId, int $playerId, string $playerResponse)
    {
        $dataToSend = array(
            "game_id" => $gameId,
            "round_id" => $roundId,
            "player_id" => $playerId,
            "player_response" => $playerResponse
        );
        $dataToSend = json_encode($dataToSend);
        $curl = curl_init(self::$rootUrl . "/game/round/playeranswer");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataToSend);
        $data = curl_exec($curl);
        if($data === false)
        {
            echo '\nCurl error: ' . curl_error($curl) . '\n';
        }
        curl_close($curl);
    }

    public function getGameData(int $gameId)
    {
        $dataToSend = array(
            "game_id" => $gameId
        );
        $dataToSend = json_encode($dataToSend);
        $curl = curl_init(self::$rootUrl . "/game/getgamedata");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataToSend);
        $data = curl_exec($curl);
        if($data === false)
        {
            echo '\nCurl error: ' . curl_error($curl) . '\n';
        }
        else
        {
            return json_decode($data, true);
        }
        curl_close($curl);
    }

    public function getPlayerData(string $credential)
    {
        $dataToSend = array(
            "credential_key" => $credential
        );
        $dataToSend = json_encode($dataToSend);
        $curl = curl_init(self::$rootUrl . "/player/playerdata");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataToSend);
        $data = curl_exec($curl);
        if($data === false)
        {
            echo '\nCurl error: ' . curl_error($curl) . '\n';
        }
        else
        {
            return json_decode($data, true);
        }
        curl_close($curl);
    }

    public function checkRound(int $gameId, int $roundId)
    {
        $dataToSend = array(
            "game_id" => $gameId,
            "round_id" => $roundId
        );
        $dataToSend = json_encode($dataToSend);
        $curl = curl_init(self::$rootUrl . "/game/round/check");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataToSend);
        $data = curl_exec($curl);
        if($data === false)
        {
            echo '\nCurl error: ' . curl_error($curl) . '\n';
        }
        else
        {
            return json_decode($data, true);
        }
        curl_close($curl);
    }

    public function updateLeaderboard(int $playerId)
    {
        $dataToSend = array(
            "player_id" => $playerId
        );
        $dataToSend = json_encode($dataToSend);
        $curl = curl_init(self::$rootUrl . "/player/updateleaderboard");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataToSend);
        $data = curl_exec($curl);
        if($data === false)
        {
            echo '\nCurl error: ' . curl_error($curl) . '\n';
        }
        else
        {
            return json_decode($data, true);
        }
        curl_close($curl);
    }

    public function deleteGame(int $gameId)
    {
        $dataToSend = array(
            "game_id" => $gameId
        );
        $dataToSend = json_encode($dataToSend);
        $curl = curl_init(self::$rootUrl . "/game/delete");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataToSend);
        $data = curl_exec($curl);
        if($data === false)
        {
            echo '\nCurl error: ' . curl_error($curl) . '\n';
        }
        else
        {
            return json_decode($data, true);
        }
        curl_close($curl);
    }

    public function addGameParticipant(int $gameId, int $playerId)
    {
        $dataToSend = array(
            "game_id" => $gameId,
            "player_id" => $playerId
        );
        $dataToSend = json_encode($dataToSend);
        $curl = curl_init($self::self::$rootUrlUrl . "/game/participants");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataToSend);
        $data = curl_exec($curl);
        if($data === false)
        {
            echo '\nCurl error: ' . curl_error($curl) . '\n';
        }
        else
        {
            return json_decode($data, true);
        }
        curl_close($curl);
    }

}

/* $api = new Api();
echo var_dump($api->getPlayerData('$2y$12$AcwDG9cpoRw0Sc8dmHniUOV98jzgLWAfnv7sR7ycFhGvUkUi2GQv')); */