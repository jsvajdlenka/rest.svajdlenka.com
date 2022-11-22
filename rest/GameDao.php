<?php
require_once 'Database.php';
require_once 'Game.php';

class GameDao
{
    const TABLE = "game_hedgehog";

    private $dbh;

    function __construct($dbh) {
        $this->dbh = $dbh;
    }

    public function findById($id) {
        $query = "SELECT * FROM ".self::TABLE." WHERE id = :id;";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            while ($row = $stmt->fetch()) {
                return Game::withRow($row);
            }
        }

        return null;
    }

    public function findJoinableGameId() {
//        $query = "SELECT ID FROM ".self::TABLE." G WHERE G.STATE = 'CREATED' OR (G.STATE = 'JOINED' AND G.MAX_PLAYERS > (SELECT COUNT(*) FROM ".GameDao::TABLE." P WHERE P.GAME_ID = G.ID ));";
        $query = "SELECT ID FROM game_hedgehog G WHERE G.STATE = 'CREATED' OR (G.STATE = 'JOINED' AND G.MAX_PLAYERS > (SELECT COUNT(*) FROM player_hedgehog P WHERE P.GAME_ID = G.ID ));";
        $stmt = $this->dbh->prepare($query);
        if ($stmt->execute()) {
            if ($row = $stmt->fetch()) {
                return $row["ID"];
            }
        }

        return null;
    }
    public function insert($game) {
        $query = "INSERT INTO ".self::TABLE;
        $query = $query." ( MIN_PLAYERS, MAX_PLAYERS, CREATOR_ID, ACTUAL_PLAYER_ID, ACTUAL_ROUND, ACTUAL_MOVE, STATE, FINISH_POS, LAST_UPDATE_TIME )";
        $query = $query." VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? );";

        $values = [];
        array_push($values, $game->minPlayers);
        array_push($values, $game->maxPlayers);
        array_push($values, $game->creatorId);
        array_push($values, $game->actualPlayerId);
        array_push($values, $game->actualRound);
        array_push($values, $game->actualMove);
        array_push($values, $game->state);
        array_push($values, $game->finishPos);
        array_push($values, $game->lastUpdateTime);

        $this->dbh->prepare($query)->execute($values);
        $game->id = $this->dbh->lastInsertId();
    }

    public function update($game) {
        $query = "UPDATE ".self::TABLE;
        $query = $query." SET MIN_PLAYERS=?, MAX_PLAYERS=?, CREATOR_ID=?, ACTUAL_PLAYER_ID=?, ACTUAL_ROUND=?, ACTUAL_MOVE=?, STATE=?, FINISH_POS=?, LAST_UPDATE_TIME=? ";
        $query = $query." WHERE ID = ?;";

        $values = [];
        array_push($values, $game->minPlayers);
        array_push($values, $game->maxPlayers);
        array_push($values, $game->creatorId);
        array_push($values, $game->actualPlayerId);
        array_push($values, $game->actualRound);
        array_push($values, $game->actualMove);
        array_push($values, $game->state);
        array_push($values, $game->finishPos);
        array_push($values, $game->lastUpdateTime);

        array_push($values, $game->id);

        $this->dbh->prepare($query)->execute($values);
    }
}