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

    function generateCurrentBlob(data){
     var d = new Date();
     var dateString = (d.getHours()+1) + ':' + (d.getMinutes());
     var _htmlTemplateString = '<div class="col-xs-12 user_msg"><div class="media message-box"><div class="media-left"><img class="media-object user-profile-in-chat" src="img/green.png" alt="general_id" style="width: 35px; height: 35px;"></div><div class="media-body"><h4 class="media-heading timestamp" id="top-aligned-media">'+ data.name +',' + dateString+'<a class="anchorjs-link" href="#top-aligned-media"><span class="anchorjs-icon"></span></a></h4><p>'+ data.msg+'</p></div></div></div>';
    };


     io.on('newMsg', function(data) {
        var outputHTMLString = generateCurrentBlob(data);
        alert(data);
        console.log(data,outputHTMLString);
        //$('.chat_body').append(outputHTMLString);
    });
});