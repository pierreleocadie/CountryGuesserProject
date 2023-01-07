<?php

namespace CountryGuesser\Models\Auth;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Models\Database\DatabaseConnection;
use CountryGuesser\Models\Auth\CredentialKey;

use PDO;

/**
    AuthDB class
    This class contains methods for interacting with the database for user authentication purposes.

    @param PDO $db A PDO object for connecting to the database
*/
class AuthDB
{

    private object $db;

    /**
        Constructor for the AuthDB class
        @param PDO $db A PDO object for connecting to the database
    */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
        register method
        This method registers a player in the database and generates a credential key for the player.

        @param string $nickname The player's nickname
        @param string $email The player's email address
        @param string $password The player's hashed password
    */
    public function register(string $nickname, string $email, string $password): void
    {
        $query = $this->db->prepare("INSERT INTO players (nickname, email, password) VALUES (:nickname, :email, :password)");
        $query->execute([
            "nickname" => $nickname,
            "email" => $email,
            "password" => $password,
        ]);
        
        // Select the player that was just registered
        $query = $this->db->prepare("SELECT player_id, nickname, email, password, created_at FROM players WHERE nickname = :nickname AND email = :email AND password = :password");
        $query->execute([
            "nickname" => $nickname,
            "email" => $email,
            "password" => $password,
        ]);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        // Generate a credential key for the player
        $credentialKey = CredentialKey::generateCredentialKey($result);

        // Insert the credential key into the database
        $query = $this->db->prepare("UPDATE players SET credential = :credential WHERE nickname = :nickname AND email = :email AND password = :password");
        $query->execute([
            "credential" => $credentialKey,
            "nickname" => $nickname,
            "email" => $email,
            "password" => $password,
        ]);
    }

    /**
        selectPlayerPasswordHash method
        This method selects a player's password hash from the database based on their nickname or email.

        @param string $nickname_email The player's nickname or email address
        @return string The player's password hash
    */
    public function selectPlayerPasswordHash(string $nickname_email): string
    {
        $query = $this->db->prepare("SELECT password FROM players WHERE nickname = :nickname_email OR email = :nickname_email");
        $query->execute([
            "nickname_email" => $nickname_email
        ]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result["password"];
    }

    /**
        login method
        This method logs a player in by checking if their login credentials are correct.
        
        @param string $nickname_email The nickname or email address of the player
        @param string $password The password of the player
        @return array An array containing the player's information, including their credential key
    */
    public function login(string $nickname_email, string $password): array
    {
        $query = $this->db->prepare("SELECT player_id, nickname, email, credential FROM players WHERE (nickname = :nickname_email OR email = :nickname_email) AND password = :password");
        $query->execute([
            "nickname_email" => $nickname_email,
            "password" => $password
        ]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

}

/* $dbConnect = new DatabaseConnection();
$db = $dbConnect->getDbConnection();
$auth = new AuthDB($db);
echo var_dump($auth->selectPlayerPasswordHash("testForWS")); */