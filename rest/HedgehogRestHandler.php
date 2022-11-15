<?php
require_once ("SimpleRest.php");
require_once ("Hedgehog.php");

class HedgehogRestHandler extends SimpleRest
{

    public function rollDice($id)
    {
        $object = new Hedgehog();
        $rawData = $object->rollDice($id);

        $statusCode = 200;

        $requestContentType = $_SERVER['HTTP_ACCEPT'];
        $this->setHttpHeaders($requestContentType, $statusCode);

        if (strpos($requestContentType, 'application/json') !== false) {
            $response = $this->encodeJson($rawData);
            echo $response;
        } else if (strpos($requestContentType, 'text/html') !== false) {
            $response = $this->encodeHtml($rawData);
            echo $response;
        } else if (strpos($requestContentType, 'application/xml') !== false) {
            $response = $this->encodeXml($rawData);
            echo $response;
        }
    }
}