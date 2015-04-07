
$(document).ready(function() {

/*    //global data
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
    });*/

var socket = io('http://localhost');



$('#sendMessage').click(function(){
    socket.emit('sendMessage',$('#chatInput').val());
    $('#chatInput').val('');
    return false;
});


socket.on('incomingMessage',function(msg){

/*    var HTMLString = '<div class="col-xs-12 user_msg">
    <div class="media message-box">
    <div class="media-left">
    <img class="media-object user-profile-in-chat" src="img/green.png" alt="general_id" style="width: 35px; height: 35px;">
    </div>
    <div class="media-body">
    <h4 class="media-heading timestamp" id="top-aligned-media">Jonny, 14:20<a class="anchorjs-link" href="#top-aligned-media"><span class="anchorjs-icon"></span></a></h4>
    <p>' + msg + '</p>
    </div>
    </div>
    </div>'

    $('#chatBody').append(HTMLString);*/
    console.log(msg);
});

/*$('form').submit(function(){
    socket.emit('sendMessage', $('#m').val());
    $('#m').val('');
    return false;
});

socket.on('incomingMessage', function(msg){
    $('#messages').append($('<li>').text(msg));
});*/

  //DC notification
  io.on('connection', function(socket){
    console.log('a user connected');
    socket.on('disconnect', function(){
        console.log('user disconnected');
    });

});
});