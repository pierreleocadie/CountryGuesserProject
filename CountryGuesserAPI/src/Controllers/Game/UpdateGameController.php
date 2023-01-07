<?php 

namespace CountryGuesser\Controllers;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Lib\RequestDataCompliance;
use CountryGuesser\Models\Game\UpdateGame;

/*

    Steps to update a game when the game is finished to update the winner and the number of rounds played into the database:
    - Recover data from request
    - Check if the data is in JSON format -> If not, we send an error
    - Check if the data correspond to what is expected
        - If YES :
            - We update the game
        - If NO :
            - We send an error

*/

$userData = file_get_contents("php://input");
if($userData && RequestDataCompliance::checkIfItsJSON($userData))
{
    $userData = json_decode($userData, true);
    if(RequestDataCompliance::UpdateGameDataCompliance($userData)){
        $game = new UpdateGame();
        $game->updateGame($userData["game_id"]);
    }else{
        echo json_encode(["error" => "Invalid data", "details" => "game_id -> int"]);
    }
}else{
    echo json_encode(["error" => "No data or invalid format", "details" => "Must be JSON format"]);
}