<?php 

namespace CountryGuesser\Controllers;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Lib\RequestDataCompliance;
use CountryGuesser\Models\Game\CreateGame;

/*

    Steps to create a game:
    - Create a game in the database
    - Return the game id

*/

$game = new CreateGame();
$gameId = $game->createGame();
echo $gameId;