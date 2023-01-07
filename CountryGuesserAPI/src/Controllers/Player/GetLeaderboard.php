<?php 

namespace CountryGuesser\Controllers;

require_once __DIR__ . "/../../../vendor/autoload.php";

use CountryGuesser\Lib\RequestDataCompliance;
use CountryGuesser\Models\Player\Leaderboard;

/*

    Steps to get the leaderboard:
    - We get the leaderboard from the database
    - We send it back

*/

$leaderboard = new Leaderboard();
echo $leaderboard->getLeaderboard();