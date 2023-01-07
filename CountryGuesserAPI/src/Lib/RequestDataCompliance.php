<?php

namespace CountryGuesser\Lib;

require_once __DIR__ . "/../../vendor/autoload.php";

/**

    RequestDataCompliance class
    This class contains methods for checking the compliance of data in API requests.
*/
class RequestDataCompliance
{
    /**
        checkIfItsJSON method
        This method checks if a string is a valid JSON string.

        @param string $data The string to be checked
        @return bool True if the string is a valid JSON string, False if it is not
    */
    public static function checkIfItsJSON(string $data): bool
    {
        return is_string($data) && is_array(json_decode($data, true)) ? true : false;
    }

    /**
        LoginDataCompliance method
        This method checks if the data for a login request is compliant. 
        The data should contain a "nickname_email" and "password" key, with string values.
        
        @param array $data The data to be checked
        @return bool True if the data is compliant, False if it is not
    */
    public static function LoginDataCompliance(array $data): bool
    {
        if(array_key_exists("nickname_email", $data) && array_key_exists("password", $data)){
            if(is_string($data["nickname_email"]) && is_string($data["password"])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
        RegisterDataCompliance method
        This method checks if the data for a register request is compliant. 
        The data should contain a "nickname", "email", "password", and "password_confirmation" key, with string values.
        
        @param array $data The data to be checked
        @return bool True if the data is compliant, False if it is not
    */
    public static function RegisterDataCompliance(array $data): bool
    {
        if(array_key_exists("nickname", $data) && array_key_exists("email", $data) && array_key_exists("password", $data) && array_key_exists("password_confirmation", $data)){
            if(is_string($data["nickname"]) && is_string($data["email"]) && is_string($data["password"]) && is_string($data["password_confirmation"])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
        CreateRoundDataCompliance method
        This method checks if the data for a create round request is compliant. 
        The data should contain a "game_id", "round_id", and "response" key, with integer and string values respectively.
        
        @param array $data The data to be checked
        @return bool True if the data is compliant, False if it is not
    */
    public static function CreateRoundDataCompliance(array $data): bool
    {
        if(array_key_exists("game_id", $data) && array_key_exists("round_id", $data) && array_key_exists("response", $data)){
            if(is_int($data["game_id"]) && is_int($data["round_id"]) && is_string($data["response"])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
        InsertRoundDataDataCompliance method
        This method checks if the request data for inserting round data is compliant.
        The request data must contain "game_id", "round_id", "player_id", and "player_response" keys.
        The values for these keys must be integers and a string, respectively.

        @param array $data The request data to be checked for compliance
        @return bool True if the request data is compliant, False if it is not
    */
    public static function InsertRoundDataDataCompliance(array $data): bool
    {
        if(array_key_exists("game_id", $data) && array_key_exists("round_id", $data) && array_key_exists("player_id", $data) && array_key_exists("player_response", $data)){
            if(is_int($data["game_id"]) && is_int($data["round_id"]) && is_int($data["player_id"]) && is_string($data["player_response"])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
        UpdateGameDataCompliance method
        This method checks if the request data for updating a game is compliant.
        The request data must contain a "game_id" key with an integer value.

        @param array $data The request data to be checked for compliance
        @return bool True if the request data is compliant, False if it is not
    */
    public static function UpdateGameDataCompliance(array $data): bool
    {
        if(array_key_exists("game_id", $data)){
            if(is_int($data["game_id"])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
        GetGameDataDataCompliance method
        This method checks if the request data for getting game data is compliant.
        The request data must contain a "game_id" key with an integer value.

        @param array $data The request data to be checked for compliance
        @return bool True if the request data is compliant, False if it is not
    */
    public static function GetGameDataDataCompliance(array $data): bool
    {
        if(array_key_exists("game_id", $data)){
            if(is_int($data["game_id"])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
        PlayerDataWithCredentialKeyDataComplicance method
        This method checks if an array of data has a "credential_key" key and if its value is a string.

        @param array $data The array of data to be checked
        @return bool True if the data is compliant, False if it is not
    */
    public static function PlayerDataWithCredentialKeyDataComplicance(array $data): bool
    {
        if(array_key_exists("credential_key", $data)){
            if(is_string($data["credential_key"])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
        CheckRoundDataCompliance method
        This method checks if an array of data has "game_id" and "round_id" keys and if their values are integers.

        @param array $data The array of data to be checked
        @return bool True if the data is compliant, False if it is not
    */
    public static function CheckRoundDataCompliance(array $data): bool
    {
        if(array_key_exists("game_id", $data) && array_key_exists("round_id", $data)){
            if(is_int($data["game_id"]) && is_int($data["round_id"])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
        DeleteGameDataCompliance method
        This method checks if an array of data has a "game_id" key and if its value is an integer.

        @param array $data The array of data to be checked
        @return bool True if the data is compliant, False if it is not
    */
    public static function DeleteGameDataCompliance(array $data): bool
    {
        if(array_key_exists("game_id", $data)){
            if(is_int($data["game_id"])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
        GameParticipantsDataCompliance method
        This method checks if an array of data has "game_id" and "player_id" keys and if their values are integers.

        @param array $data The array of data to be checked
        @return bool True if the data is compliant, False if it is not
    */
    public static function GameParticipantsDataCompliance(array $data): bool
    {
        if(array_key_exists("game_id", $data) && array_key_exists("player_id", $data)){
            if(is_int($data["game_id"]) && is_int($data["player_id"])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
        UpdateLeaderboardDataCompliance method
        This method checks if the data in an array is compliant for updating a leaderboard.
        The array must contain a key "player_id" with an integer value.

        @param array $data The array to be validated
        @return bool True if the data is compliant, False if it is not
    */
    public static function UpdateLeaderboardDataCompliance(array $data): bool
    {
        if(array_key_exists("player_id", $data)){
            if(is_int($data["player_id"])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
        GetLeaderboardStatsDataCompliance method
        This method checks if the data in an array is compliant for getting leaderboard stats for a player.
        The array must contain a key "player_id" with an integer value.

        @param array $data The array to be validated
        @return bool True if the data is compliant, False if it is not
    */
    public static function GetLeaderboardStatsDataCompliance(array $data): bool
    {
        if(array_key_exists("player_id", $data)){
            if(is_int($data["player_id"])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}