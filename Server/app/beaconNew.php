<?php
/**
 * Created by PhpStorm.
 * User: וSER
 * Date: 4/7/2015
 * Time: 7:30 PM
 */
class beaconNew {
    public $beaconID;//Represent an MAC address
    public $responseID;
    public $mysqli;
    public $beaconTable;
    public $clientTable;
    public $beaconIndex;

    public $name;//new

    function __construct($beaconID, $responseID, $name)
    {
        $this->beaconID = $beaconID;
        $this->responseID = $responseID;

        $this->mysqli = new mysqli("mysql14.000webhost.com","a5016316_argov","argov123","a5016316_argov");
        $this->beaconTable = "beacon";
        $this->clientTable = "clients";
        $this->beaconIndex = 0;

        $this->name = $name;//new
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




    /////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////

    private function generateClientId($clientArr)
    {
        $size = $this->getAmountConnected($clientArr);
        $beaconShrink = substr($this->beaconID,0,3);
        return $beaconShrink . $size . ($this->get_rand_str(5));
    }


    private function getAllUserName($clientArr)
    {
        $namesArray = array();
        if($clientArr === null)
        {
            return null;
        }

        for($i = 0; $i < sizeof($clientArr); $i++)
        {
            $currentObjectInArray = $clientArr[$i];
            $name = $currentObjectInArray->name;
            $localCid = $currentObjectInArray->uid;
            $imageSrc = $currentObjectInArray->image;
            $key = "'" . $i . "'";
            $namesArray[$key] = array("name"=>$name, "clientId"=>$localCid,"userImg"=>$imageSrc);
        }

        return $namesArray;

    }


    private function array_push_assoc($array, $key, $value){
        $array[$key] = $value;
        return $array;
    }


    public function handleConnectionRequest()
    {

        $userSet = $this->isBeaconExists();

        if($userSet === null)
        {
            return array("connection"=>"-1");
        }
        //
        $amountConnect = $this->getAmountConnected($userSet);
        $imgSrc = $this->getFreeImage($amountConnect);
        //
        $clientId = $this->addNewClient($userSet, $imgSrc);

        if($clientId === null)
        {
            return array("connection"=>"-1");
        }


        $localBeaconIndex = $this->beaconIndex;

        $namesArray = $this->getAllUserName($userSet);

        return array("connection"=>"1",'cid'=>$clientId, 'amount'=> $amountConnect, "img"=>$imgSrc, "localID"=>$localBeaconIndex, "onlineUsers"=>$namesArray);
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


    private function addNewClient($clientArr, $imgSrc)
    {
        $localBeaconId = $this->beaconIndex;//Changed to work with new table Structure
        $localClientTable = $this->clientTable;

        $localNameToAdd = $this->name;//new

        $clientId = $this->generateClientId($clientArr);
        $addNewClientQuery = "INSERT INTO $localClientTable (bid, uid, name, image) VALUES ($localBeaconId, '$clientId', '$localNameToAdd', '$imgSrc')";//new
        $addNewClientExecute = $this->mysqli->query($addNewClientQuery);
        if($addNewClientExecute)
        {
            return $clientId;
        }
        return null;
    }

    public function getFreeImage($amountConnected)
    {
        $dir = "./../../Client/avatars";
        $fileNamesArr = scandir($dir);
        if($amountConnected + 2 < sizeof($fileNamesArr))
        {
            return $fileNamesArr[$amountConnected + 2];
        }
        return $this->getFreeImage($amountConnected % sizeof($fileNamesArr));
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
    public function handleDisconnectionRequest($clientID, $beaconID)
    {
        //echo "</br> handleDisconnect berifyBeaconbool = " .$bool ."</br>";
        $this->beaconIndex = $beaconID;
        $bool = $this->verifyBeaconBooleanWithID();
        if($bool)
        {
            return $this->removeClientFromDB($clientID, $beaconID);//Added a parameter
        }
        return false;
    }
    //Changed added one more parameter!!!
    private function removeClientFromDB($clientID,$beaconID)
    {
        //$localBeaconId = $this->beaconID;
        $localBeaconId = $beaconID;//Meant to work correctly
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
    public function verifyBeaconBooleanWithID()
    {
        $localBeacon = $this->beaconIndex;
        $beaconTbl = $this->beaconTable;
        $beaconExistsQuery = "SELECT * FROM $beaconTbl WHERE id = $localBeacon";
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


    private function get_rand_str($length)
    {
        $chars = array_merge(range('a','z'), range('A','Z'), array('!','%','*'));
        $length = intval($length) > 0 ? intval($length) : 16;
        $max = count($chars) - 1;
        $str = "";

        while($length--) {
            shuffle($chars);
            $rand = mt_rand(0, $max);
            $str .= $chars[$rand];
        }
        return $str;
    }



}