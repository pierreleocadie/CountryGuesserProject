<?php

namespace CountryGuesser\Lib;

/**
    SecureData class
    This class contains methods for securing data.
*/
class SecureData
{   
    /**
        secureStringData method
        This method secures a string by converting special characters to HTML entities.
        
        @param string $data The string to be secured
        @return string The secured string
    */
    public static function secureStringData(string $data): string
    {
        return htmlspecialchars($data);
    }

    /**
        secureArrayData method
        This method secures the values in an array by converting special characters to HTML entities.

        @param array $data The array to be secured
        @return array The secured array
    */
    public static function secureArrayData(array $data): array
    {
        foreach ($data as $key => $value) {
            $data[$key] = htmlspecialchars($value);
        }
        return $data;
    }

    /**
        securePassword method
        This method secures a password by hashing it with the Bcrypt algorithm.
        
        @param string $password The password to be secured
        @return string The hashed password
    */
    public static function securePassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ["cost" => 12]);
    }
}