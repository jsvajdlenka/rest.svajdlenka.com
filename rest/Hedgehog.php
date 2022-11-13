<?php

/*
 * A domain Class to demonstrate RESTful web services
 */

class Hedgehog
{

    /*
     * you should hookup the DAO here
     */
    public function rollDice($id)
    {
        $result = array(
            "id" =>  $id,
            "value" => rand(1, 6)
        );
        return $result;
    }

}

