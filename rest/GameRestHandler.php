<?php
require_once ("SimpleRest.php");
require_once ("Game.php");
require_once ("Player.php");
require_once ("GameDao.php");
require_once ("PlayerDao.php");

class GameRestHandler extends SimpleRest {
    private $dbh;
    private $gameDao;
    private $playerDao;

    public function __construct($database) {
        $this->dbh = $database->getDbh();
        $this->gameDao = new GameDao($this->dbh);
        $this->playerDao = new PlayerDao($this->dbh);
    }

    public function createGame($playerid, $playername, $minPlayers, $maxPlayers) {
        $game = Game::withFull($minPlayers, $maxPlayers);
        $this->gameDao->insert($game);
        $creator = Player::withFull($game->id, $playerid, $playername);
        $this->playerDao->insert($creator);
        $game->addCreator($creator);
        $this->gameDao->update($game);

        $this->sendGameResponse($game);
    }

    public function joinGame($gameid, $playerid, $playername) {
        $game = $this->gameDao->findById($gameid);
        if ($game != null) {
            // Read joined players
            $game->players = $this->playerDao->findByGameId($gameid);
            if ( $game->isJoinAble() ) {
                $joiner = Player::withFull($gameid, $playerid, $playername);
                $this->playerDao->insert($joiner);
                $game->addJoiner($joiner);
                $this->gameDao->update($game);
                $this->sendGameResponse($game);
            } else {
                $this->sendErrorResponse("Game is full");
            }
        } else {
            $this->sendErrorResponse("Game not exists");
        }
    }

    public function sendGameResponse($game) {
        $statusCode = 200;
        $requestContentType = $_SERVER['HTTP_ACCEPT'];
        $this->setHttpHeaders($requestContentType, $statusCode);

        if (strpos($requestContentType, 'application/json') !== false) {
            $response = $this->encodeJson($game);
            echo $response;
        } else if (strpos($requestContentType, 'text/html') !== false) {
            $response = $this->encodeHtml($game);
            echo $response;
        } else if (strpos($requestContentType, 'application/xml') !== false) {
            $response = $this->encodeXml($game);
            echo $response;
        }
    }

    public function sendErrorResponse($error) {
        $statusCode = 400;
        $requestContentType = $_SERVER['HTTP_ACCEPT'];
        $this->setHttpHeaders($requestContentType, $statusCode);

        if (strpos($requestContentType, 'application/json') !== false) {
            $response = $this->encodeJson($error);
            echo $response;
        } else if (strpos($requestContentType, 'text/html') !== false) {
            $response = $this->encodeHtml($error);
            echo $response;
        } else if (strpos($requestContentType, 'application/xml') !== false) {
            $response = $this->encodeXml($error);
            echo $response;
        }
    }

}