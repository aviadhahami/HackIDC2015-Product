/**
 * Created by וSER on 4/8/2015.
 */



function HandleNewConnection(beaconID)
{
    var localUrl = "response.php";
    var localType = "get";
    var localData = {"rid":"0", "bid":beaconID};

    var uid;
    var imgName;
    var amountConnected;
    $.ajax({
        type: "GET",
        url: localUrl,
        data: "rid=" + localData.rid +"&bid="+localData.bid,
        success: function(response)
        {
           if(response.connection === 1)
           {
               //connection succeded

               uid = response.uid;
               imgName = response.img;
               amountConnected = response.amountConnected;
           }
        }

    });
}


function handleDisconnectionRequest(beaconID, clientID)
{
    var localUrl = "response.php";
    var localType = "get";
    var localData = {"rid":"1", "bid":beaconID};//rid == 1 --> Disconnection request

    $.ajax({
        type: "GET",
        url: localUrl,
        data: "rid=" + localData.rid +"&bid="+localData.bid + "&cid=" + clientID,
        success: function(response)
        {
            if(response.connection === "1")
            {
                //connection succeded

                uid = response.uid;
                imgName = response.img;
                amountConnected = response.amountConnected;
            }
            else if(response.connection === "-1")
            {

            }
        }

    });


}

