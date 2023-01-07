<?php

namespace CountryGuesser\Models\Auth;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Models\Database\DatabaseConnection;
use CountryGuesser\Lib\CheckIfDataExist;
use CountryGuesser\Lib\CheckIfDataValid;
use CountryGuesser\Lib\SecureData;
use CountryGuesser\Models\Auth\AuthDB;

/**
    Login class
    This class contains a method for logging a user in.

    @param array $userData An array containing the user's login data (nickname_email, password)
*/
class Login
{
    private object $db;
    private object $databaseConnect;
    private string $userData;

    /**
        Constructor for the Login class
        @param array $userData An array containing the user's login data (nickname_email, password)
    */
    public function __construct(array $userData)
    {
        $this->databaseConnect = new DatabaseConnection();
        $this->db = $this->databaseConnect->getDbConnection();
        $this->userData = $this->login($userData);
    }

    /**
        login method
        This method logs a user in by verifying their login credentials and returning their data.
        It also handles validation of the user data and any error messages.

        @param array $userData An array containing the user's login data (nickname_email, password)
        @return string A JSON encoded string containing the user's data or error messages
    */
    private function login(array $userData): string
    {
        $errors = [];

        if(CheckIfDataValid::checkIfArrayValuesNotEmpty($userData)){
            $userData = SecureData::secureArrayData($userData);
            $checkDataIfExist = new CheckIfDataExist($this->db);

            if(!$checkDataIfExist->checkIfDataExist("nickname", "players", $userData["nickname_email"]) && !$checkDataIfExist->checkIfDataExist("email", "players", $userData["nickname_email"])){
                $errors[] = "Nickname or email incorrect";
            }

            if(count($errors) == 0){
                $auth = new AuthDB($this->db);
                $playerPasswordHash = $auth->selectPlayerPasswordHash($userData["nickname_email"]);
                if(password_verify($userData["password"], $playerPasswordHash)){
                    $userData = $auth->login($userData["nickname_email"], $playerPasswordHash);
                    if(empty($userData) || $userData == null){
                        $errors[] = "Password is incorrect";
                    }else{
                        return json_encode($userData);
                    }
                }else{
                    $errors[] = "Password is incorrect";
                }
            }
        }else{
            $errors[] = "All fields are required";
        }
        return json_encode($errors);
    }
    
    /**
        getLoginUserData method
        This method returns the data of the logged in user.
        @return string The logged in user's data
    */
    public function getLoginUserData(): string
    {
        return $this->userData;
    }
}

/* $userData = ["nickname_email" => "test", "password" => "test"];
//$databaseConnect = new DatabaseConnection();
//$db = $databaseConnect->getDbConnection();
//$auth = new AuthDB($db);
//$auth->login($userData["nickname_email"], $userData["password"]);
$log = new Login($userData);
echo $log->getLoginUserData(); */