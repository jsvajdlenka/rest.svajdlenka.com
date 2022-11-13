<?php
require_once("HedgehogRestHandler.php");

$view = "";
if (isset($_GET["view"]))
    $view = $_GET["view"];
/*
 * controls the RESTful services
 * URL mapping
 * new test
 */
switch ($view) {
    case "roll":
        // to handle REST Url /mobile/list/
        $mobileRestHandler = new HedgehogRestHandler();
        $mobileRestHandler->rollDice($_GET["id"]);
        break;

    case "":
        // 404 - not found;
        break;
}
