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
    $beaconHandler = new Beacon();
}







?>