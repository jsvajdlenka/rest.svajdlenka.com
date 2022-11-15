<?php
require_once ("Player.php");

/*
 * A domain Class Game
 */

const STATE_WRONG_ID = "WRONG_ID";
const STATE_CREATED  = "CREATED";   // Created game
const STATE_JOINED   = "JOINED";    // If there is minimum one player joined to game
const STATE_STARTED  = "STARTED";   // Game in progress
const STATE_FINISHED = "FINISHED";  // Already finished game

class Game
{
    public $id = -1;               // Game id
    public $minPlayers = 2;        // Minimum players
    public $maxPlayers = 9;        // Maximum unique players
    public $creatorId;             // Player who created game
    public $actualPlayerId = 0;    // Actual player id
    public $actualRound = 0;       // Actual round of game
    public $state = STATE_CREATED; // Actual state of game
    public $players = [];          // All players of game

    public function __construct() {
        $this->players = [];
    }

    public static function withFull($minPlayers=2, $maxPlayers=9) {
        $instance = new self();
        $instance->minPlayers = $minPlayers;
        $instance->maxPlayers = $maxPlayers;
        $instance->creatorId = -1;
        $instance->actualPlayerId = -1;
        $instance->actualRound = 0;
        $instance->state = STATE_CREATED;

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

        return $instance;
    }

    public function addCreator($creator) {
        $this->creatorId = $creator->playerId;
        $this->actualPlayerId = $creator->playerId;
        $this->players[$creator->playerId] = $creator;
    }

    public function addJoiner($joiner) {
        $this->state = STATE_JOINED;
        $this->players[$joiner->playerId] = $joiner;
    }

    public function isJoinAble() {
        return count($this->players) < $this->maxPlayers;
    }

    public static function generateId($games) {
        $gameId = 0;
        foreach ($games as $game) {
            if ($game.id > $gameId) {
                $gameId = $game.id;
            }
        }
        return $gameId+1;
    }
}


