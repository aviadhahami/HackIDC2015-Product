$(document).ready(function() {
    console.log('got JQ on via integration');

    //global data
    var url = 'https://hackidc2015.imrapid.io/message';
    var roomID = 'david';
    var projectName = 'hackidc2015';


    //message output
    $('#send').click(function(){
        var chatMsg = $('#textBox').val();
        var msg = {
            message : chatMsg,
            room : roomID
        };
        console.log('sending message object.. msg is' + chatMsg);

        $.post(url,msg,function(data,status){
            console.log('data: ' + data + 'status : ' + status + 'from the POST');
        });
    });


    //message feed
    var io = createIO(projectName, roomID);

    io.on('newMsg', function(data) {
        alert(data);
        console.log(data);
    });
});