<?php
/**
 * Created by PhpStorm.
 * User: וSER
 * Date: 4/7/2015
 * Time: 7:30 PM
 */

class Beacon {

    public $beaconID;
    public $responseID;
    public $mysqli;
    public $beaconTable;
    public $clientTable;

    function __construct($beaconID, $responseID)
    {
        $this->beaconID = $beaconID;
        $this->responseID = $responseID;

        $this->mysqli = new mysqli("localhost","839771","argov123","839771");
        $this->$beaconTable = "beacon";
        $this->clientTable = "clients";
    }


    /**
     * @return null | objectSet if beacon doesn't exists or failed in fetch returns null | returns the query set of clients
     */
    public function isBeaconExists()
    {
        $localBeacon = $this->beaconID;
        $beaconTbl = $this->$beaconTable;
        $beaconExistsQuery = "SELECT * FROM $beaconTbl WHERE bid = '$localBeacon'";

        $resultSet = $this->mysqli->query($beaconExistsQuery);
        $resultSetNumRows = $resultSet->num_rows;
        if(false || $resultSetNumRows === 0)
        {
            return null;
        }

        elseif($resultSetNumRows === 1)
        {
            return $resultSet->fetch_object();
        }

        return $this->getClientsSet();

    }


    /**
     * @desc retrieves an object set that represent the fetch
     * @return null|object|stdClass
     */
    private function getClientsSet()
    {
        $localClientTbl = $this->clientTable;
        $localBeacon = $this->beaconID;

        $returnArr = array();

        $getClientSetQuery = "SELECT * FROM $localClientTbl WHERE bid = '$localBeacon'";

        $clientSetResult = $this->mysqli->query($getClientSetQuery);


        //In case fetch failed
        if($clientSetResult === false)
        {
            return null;
        }


        //TODO: when result set is empty aka no other clients should be handled
        while($nextRow = $clientSetResult->fetch_object())
        {
            $returnArr[] = $nextRow;
        }
        return $returnArr;


    }

    /////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////


    private function addNewClient()
    {
        $localBeaconId = $this->beaconID;
        $localClientTable = $this->clientTable;

        $clientId = $this->generateClientId();

        $addNewClientQuery = "INSERT INTO $localClientTable (bid, cid) VALUES ('$localBeaconId', '$clientId')";

        $addNewClientExecute = $this->mysqli->query($addNewClientQuery);




    }

    private function generateClientId(){}

    public function handleConnectionRequest()
    {
        $beaconIdSet = $this->isBeaconExists();
        if($beaconIdSet === null)
        {
            return -1;
        }

    }

    private function getAmountConnected($arrObjects)
    {
        if($arrObjects === null)
        {
            return 0;
        }
        return sizeof($arrObjects);
    }


    /////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////
    public function handleDisconnectionRequest(){}






}