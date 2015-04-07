<?php
/**
 * Created by PhpStorm.
 * User: Gal-Rettig
 * Date: 4/7/2015
 * Time: 7:13 PM
 */

include_once("Beacon.php");

$requestID = intval($_GET['rid']);//request id 0/1 connect/disconnect
$beaconID = $_GET['bid'];
$clientID = $_GET['cid'];


echo "</br> type of : </br>" .
    "rid = " . (gettype($requestID)) . "</br>" .
    "bid = " . (gettype($beaconID)) . "</br>" ;



if($requestID === 0 || $requestID === 1)
{
    $beaconHandler = new Beacon($beaconID,$requestID);
    if($requestID === 0)
    {
        $result = $beaconHandler->handleConnectionRequest();

        ////
        echo "</br> handleConnectionResult  = " . $result  ."|| </br>";
        ///
        if($result !== null)
        {
            echo parseResponseToJson($result);
        }
    }
    elseif($requestID === 1)
    {
        if($beaconHandler->handleDisconnectionRequest($clientID))
        {
            echo json_encode(["connection"=>"1"]);
        }
        else {
            echo json_encode(["connection"=>"-1"]);
        }
    }
}

// {connection:"-1/0/1""userId":"userid", "amountConnected":"??"}
function parseResponseToJson($resArr)
{
    return json_encode($resArr);
}





?>