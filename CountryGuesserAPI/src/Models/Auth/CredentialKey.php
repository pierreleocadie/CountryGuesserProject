<?php

namespace CountryGuesser\Models\Auth;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Lib\SecureData;

/**
    CredentialKey class
    This class contains a method for generating a unique credential key for a user.
*/
class CredentialKey
{
    /**
        generateCredentialKey method
        This method generates a unique credential key for a user by hashing a combination of the user's data.
        
        @param array $userData An array containing the user's data (player_id, nickname, email, password, created_at)
        @return string The generated credential key
    */
    public static function generateCredentialKey(array $userData): string
    {
        $key = SecureData::securePassword($userData["player_id"] . $userData["nickname"] . $userData["email"] . $userData["password"] . $userData["created_at"]);
        return $key;
    }
}