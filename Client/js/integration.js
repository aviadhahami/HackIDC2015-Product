$(document).ready(function() {

    //global data
    var url = "https://hackidc2015.imrapid.io/message";
    var roomID = "2";
    var projectName = "hackidc2015";



    //message output
    $('#myButt').click(function(e) {
        var message = $('#chatInput').val();
        var clientData = {
            room: roomID,
            UID: '2',
            message: message
        };
        $.post(url, clientData, function(data) {
            console.log(data);
        });
    });


    //message feed
    var io = createIO(projectName, roomID);
    io.on('newMessage', function(data) {
        console.log(data.message);
    });
});