
$(document).ready(function() {

    //Managment server integration module//

    var major = '125';
    var minor = '670';
    //two vars above this should come from beacon integration

    var serverDomain = 'http://argov.webuda.com/';
    var managmentServerUrl = serverDomain + 'Server/app/response.php?callback=?';

    var method = 'GET';
    var connectionFlag = '0'; //0 is income, 1 is disconnect
    var bid = major+ '.' + minor;
    window.cid = ''; //recieved by server and shouold be sent to server upon DC

    //preping global vars
    var chatAmount ='';
    var userImg = '';
    var localID = '';
    var connectionStatus = '';
    var userID = '';


    //Primary connection logic
    window.initiatePrimaryConnection = function(){
    var getReqDataString = 'rid=' + connectionFlag + '&bid=' + bid + '&userName=' + window.clientName; //connection GET request string
    $.getJSON(managmentServerUrl,getReqDataString).done(function(res){
      console.log(res);
      connectionStatus = res.connection;
      window.cid = res.cid;
      chatAmount = res.amount;
      userImg = 'avatars/' + res.img;
      localID = res.localID; //Beacon ID @ the server

      //chat members population
      var usersArray = res.onlineUsers;
      var onlineUsersList = $('.usersList');
      //STUB
      usersArray = [{cid:'asdf',clientName:'David'},{cid:'w45j',clientName:'Rubi'},{cid:'kjw4',clientName:'Jonny'}];
      //
      usersArray.forEach(function(user){
        var onlineUserHtmlStub = '<li data-userid=' + user.cid + '><a href="#" class="user"><img src="avatars/yellow.png"><span class="user_name">' +user.clientName + '</span></a></li>';
        onlineUsersList.append(onlineUserHtmlStub);
      });
      //end of members population

      //shout to RapidAPI for new user
      var newUserURL = 'hackidc2015.imrapid.io/users';
      var requestObject = {
        cid : window.cid,
        clientName : window.clientName 
      };
      $.post(newUserURL,requestObject,function(){
        console.log('send to Rapi of new user')
      });
      //end of RAPI shout


    }).fail(function(e){
      alert('Oops ! problems!, stub generated');
      console.log(e);
      connectionStatus = 0;
      window.cid = 0;
      chatAmount = 5;
      userImg = 'avatars/cyan.png';
      localID = 0;
    }).always(function(){
      userID = window.cid === '' ? 0 : window.cid;
    });

  };
    //end of server integration module//


    //global data
    var url = 'https://hackidc2015.imrapid.io/message';
    var roomID = bid + '_LBC';
    var projectName = 'hackidc2015'; //do not change ! server critical (RapidAPI)
    var chatMsg ='';




    console.log(window.clientName);
    console.log('got JQ on via integration');



    //message output
    $('#sendMsg').click(function(){

      chatMsg = $('#chatMsg').val();
      $('#chatMsg').val('');
      var msg = {
        name: window.clientName,
        message : chatMsg,
        room : roomID,
        userID : userID,
        userImg : userImg,
        msgType : 'txt'
      };


      console.log('sending message object.. msg is ',msg);
      var outputHTMLString = generateCurrentBlob(msg,true);
      $('.chat_body').append(outputHTMLString);
      ScrollFix();

      $.post(url,msg,function(data,status){
        console.log('data: ' + data + 'status : ' + status + 'from the POST');


        if (status === "success") {
          //$('.timestamp').append('Recieved');
          var currentMsg = $('h4[data-userID=' + userID + ']');
          currentMsg.append('Recieved');
          currentMsg.removeAttr('data-userID');
        }
      });
    });

    //img message output
    $('.gif_drawer img').on('click',function(){
      chatMsg = $(this).attr('src');
      $('#chatMsg').val('');
      //close drawer
      $('#open-button').click();
      var msg = {
        name: window.clientName,
        message : chatMsg,
        room : roomID,
        userID : userID,
        userImg : userImg,
        msgType : 'img'
      };


      console.log('sending message object.. msg is ',msg);
          // CHECK ME ! 
        //code for local double messaging
        var outputHTMLString = generateCurrentBlobForImage(msg,true);
        $('.chat_body').append(outputHTMLString);
        ScrollFix();


        //end of local double messaging

        $.post(url,msg,function(data,status){
          console.log('data: ' + data + 'status : ' + status + 'from the POST');

            //mark V if recieved by server
            if (status === "success") {
              var currentMsg = $('h4[data-userID=' + userID + ']');
              currentMsg.append('Recieved');
              currentMsg.removeAttr('data-userID');
            }
          });

      });

$('#roomName').text(roomID);
$('#roomTag').text('@' + roomID);
    //message feed
    var io = createIO(projectName, roomID);

    function generateCurrentBlob(data,flag){

      var d = new Date();
      var hours = d.getHours() < 10 ? '0' + d.getHours() : d.getHours();
      var minutes = d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes();
      var dateString = hours + ':' + minutes;
      
      if (flag){
        //if true we need to add data attr
        var _htmlTemplateString = '<div class="col-xs-12 user_msg"><div class="media message-box"><div class="media-left"><img class="media-object user-profile-in-chat" src="' + data.userImg +'" alt="general_id" style="width: 35px; height: 35px;"></div><div class="media-body"><h4 class="media-heading timestamp" id="top-aligned-media" data-userID=' + userID +'>'+ data.name+', ' + dateString+'<a class="anchorjs-link" href="#top-aligned-media"><span class="anchorjs-icon"></span></a></h4><p>'+ data.message+'</p></div></div></div>';

      }else{
        //regular generation  
        var _htmlTemplateString = '<div class="col-xs-12 user_msg"><div class="media message-box"><div class="media-left"><img class="media-object user-profile-in-chat" src="' + data.userImg +'" alt="general_id" style="width: 35px; height: 35px;"></div><div class="media-body"><h4 class="media-heading timestamp" id="top-aligned-media">'+ data.name+', ' + dateString+'<a class="anchorjs-link" href="#top-aligned-media"><span class="anchorjs-icon"></span></a></h4><p>'+ data.message+'</p></div></div></div>';

      }
      return _htmlTemplateString;

    };

    function generateCurrentBlobForImage(data,flag) {
     // console.log('img blob');
     var d = new Date();
     var hours = d.getHours() < 10 ? '0' + d.getHours() : d.getHours();
     var minutes = d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes();
     var dateString = hours + ':' + minutes;

     if (flag){
      var _htmlTemplateString = '<div class="col-xs-12 user_msg"><div class="media message-box"><div class="media-left"><img class="media-object user-profile-in-chat" src="' + data.userImg +'" alt="general_id" style="width: 35px; height: 35px;"></div><div class="media-body"><h4 class="media-heading timestamp" id="top-aligned-media" data-userID=' + userID+ '>'+ data.name+', ' + dateString+'<a class="anchorjs-link" href="#top-aligned-media"><span class="anchorjs-icon"></span></a></h4><p><img src="'+ data.message+'"/></p></div></div></div>';

    }else{
      var _htmlTemplateString = '<div class="col-xs-12 user_msg"><div class="media message-box"><div class="media-left"><img class="media-object user-profile-in-chat" src="' + data.userImg +'" alt="general_id" style="width: 35px; height: 35px;"></div><div class="media-body"><h4 class="media-heading timestamp" id="top-aligned-media">'+ data.name+', ' + dateString+'<a class="anchorjs-link" href="#top-aligned-media"><span class="anchorjs-icon"></span></a></h4><p><img src="'+ data.message+'"/></p></div></div></div>';

    }
    return _htmlTemplateString;
  }

  function ScrollFix() {
    $(".chat_body").scrollTop($(".chat_body")[0].scrollHeight);
  }

  io.on('newMsg', function(data) {
    //console.log('data is ', JSON.stringify(data), 'data string is', data + '');
    //console.log(dataArr);
    console.log(data.msgType);
    console.log(data.userID);
    if (! (data.userID == userID)){
      if (data.msgType === 'txt'){
        var outputHTMLString = generateCurrentBlob(data,false);
      }else{
        var outputHTMLString = generateCurrentBlobForImage(data,false);
      }
   // alert(data);

   $('.chat_body').append(outputHTMLString);
   ScrollFix();
 }

});

  io.on('newUser',function(data){
    console.log('newUser event data',data);
    var onlineUserHtmlStub = '<li data-userid=' + data.cid + '><a href="#" class="user"><img src="avatars/yellow.png"><span class="user_name">' + data.clientName + '</span></a></li>';
  });


});

