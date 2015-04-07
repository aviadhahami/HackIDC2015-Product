$(document).ready(function() {
    var url = "https://hackidc2015.imrapid.com/message";
    
    //message output
    $('#myButt').click(function(e) {
        var message = $('#chatInput').val();
        var clientData = {
            room: '2',
            UID: '2',
            message: message
        };
        $.post(url, clientData, function(data) {
            console.log(data);
        });
    });

    //message feed
    io.on('newMessage', function(data) {
        $('<div />',{
            class : 'col-xs-12 user_msg',
            text : data.message,
        }).appendTo('#chatBody');
    });
});