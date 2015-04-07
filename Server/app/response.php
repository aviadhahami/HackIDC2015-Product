<?php
/**
 * Created by PhpStorm.
 * User: Gal-Rettig
 * Date: 4/7/2015
 * Time: 7:13 PM
 */

include_once("Beacon.php");

$requestID = $_GET['rid'];//request id 0/1 connect/disconnect
$beaconID = $_GET['bid'];

if($requestID === 0 || $requestID === 1)
{
    $beaconHandler = new Beacon($beaconID,$requestID);
    if($requestID === 0)
    {
        $result = $beaconHandler->handleConnectionRequest();
        if($result !== null)
        {
            echo parseResponseToJson($result);
        }
    }
}

// {connection:"-1/0/1""userId":"userid", "amountConnected":"??"}
function parseResponseToJson($resArr)
{
    return json_encode($resArr);
}





?>