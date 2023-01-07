<?php 

namespace CountryGuesser\Controllers;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Lib\RequestDataCompliance;
use CountryGuesser\Models\Player\Leaderboard;

/*

    Steps to update the leaderboard (we update the leaderboard at the end of each game)):
    - Recover data from request
    - Check if the data is in JSON format -> If not, we send an error
    - Check if the data correspond to what is expected
        - If YES :
            - We update the leaderboard
        - If NO :
            - We send an error

*/

$userData = file_get_contents("php://input");
if($userData && RequestDataCompliance::checkIfItsJSON($userData))
{
    $userData = json_decode($userData, true);
    if(RequestDataCompliance::UpdateLeaderboardDataCompliance($userData)){
        $leaderboard = new Leaderboard();
        $leaderboard->updateLeaderboard($userData["player_id"]);
    }else{
        echo json_encode(["error" => "Invalid data", "details" => "player_id -> int"]);
    }
}else{
    echo json_encode(["error" => "No data or invalid format", "details" => "Must be JSON format"]);
}