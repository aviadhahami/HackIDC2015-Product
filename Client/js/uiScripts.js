$(document).ready(function() {

    //Overriding "ENTER" key for send msg
    $('.chat_input_box').on('keyup',function(e){
    	if (e.which == 13){
    		$('#sendMsg').click();
    		e.preventDefault();
    	}
    });
    $('.username_form').submit(function(){
    	window.clientName = $('#namePick').val().length < 4 ? chance.name() : $('#namePick').val();
    });

    var toggle = 0;
    $('#open-button').click(function(){
        if(toggle === 0){
            $(".gif_drawer").animate({
            top: "-=1600px",
            }, 1000 );
            toggle = 1;
        }
        else{
            $(".gif_drawer").animate({
            top: "+=1600px",
            }, 1000 );
            toggle = 0;
        }
    });
});
