<?php

require_once "Rooms.php";
require_once "Queue.php";
require_once "API.php";

$rooms = new Rooms();
$queue = new Queue();
$api = new API();

function checkRound(int $gameId, int $roundId)
{
    $dataToSend = array(
        "game_id" => $gameId,
        "round_id" => $roundId
    );
    $dataToSend = json_encode($dataToSend);
    $curl = curl_init("https://api.countryguesser.deletesystem32.fr/game/round/check");
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
        return $data;
    }
    curl_close($curl);
}


function getLeaderboardStats(int $playerId)
{
    $dataToSend = array(
        "player_id" => $playerId
    );
    $dataToSend = json_encode($dataToSend);
    $curl = curl_init("https://api.countryguesser.deletesystem32.fr/player/getleaderboardstats");
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
        return $data;
    }
    curl_close($curl);
}

function deleteGame(int $gameId)
{
    $dataToSend = array(
        "game_id" => $gameId
    );
    $dataToSend = json_encode($dataToSend);
    $curl = curl_init("https://api.countryguesser.deletesystem32.fr/game/delete");
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

deleteGame(1);