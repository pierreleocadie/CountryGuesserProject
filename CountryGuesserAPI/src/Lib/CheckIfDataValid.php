<?php

namespace CountryGuesser\Lib;

/**
    CheckIfDataValid class
    This class contains methods for validating data.
*/
class CheckIfDataValid
{
    /**
        checkIfNicknameValid method
        This method checks if a nickname is valid. A valid nickname is between 3 and 20 characters long.

        @param string $nickname The nickname to be validated
        @return bool True if the nickname is valid, False if it is not
    */
    public static function checkIfNicknameValid(string $nickname): bool
    {
        return mb_strlen($nickname) >= 3 && mb_strlen($nickname) <= 20;
    }

    /**
        checkIfEmailValid method
        This method checks if an email address is valid.

        @param string $email The email address to be validated
        @return bool True if the email address is valid, False if it is not
    */
    public static function checkIfEmailValid(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
        checkPasswordLengthValid method
        This method checks if a password is long enough. A valid password is at least 8 characters long.

        @param string $password The password to be validated
        @return bool True if the password is long enough, False if it is not
    */
    public static function checkPasswordLengthValid(string $password): bool
    {
        return mb_strlen($password) >= 8;
    }

    /**
        checkIfPasswordValid method
        This method checks if a password and its confirmation match.

        @param string $password The password to be validated
        @param string $password_confirmation The password confirmation to be compared to the password
        @return bool True if the password and confirmation match, False if they do not
    */
    public static function checkIfPasswordValid(string $password, string $password_confirmation): bool
    {
        return $password == $password_confirmation;
    }

    /**
        checkIfArrayValuesNotEmpty method
        This method checks if all values in an array are not empty.

        @param array $array The array to be validated
        @return bool True if all values in the array are not empty, False if any value is empty or the array is empty
    */
    public static function checkIfArrayValuesNotEmpty(array $array): bool
    {
        return !in_array("", $array) && count($array) != 0;
    }

}