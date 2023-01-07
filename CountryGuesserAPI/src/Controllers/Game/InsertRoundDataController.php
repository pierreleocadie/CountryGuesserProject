<?php 

namespace CountryGuesser\Controllers;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Lib\RequestDataCompliance;
use CountryGuesser\Lib\SecureData;
use CountryGuesser\Models\Game\InsertRoundData;

/*

    Steps to insert a round data:
    - Recover data from request
    - Check if the data is in JSON format -> If not, we send an error
    - Check if the data correspond to what is expected
        - If YES :
            - We insert the round data in the database
        - If NO :
            - We send an error

*/

$userData = file_get_contents("php://input");
if($userData && RequestDataCompliance::checkIfItsJSON($userData))
{
    $userData = json_decode($userData, true);
    if(RequestDataCompliance::InsertRoundDataDataCompliance($userData)){
        $game = new InsertRoundData();
        $game->insertRoundData($userData["game_id"], $userData["round_id"], $userData["player_id"], SecureData::secureStringData($userData["player_response"]));
    }else{
        echo json_encode(["error" => "Invalid data", "details" => "game_id -> int, round_id => int, player_id -> int, player_response -> string"]);
    }
}else{
    echo json_encode(["error" => "No data or invalid format", "details" => "Must be JSON format"]);
}
