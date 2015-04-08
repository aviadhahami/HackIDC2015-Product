<?php
/**
 * Created by PhpStorm.
 * User: וSER
 * Date: 4/8/2015
 * Time: 12:24 AM
 */

class postHandler {

    public $searchTag;
    public $beaconTable;
    public $clientTable;
    public $mysqli;


    function __construct($searchTag){
        $this->mysqli = new mysqli("localhost","839771","argov123","839771");

        $this->beaconTable = "beacon";
        $this->clientTable = "clients";
    }
}