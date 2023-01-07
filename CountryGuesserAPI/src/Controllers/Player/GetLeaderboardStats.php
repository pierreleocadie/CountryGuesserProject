<?php 

namespace CountryGuesser\Controllers;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Lib\RequestDataCompliance;
use CountryGuesser\Models\Player\Leaderboard;

/*

    Steps to get the stats of a player in the leaderboard:
    - Recover data from request
    - Check if the data is in JSON format -> If not, we send an error
    - Check if the data correspond to what is expected
        - If YES :
            - We send back the stats of the player
        - If NO :
            - We send an error

*/

$userData = file_get_contents("php://input");
if($userData && RequestDataCompliance::checkIfItsJSON($userData))
{
    $userData = json_decode($userData, true);
    if(RequestDataCompliance::GetLeaderboardStatsDataCompliance($userData)){
        $leaderboard = new Leaderboard();
        echo $leaderboard->getLeaderboardStats($userData["player_id"]);
    }else{
        echo json_encode(["error" => "Invalid data", "details" => "player_id -> int"]);
    }
}else{
    echo json_encode(["error" => "No data or invalid format", "details" => "Must be JSON format"]);
}