<?php
require_once 'Database.php';
require_once 'Player.php';

class PlayerDao
{
    const TABLE = "player_hedgehog";

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
                return Player::withRow($row);
            }
        }
        return null;
    }

    public function findByGameId($gameId) {
        $query = "SELECT * FROM ".self::TABLE." WHERE GAME_ID = :gameId;";
        $stmt = $this->dbh->prepare($query);
        $stmt->bindParam(':gameId', $gameId);
        $players = [];
        if ($stmt->execute()) {
            while ($row = $stmt->fetch()) {
                $player = Player::withRow($row);
                $players[$player->playerId] = $player;
            }
        }
        return $players;
    }

    public function insert($player) {
        $query = "INSERT INTO ".self::TABLE;
        $query = $query." ( GAME_ID, PLAYER_ID, PLAYER_NAME, SERVER_ROLL, CLIENT_ROLL, CLIENT_ROUND, GAME_POS, LAST_UPDATE_TIME, LAST_READ_TIME, MESSAGES )";
        $query = $query." VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? );";

        $values = [];
        array_push($values, $player->gameId);
        array_push($values, $player->playerId);
        array_push($values, $player->playerName);
        array_push($values, $player->serverRoll);
        array_push($values, $player->clientRoll);
        array_push($values, $player->clientRound);
        array_push($values, $player->gamePos);
        array_push($values, $player->lastUpdateTime);
        array_push($values, $player->lastReadTime);
        array_push($values, $player->messages);

        $this->dbh->prepare($query)->execute($values);
        $player->id = $this->dbh->lastInsertId();
    }

    public function update($player) {
        $query = "UPDATE ".self::TABLE;
        $query = $query." SET PLAYER_NAME=?, SERVER_ROLL=?, CLIENT_ROLL=?, CLIENT_ROUND=?, GAME_POS=?, LAST_UPDATE_TIME=?, LAST_READ_TIME=?, MESSAGES=? ";
        $query = $query." WHERE GAME_ID=? AND PLAYER_ID=?;";

        $values = [];
        array_push($values, $player->playerName);
        array_push($values, $player->serverRoll);
        array_push($values, $player->clientRoll);
        array_push($values, $player->clientRound);
        array_push($values, $player->gamePos);
        array_push($values, $player->lastUpdateTime);
        array_push($values, $player->lastReadTime);
        array_push($values, $player->messages);
        array_push($values, $player->gameId);
        array_push($values, $player->playerId);

        $this->dbh->prepare($query)->execute($values);
        $player->id = $this->dbh->lastInsertId();
    }

    public function markRead($player) {
        $query = "UPDATE ".self::TABLE;
        $query = $query." SET LAST_READ_TIME=?, MESSAGES=? ";
        $query = $query." WHERE GAME_ID=? AND PLAYER_ID=?;";

        $values = [];
        array_push($values, time());
        array_push($values, "");
        array_push($values, $player->gameId);
        array_push($values, $player->playerId);

        $this->dbh->prepare($query)->execute($values);
        $player->id = $this->dbh->lastInsertId();
    }

}