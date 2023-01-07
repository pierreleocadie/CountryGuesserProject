<?php 

namespace CountryGuesser\Controllers;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Lib\RequestDataCompliance;
use CountryGuesser\Lib\SecureData;
use CountryGuesser\Models\Player\PlayerData;

/*

    Steps to get the data of a player:
    - Recover data from request
    - Check if the data is in JSON format -> If not, we send an error
    - Check if the data correspond to what is expected
        - If YES :
            - We send back the data of the player
        - If NO :
            - We send an error

*/

$userData = file_get_contents("php://input");
if($userData && RequestDataCompliance::checkIfItsJSON($userData))
{
    $userData = json_decode($userData, true);
    if(RequestDataCompliance::PlayerDataWithCredentialKeyDataComplicance($userData)){
        $player = new PlayerData();
        echo $player->getPlayerData(SecureData::secureStringData($userData["credential_key"]));
    }else{
        echo json_encode(["error" => "Invalid data", "details" => "credential_key -> string"]);
    }
}else{
    echo json_encode(["error" => "No data or invalid format", "details" => "Must be JSON format"]);
}