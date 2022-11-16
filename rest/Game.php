<?php
require_once ("Player.php");

/*
 * A domain Class Game
 */

class Game {
    const STATE_WRONG_ID = "WRONG_ID";
    const STATE_CREATED  = "CREATED";   // Created game
    const STATE_JOINED   = "JOINED";    // If there is minimum one player joined to game
    const STATE_STARTED  = "STARTED";   // Game in progress
    const STATE_FINISHED = "FINISHED";  // Already finished game

    public $id = -1;               // Game id
    public $minPlayers = 2;        // Minimum players
    public $maxPlayers = 9;        // Maximum unique players
    public $finishPos = 103;       // Finish position for game
    public $creatorId;             // Player who created game
    public $actualPlayerId = 0;    // Actual player id
    public $actualRound = 0;       // Actual round of game
    public $state = self::STATE_CREATED; // Actual state of game
    public $lastUpdateTime;        // Last timestamp of change
    public $players = [];          // All players of game

    public function __construct() {
        $this->players = [];
    }

    public static function withFull($minPlayers, $maxPlayers, $finishPos) {
        $instance = new self();
        $instance->minPlayers = $minPlayers;
        $instance->maxPlayers = $maxPlayers;
        $instance->finishPos = $finishPos;
        $instance->creatorId = -1;
        $instance->actualPlayerId = -1;
        $instance->actualRound = 0;
        $instance->lastUpdateTime = time();
        $instance->state = self::STATE_CREATED;

        return $instance;
    }

    public static function withRow( array $row ) {
        $instance = new self();
        $instance->id = $row["ID"];
        $instance->minPlayers = $row["MIN_PLAYERS"];
        $instance->maxPlayers = $row["MAX_PLAYERS"];
        $instance->creatorId = $row["CREATOR_ID"];
        $instance->actualPlayerId = $row["ACTUAL_PLAYER_ID"];
        $instance->actualRound = $row["ACTUAL_ROUND"];
        $instance->state = $row["STATE"];
        $instance->finishPos = $row["FINISH_POS"];
        $instance->lastUpdateTime = $row["LAST_UPDATE_TIME"];

        return $instance;
    }

    public function addCreator($creator) {
        $this->creatorId = $creator->playerId;
        $this->actualPlayerId = $creator->playerId;
        $this->players[$creator->playerId] = $creator;
        $this->lastUpdateTime = time();
    }

    public function addJoiner($joiner) {
        $this->state = self::STATE_JOINED;
        $this->actualPlayerId = $joiner->playerId;
        $this->players[$joiner->playerId] = $joiner;
        $this->lastUpdateTime = time();
    }

    public function startGame() {
        $this->state = self::STATE_STARTED;
        $this->actualRound = 1;
        $this->lastUpdateTime = time();
    }

    public function nextPlayer() {
        $playerKeys = array_keys($this->players);
        $index = array_search($this->actualPlayerId, $playerKeys);
        if ($index < count($playerKeys)-1) {
            $this->actualPlayerId = $playerKeys[$index + 1];
        } else {
            $this->actualPlayerId = $playerKeys[0];
            $this->actualRound += 1;
        }
        if ( $this->isGameFinished() ) {
            $this->state = self::STATE_FINISHED;
        }
        $this->lastUpdateTime = time();
    }

    public function isGameFinished() {
        foreach ($this->players as $player) {
            if ($player->gamePos < $this->finishPos ) {
                return false;
            }
        }
        return true;
    }

    public function isAbleToJoin() {
        return count($this->players) < $this->maxPlayers
            && ($this->state == self::STATE_CREATED || $this->state == self::STATE_JOINED);
    }

    public function isAbleToStart() {
        return count($this->players) >= $this->minPlayers && $this->state == self::STATE_JOINED;
    }

}


