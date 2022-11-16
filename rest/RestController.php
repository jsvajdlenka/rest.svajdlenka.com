<?php
require_once("HedgehogRestHandler.php");
require_once("GameRestHandler.php");
require_once("Database.php");

const P_ACTION = "action";
const P_PLAYER_ID = "playerid";
const P_PLAYER_NAME = "playername";
const P_GAME_ID = "gameid";
const P_MIN_PLAYERS = "count";
const P_MAX_PLAYERS = "uniqueplayers";
const P_FINISH_POS = "finishpos";
const P_CLIENT_ROUND = "clientround";
const P_CLIENT_ROLL = "clientroll";

const A_CREATE = "create";
const A_JOIN = "join";
const A_START = "start";
const A_ROLL = "roll";
const A_STATUS = "status";

function getParam($key, $default="") {
    if (isset($_GET[$key])) {
        return $_GET[$key];
    } else {
        return $default;
    }

}

$action = getParam(P_ACTION);

$database = new Database();
$gameRestHandler = new GameRestHandler($database);

switch ($action) {
    case A_CREATE:
        $gameRestHandler->gameCreate( getParam(P_PLAYER_ID, -1)
                                    , getParam(P_PLAYER_NAME)
                                    , getParam(P_MIN_PLAYERS, 2)
                                    , getParam(P_MAX_PLAYERS, 9)
                                    , getParam(P_FINISH_POS, 103) );
        break;
    case A_JOIN:
        $gameRestHandler->gameJoin( getParam(P_GAME_ID, -1)
                                  , getParam(P_PLAYER_ID, -1)
                                  , getParam(P_PLAYER_NAME) );
        break;
    case A_START:
        $gameRestHandler->gameStart( getParam(P_GAME_ID, -1)
                                   , getParam(P_PLAYER_ID, -1) );
        break;
    case A_ROLL:
        $gameRestHandler->playerRoll( getParam(P_GAME_ID, -1)
                                    , getParam(P_PLAYER_ID, -1)
                                    , getParam(P_CLIENT_ROUND, -1)
                                    , getParam(P_CLIENT_ROLL, -1) );
        break;
    case A_STATUS:
        $gameRestHandler->playerStatus( getParam(P_GAME_ID, -1)
                                      , getParam(P_PLAYER_ID, -1) );
        break;

    case "":
        // 404 - not found;
        break;
}
