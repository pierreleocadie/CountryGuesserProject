<?php

namespace CountryGuesser\Models\Auth;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Models\Database\DatabaseConnection;
use CountryGuesser\Lib\CheckIfDataExist;
use CountryGuesser\Lib\CheckIfDataValid;
use CountryGuesser\Lib\SecureData;
use CountryGuesser\Models\Auth\AuthDB;

/**
    Register class
    This class contains a method for registering a new user.
    It also has a method for getting the registered user's data.

    @param array $userData An array containing the user's registration data (nickname, email, password, password_confirmation)
*/
class Register
{

    private object $db;
    private object $databaseConnect;
    private string $userData;

    /**
        Constructor for the Register class

        @param array $userData An array containing the user's registration data (nickname, email, password, password_confirmation)
    */
    public function __construct(array $userData)
    {
        $this->databaseConnect = new DatabaseConnection();
        $this->db = $this->databaseConnect->getDbConnection();
        $this->userData = $this->register($userData);
    }

    /**
        register method
        This method registers a new user in the database.
        It also checks for validation errors and returns them as a JSON encoded string.

        @param array $userData An array containing the user's registration data (nickname, email, password, password_confirmation)
        @return string A JSON encoded string containing any errors that occurred during the registration process, or the registered user's data if successful
    */
    private function register(array $userData)
    {
        $errors = [];

        if(CheckIfDataValid::checkIfArrayValuesNotEmpty($userData)){
            $userData = SecureData::secureArrayData($userData);
            
            if(!CheckIfDataValid::checkIfNicknameValid($userData["nickname"])){
                $errors[] = "Nickname must be between 3 and 20 characters long";
            }
            if(!CheckIfDataValid::checkIfEmailValid($userData["email"])){
                $errors[] = "Email is not valid";
            }
            if(!CheckIfDataValid::checkPasswordLengthValid($userData["password"])){
                $errors[] = "Password must be at least 8 characters long";
            }else{
                if(!CheckIfDataValid::checkIfPasswordValid($userData["password"], $userData["password_confirmation"])){
                    $errors[] = "Passwords do not match";
                }
            }

            if(count($errors) == 0){
                $checkDataIfExist = new CheckIfDataExist($this->db);

                if($checkDataIfExist->checkIfDataExist("nickname", "players", $userData["nickname"])){
                    $errors[] = "Nickname already exists";
                }
                if($checkDataIfExist->checkIfDataExist("email", "players", $userData["email"])){
                    $errors[] = "Email already exists";
                }
                if(count($errors) == 0){
                    $auth = new AuthDB($this->db);
                    $password = SecureData::securePassword($userData["password"]);
                    $auth->register($userData["nickname"], $userData["email"], $password);
                    $userData = $auth->login($userData["email"], $password);
                    if(empty($userData) || $userData == null){
                        $errors[] = "We are sorry...Something went wrong, try again later";
                    }else{
                        return json_encode($userData);
                    }
                }
            }
        }else{
            $errors[] = "All fields are required";
        }
        return json_encode($errors);
    }

    /**
        getRegisteredUserData method
        This method returns the data of the registered in user.
        @return string The registered user's data
    */
    public function getRegisteredUserData(): string
    {
        return $this->userData;
    }
}

/* $userData = ["nickname" => "testFF", "email" => "testFF@test.com", "password" => "testtest", "password_confirmation" => "testtest"];
$register = new Register($userData);
echo $register->getRegisteredUserData(); */