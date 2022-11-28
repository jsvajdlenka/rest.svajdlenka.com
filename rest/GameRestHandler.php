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

    public function gameCreate($playerId, $playerName, $minPlayers, $maxPlayers, $finishPos) {
        $game = Game::withFull($minPlayers, $maxPlayers, $finishPos);
        $this->gameDao->insert($game);
        $creator = Player::withFull($game, $playerId, $playerName);
        $this->playerDao->insert($creator);
        $game->addCreator($creator);
        $this->gameDao->update($game);

        $this->sendGameResponse($game);
    }

    public function gameJoin($gameId, $playerId, $playerName) {
        if ($gameId == -1) {
            $gameId = $this->gameDao->findJoinableGameId();
        }
        $game = $this->gameDao->findById($gameId);
        if ($game == null) {
            $this->sendErrorResponse("Game not exists");
            return;
        }
        // Read joined players
        $game->players = $this->playerDao->findByGameId($gameId);
        if ( !$game->isAbleToJoin() ) {
            $this->sendErrorResponse("Game is full");
            return;
        }
        $joiner = Player::withFull($game, $playerId, $playerName);
        $this->playerDao->insert($joiner);
        $game->addJoiner($joiner);
        $this->gameDao->update($game);
        $this->sendGameResponse($game);
    }

    public function gameStart($gameId, $playerId) {
        $game = $this->gameDao->findById($gameId);
        if ($game == null) {
            $this->sendErrorResponse("Game not exists");
            return;
        }
        // Read joined players
        $game->players = $this->playerDao->findByGameId($gameId);
        if ( $game->creatorId != $playerId ) {
            $this->sendErrorResponse("Game have to be started by creator");
            return;
        }
        if ( !$game->isAbleToStart() ) {
            $this->sendErrorResponse("Game need more joined players or is already started");
            return;
        }
        $game->startGame();;
        $this->gameDao->update($game);

//        $player = $game->players[$playerId];
//        $player->markRead();
//        $this->playerDao->update($player);
        $this->sendGameResponse($game);
    }

    public function playerRoll($gameId, $playerId, $clientRound, $clientRoll) {
        $game = $this->gameDao->findById($gameId);
        if ($game == null) {
            $this->sendErrorResponse("Game not exists");
            return;
        }
        // Read joined players
        $game->players = $this->playerDao->findByGameId($gameId);
        if ($game->actualPlayerId != $playerId) {
            $this->sendErrorResponse("Player is not on the move");
            return;
        }
        if ( !array_key_exists($playerId, $game->players) ) {
            $this->sendErrorResponse("Unknown player for game");
            return;
        }
        $player = $game->players[$playerId];
//        if ($clientRound != $game->actualRound) {
//            $this->sendErrorResponse("Not correct round for game");
//            return;
//        }
        if ($clientRound > $player->clientRound) {
            $player->rollDice($clientRoll, $clientRound);
            $game->rollDice($player->playerId, $clientRound, $player->serverRoll);
            $this->gameDao->update($game);
            foreach ($game->players as $updatePlayer) {
                $this->playerDao->update($updatePlayer);
            }
        }
        $this->sendGameResponse($game);
    }

    public function playerNext($gameId, $playerId, $clientRound) {
        $game = $this->gameDao->findById($gameId);
        if ($game == null) {
            $this->sendErrorResponse("Game not exists");
            return;
        }
        // Read joined players
        $game->players = $this->playerDao->findByGameId($gameId);
        if ($game->actualPlayerId != $playerId) {
            $this->sendErrorResponse("Player is not on the move");
            return;
        }
        if ( !array_key_exists($playerId, $game->players) ) {
            $this->sendErrorResponse("Unknown player for game");
            return;
        }
        $player = $game->players[$playerId];
        if ($clientRound != $game->actualRound) {
            $this->sendErrorResponse("Not correct round for game");
            return;
        }
        $player->nextPlayer($clientRound);
        $game->nextPlayer();
        $this->gameDao->update($game);
        $this->playerDao->update($player);
        $this->sendGameResponse($game);
    }

    public function playerStatus($gameId, $playerId) {
        $game = $this->gameDao->findById($gameId);
        if ($game == null) {
            $this->sendErrorResponse("Game not exists");
            return;
        }
        // Read joined players
        $game->players = $this->playerDao->findByGameId($gameId);
        if ( !array_key_exists($playerId, $game->players) ) {
            $this->sendErrorResponse("Unknown player for game");
            return;
        }
        $player = $game->players[$playerId];
        $this->playerDao->markRead($player);
        $this->sendGameResponse($game);
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