
$(document).ready(function() {

    //Managment server integration module//

    var major = '';
    var minor = '';
    //two vars above this should come from beacon integration


    var serverDomain = 'argov.freevar.com/';
    var managmentServerUrl = serverDomain + 'serverTest/app/response.php';
    var method = 'GET';
    var connectionFlag = '0'; //0 is income, 1 is disconnect
    var bid = major+ '.' + minor;
    window.cid = ''; //recieved by server and shouold be sent to server upon DC

    //preping global vars
    var chatAmount ='';
    var userImg = '';
    var localID = '';
    var connectionStatus = '';

    window.initiatePrimaryConnection = function(){
    var getReqDataString = 'rid=' + connectionFlag + '&bid=' + bid; //connection GET request string
    $.getJSON(managmentServerUrl,getReqDataString).done(function(res){
      consosle.log(res);
      connectionStatus = res.connection;
      window.cid = res.cid;
      chatAmount = res.amount;
      userImg = res.img;
      localID = res.localID; //Beacon ID @ the server
    }).fail(function(e){
      alert('Oops ! problems ! reload page!');
      console.log(e);
    });

  };
    //end of server integration module//


    //global data
    var url = 'https://hackidc2015.imrapid.io/message';
    var roomID = bid + 'BeaconRoom';
    var projectName = 'hackidc2015'; //do not change ! server critical (RapidAPI)
    var chatMsg ='';


    //////////////
    // Pre Chat //
    //////////////



    console.log(window.clientName);
    console.log('got JQ on via integration');

    var userID = localID;


    //message output
    $('#sendMsg').click(function(){

      chatMsg = $('#chatMsg').val();
      $('#chatMsg').val('');
      var msg = {
        name: window.clientName,
        message : chatMsg,
        room : roomID,
        userID : userID,
        userImg : userImg
      };
      console.log('sending message object.. msg is ',msg);
      var outputHTMLString = generateCurrentBlob(msg);

      $('.chat_body').append(outputHTMLString);
      ScrollFix();
      $.post(url,msg,function(data,status){
        console.log('data: ' + data + 'status : ' + status + 'from the POST');
        if (status === "success") {
          $('.timestamp').append('V');
        }
      });
    });


    //message feed
    var io = createIO(projectName, roomID);

    function generateCurrentBlob(data){

     var d = new Date();
     var hours = d.getHours() < 10 ? '0' + d.getHours() : d.getHours();
     var minutes = d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes();
     var dateString = hours + ':' + minutes;

     
     var d = new Date();
     var hours = d.getHours() < 10 ? '0' + d.getHours() : d.getHours();
     var minutes = d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes();
     var dateString = hours + ':' + minutes;
     var _htmlTemplateString = '<div class="col-xs-12 user_msg"><div class="media message-box"><div class="media-left"><img class="media-object user-profile-in-chat" src=' + data.userImg +' alt="general_id" style="width: 35px; height: 35px;"></div><div class="media-body"><h4 class="media-heading timestamp" id="top-aligned-media">'+ data.name+', ' + dateString+'<a class="anchorjs-link" href="#top-aligned-media"><span class="anchorjs-icon"></span></a></h4><p>'+ data.message+'</p></div></div></div>';
     return _htmlTemplateString;

   };

   function ScrollFix() {
    $(".chat_body").scrollTop($(".chat_body")[0].scrollHeight);
  }

  io.on('newMsg', function(data) {
    var outputHTMLString = generateCurrentBlob(data);
   // alert(data);

   $('.chat_body').append(outputHTMLString);
   ScrollFix();

 });
});

