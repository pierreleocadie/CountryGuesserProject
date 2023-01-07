<?php 

namespace CountryGuesser\Controllers;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Lib\RequestDataCompliance;
use CountryGuesser\Models\Game\DeleteGame;

/*
    Steps to delete a game:
    - Recover data from request
    - Check if the data is in JSON format -> If not, we send an error
    - Check if the data correspond to what is expected
        - If YES :
            - We delete the game and all the data associated with it in the database
        - If NO :
            - We send an error
*/

$userData = file_get_contents("php://input");
if($userData && RequestDataCompliance::checkIfItsJSON($userData))
{
    $userData = json_decode($userData, true);
    if(RequestDataCompliance::DeleteGameDataCompliance($userData)){
        new DeleteGame($userData["game_id"]);
    }else{
        echo json_encode(["error" => "Invalid data", "details" => "game_id -> int"]);
    }
}else{
    echo json_encode(["error" => "No data or invalid format", "details" => "Must be JSON format"]);
}