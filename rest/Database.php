<?php

class Database
{
    const HOST = "localhost";
    const PORT = 3306;
    const DATABASE = "game";
    const USER = "hedgehog";
    const PASSWORD = "game";

    private $dbh;

    function __construct()
    {
        $connectionString = "mysql:host=" . self::HOST . ";port=" . self::PORT . ";charset=utf8;dbname=" . self::DATABASE;
        $this->dbh = new PDO($connectionString,
            self::USER,
            self::PASSWORD);
    }

    function getDbh()
    {
        return $this->dbh;
    }

}
