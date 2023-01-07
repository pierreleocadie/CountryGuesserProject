<?php

require_once __DIR__ . "/../vendor/autoload.php";

use CountryGuesser\Models\Router;

// To avoid CORS errors
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

$uri = $_SERVER["REQUEST_URI"];

$router = new Router();
$router->defineRoute("/login",                      "Auth/LoginController.php");
$router->defineRoute("/register",                   "Auth/RegisterController.php");
$router->defineRoute("/game/create",                "Game/CreateGameController.php");
$router->defineRoute("/game/update",                "Game/UpdateGameController.php");
$router->defineRoute("/game/delete",                "Game/DeleteGameController.php");
$router->defineRoute("/game/getgamedata",           "Game/GetGameDataController.php");
$router->defineRoute("/game/participants",          "Game/GameParticipantsController.php");
$router->defineRoute("/game/round/create",          "Game/CreateRoundController.php");
$router->defineRoute("/game/round/playeranswer",    "Game/InsertRoundDataController.php");
$router->defineRoute("/game/round/check",           "Game/CheckRoundController.php");
$router->defineRoute("/player/playerdata",          "Player/PlayerDataController.php");
$router->defineRoute("/player/getleaderboard",      "Player/GetLeaderboard.php");
$router->defineRoute("/player/updateleaderboard",   "Player/UpdateLeaderboard.php");
$router->defineRoute("/player/getleaderboardstats", "Player/GetLeaderboardStats.php");

$router->redirect($uri);


