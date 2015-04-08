<?php
/**
 * Created by PhpStorm.
 * User: וSER
 * Date: 4/7/2015
 * Time: 7:30 PM
 */

class Beacon {

    public $beaconID;//Represent an MAC address
    public $responseID;
    public $mysqli;
    public $beaconTable;
    public $clientTable;
    public $beaconIndex;

    function __construct($beaconID, $responseID)
    {
        $this->beaconID = $beaconID;
        $this->responseID = $responseID;

        $this->mysqli = new mysqli("localhost","839771","argov123","839771");

        $this->beaconTable = "beacon";
        $this->clientTable = "clients";

        $this->beaconIndex = 0;
    }





    /**
     * @return null | objectSet if beacon doesn't exists or failed in fetch returns null | returns the query set of clients
     */
    public function isBeaconExists()
    {
        $localBeacon = $this->beaconID;
        $beaconTbl = $this->beaconTable;
        $beaconExistsQuery = "SELECT * FROM $beaconTbl WHERE bid = '$localBeacon'";

        $resultSet = $this->mysqli->query($beaconExistsQuery);
        $resultSetNumRows = $resultSet->num_rows;
        if(false || $resultSetNumRows === 0)
        {

            return null;
        }

        elseif($resultSetNumRows === 1)
        {
            $beaconRowForExtractingId = $resultSet->fetch_object();
            $this->beaconIndex = $beaconRowForExtractingId->id;
            return $this->getClientsSet();
        }
        $this->beaconIndex = -1;

        return null;

    }


    /**
     * @desc retrieves an object set that represent the fetch
     * @return null|object|stdClass
     */
    private function getClientsSet()
    {
        //TODO: check if the beacon index is greated then 1 aka valid index
        $localClientTbl = $this->clientTable;
        //$localBeacon = $this->beaconID;
        $localBeacon = $this->beaconIndex;//Changed to work with new table structure

        $returnArr = array();

        $getClientSetQuery = "SELECT * FROM $localClientTbl WHERE bid = $localBeacon";//crossing against id from other table

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
        //$localBeaconId = $this->beaconID;
        $localBeaconId = $this->beaconIndex;//Changed to work with new table Structure
        $localClientTable = $this->clientTable;

        $clientId = $this->generateClientId($clientArr);

        $addNewClientQuery = "INSERT INTO $localClientTable (bid, uid) VALUES ($localBeaconId, '$clientId')";

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
        ///
        //echo "</br> entered HandleConnection";
        ///
        $userSet = $this->isBeaconExists();

       // echo "</br> userset : " . print_r($userSet) . "||</br>";

        if($userSet === null)
        {
            return ["connection"=>"-1"];
        }

       // echo "</br> first if block passed in connection handler </br>";

        $clientId = $this->addNewClient($userSet);

        ////
       // echo "</br> clientID = ". $clientId ."</br>";
        ////

        if($clientId === null)
        {
            return ["connection"=>"-1"];
        }
        //echo "</br> second if block passed in connection handler </br>";

        $amountConnect = $this->getAmountConnected($userSet);
        $imgSrc = $this->getFreeImage($amountConnect);

        return ["connection"=>"1",'cid'=>$clientId, 'amount'=> $amountConnect, "img"=>$imgSrc];
    }




    /////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////
    public function handleDisconnectionRequest($clientID)
    {
        $bool = $this->verifyBeaconBoolean();
        //echo "</br> handleDisconnect berifyBeaconbool = " .$bool ."</br>";
        if($bool)
        {
            return $this->removeClientFromDB($clientID);
        }
        return false;
    }


    private function removeClientFromDB($clientID)
    {
        //$localBeaconId = $this->beaconID;
        $localBeaconId = $this->beaconIndex;//Meant to work correctly
        $localClientTable = $this->clientTable;

        $removeClientQuery = "DELETE FROM $localClientTable WHERE bid = $localBeaconId AND uid = '$clientID'";

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
        $beaconTbl = $this->beaconTable;
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



    ////////////////////////////////////
    ///////////////////////////////////
    ///////////YET TO BE TESTED///////
    /////////////////////////////////
    ////////////////////////////////
    public function addNewBeaconId()
    {
        $beaconTbl = $this->beaconTable;
        $localBeaconId = $this->beaconID;

        $isBeaconExistBool = $this->verifyBeaconBoolean();

        if(!$isBeaconExistBool)
        {
            $insertBeaconQuery = "INSERT INTO $beaconTbl (bid) VALUES ('$localBeaconId')";
            $insertBeaconExecute = $this->mysqli->query($insertBeaconQuery);

            return $insertBeaconExecute;
        }

        return false;
    }

    public function removeBeaconById()
    {
        $beaconTbl = $this->beaconTable;
        $localBeaconId = $this->beaconID;

        $isBeaconExistsBool = $this->verifyBeaconBoolean();

        if($isBeaconExistsBool)
        {
            $removeBeaconRowByIdQuery = "DELETE FROM $beaconTbl WHERE bid = '$localBeaconId'";
            $removeBeaconRowByIdExecute = $this->mysqli->query($removeBeaconRowByIdQuery);

            return $removeBeaconRowByIdExecute;
        }

        return false;
    }


    public function getFreeImage($amountConnected)
    {
        $dir = "./../../appTest/avatars/";
        $fileNamesArr = scandir($dir);
        if($amountConnected < sizeof($fileNamesArr))
        {
            return $fileNamesArr[$amountConnected];
        }
        return $fileNamesArr[sizeof($fileNamesArr)];
    }


}