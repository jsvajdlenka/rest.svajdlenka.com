<?php
require_once("HedgehogRestHandler.php");
require_once("GameRestHandler.php");
require_once("Database.php");

$action = "";
if (isset($_GET["action"]))
    $action = $_GET["action"];

$database = new Database();
$gameRestHandler = new GameRestHandler($database);

switch ($action) {
    case "roll":
        // to handle REST Url /mobile/list/
        $mobileRestHandler = new HedgehogRestHandler();
        $mobileRestHandler->rollDice($_GET["id"]);
        break;
    case "create":
        $gameRestHandler->createGame($_GET["playerid"], $_GET["playername"], $_GET["count"], $_GET["uniqueplayers"]);
        break;
    case "join":
        $gameRestHandler->joinGame($_GET["gameid"], $_GET["playerid"], $_GET["playername"]);
        break;


    case "":
        // 404 - not found;
        break;
}
