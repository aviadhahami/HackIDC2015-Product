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
            return $this->getClientsSet();
        }

        return null;

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


    private function addNewClient($clientArr)
    {
        $localBeaconId = $this->beaconID;
        $localClientTable = $this->clientTable;

        $clientId = $this->generateClientId($clientArr);

        $addNewClientQuery = "INSERT INTO $localClientTable (bid, cid) VALUES ('$localBeaconId', '$clientId')";

        $addNewClientExecute = $this->mysqli->query($addNewClientQuery);

        if($addNewClientExecute)
        {
            return $clientId;
        }
        return null;


    }

    private function generateClientId($clientArr)
    {
        $size = $this->getAmountConnected($clientArr);
        $beaconShrink = substr($this->beaconID,0,10);
        return $beaconShrink . $size;

    }

    private function getAmountConnected($arrObjects)
    {
        if($arrObjects === null)
        {
            return 0;
        }
        return sizeof($arrObjects);
    }


    public function handleConnectionRequest()
    {
        $userSet = $this->isBeaconExists();
        if($userSet === null)
        {
            return null;
        }

        $clientId = $this->addNewClient($userSet);
        if($clientId === null)
        {
            return null;
        }

        return ["connection"=>"1",'cid'=>$clientId, 'amount'=> $this->getAmountConnected($userSet)];
    }




    /////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////
    public function handleDisconnectionRequest($clientID)
    {
        if($this->verifyBeaconBoolean())
        {
            return $this->removeClientFromDB($clientID);
        }
        return false;
    }


    private function removeClientFromDB($clientID)
    {
        $localBeaconId = $this->beaconID;
        $localClientTable = $this->clientTable;

        $removeClientQuery = "DELETE FROM '$localClientTable' WHERE bid = '$localBeaconId' AND uid = '$clientID'";

        $removeClientExecute = $this->mysqli->query($removeClientQuery);
        if($removeClientExecute === true)
        {
            return true;
        }
        return false;

    }

    public function verifyBeaconBoolean()
    {
        $localBeacon = $this->beaconID;
        $beaconTbl = $this->$beaconTable;
        $beaconExistsQuery = "SELECT * FROM $beaconTbl WHERE bid = '$localBeacon'";

        $resultSet = $this->mysqli->query($beaconExistsQuery);
        $resultSetNumRows = $resultSet->num_rows;
        if(false || $resultSetNumRows === 0)
        {
            return false;
        }

        elseif($resultSetNumRows === 1)
        {
            return true;
        }

        return false;

    }






}