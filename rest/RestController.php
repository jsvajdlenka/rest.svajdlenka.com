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
    case "create":
        $gameRestHandler->gameCreate( $_GET["playerid"]
                                    , $_GET["playername"]
                                    , $_GET["count"]
                                    , $_GET["uniqueplayers"]
                                    , $_GET["finishpos"] );
        break;
    case "join":
        $gameRestHandler->gameJoin( $_GET["gameid"]
                                  , $_GET["playerid"]
                                  , $_GET["playername"] );
        break;
    case "start":
        $gameRestHandler->gameStart( $_GET["gameid"]
                                   , $_GET["playerid"] );
        break;
    case "roll":
        $gameRestHandler->playerRoll( $_GET["gameid"]
                                    , $_GET["playerid"]
                                    , $_GET["clientround"]
                                    , $_GET["clientroll"] );
        break;
    case "status":
        $gameRestHandler->playerStatus( $_GET["gameid"]
                                      , $_GET["playerid"] );
        break;

    case "":
        // 404 - not found;
        break;
}
