<?php

namespace CountryGuesser\Controllers\Auth;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Models\Auth\Login;
use CountryGuesser\Lib\RequestDataCompliance;

/*

    Steps to login:
    - Recover data from request
    - Check if the data is in JSON format -> If not, we send an error
    - Check if the data correspond to what is expected
        - If YES :
            - We send back the user's data to be stored in a front-end cookie
        - If NO :
            - We send an error

*/


$userData = file_get_contents("php://input");
if($userData && RequestDataCompliance::checkIfItsJSON($userData))
{
    $userData = json_decode($userData, true);
    if(RequestDataCompliance::LoginDataCompliance($userData))
    {
        $getLoginUserData = new Login($userData);
        echo $getLoginUserData->getLoginUserData();
    }else{
        echo json_encode(["error" => "Invalid data", "details" => "nickname_email -> string, password -> string"]);
    }
}else{
    echo json_encode(["error" => "No data or invalid format", "details" => "Must be JSON format"]);
}
