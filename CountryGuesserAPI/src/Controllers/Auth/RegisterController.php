<?php

namespace CountryGuesser\Controllers\Auth;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Models\Auth\Register;
use CountryGuesser\Lib\RequestDataCompliance;

/*

    Steps to register:
    - Recover data from request
    - Check if the data is in JSON format -> If not, we send an error
    - Check if the data correspond to what is expected
        - If YES :
            - We register the user in the database and send him his data in return to connect him directly
            Register -> Login
        - If NO :
            - We send an error

*/

$userData = file_get_contents("php://input");
if($userData && RequestDataCompliance::checkIfItsJSON($userData))
{
    $userData = json_decode($userData, true);
    if(RequestDataCompliance::RegisterDataCompliance($userData)){
        $getRegisteredUserData = new Register($userData);
        echo $getRegisteredUserData->getRegisteredUserData();
    }else{
        echo json_encode(["error" => "Invalid data", "details" => "nickname -> string, email -> string, password -> string, password_confirmation -> string"]);
    }
}else{
    echo json_encode(["error" => "No data or invalid format", "details" => "Must be JSON format"]);
}