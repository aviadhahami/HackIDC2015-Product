<?php
/**
 * Created by PhpStorm.
 * User: Gal-Rettig
 * Date: 4/7/2015
 * Time: 7:13 PM
 */

//
//include_once("Beacon.php");

include_once("beaconNew.php");
$cb = $_GET['callback'];
$requestID = intval($_GET['rid']);
$beaconID = $_GET['bid'];
$name = $_GET['userName'];//new
$clientID = $_GET['cid'];//Only upon disconnection request



//  0 - new user , 1 disconnect a user
if($requestID === 0 || $requestID === 1)
{
    $beaconHandler = new beaconNew($beaconID,$requestID,$name);
    if($requestID === 0)
    {
        $result = $beaconHandler->handleConnectionRequest();


        if($result !== null)
        {
            echo $cb ."(". parseResponseToJson($result) .")";
        }
    }
    elseif($requestID === 1)
    {
        if($beaconHandler->handleDisconnectionRequest($clientID, intval($beaconID)))
        {
            echo $cb . "(" . json_encode(array("connection"=>"1")).")";
        }
        else {
            echo $cb ."(" . json_encode(array("connection"=>"-1")).")";
        }
    }
}

// {connection:"-1/0/1""userId":"userid", "amountConnected":"??"}
function parseResponseToJson($resArr)
{
    return json_encode($resArr);
}





?>