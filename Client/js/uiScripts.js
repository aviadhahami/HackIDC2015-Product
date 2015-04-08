$(document).ready(function() {

    //Overriding "ENTER" key for send msg
    $('#chatMsg').on('keyup',function(e){
    	if (e.which == 13){
    		$('#sendMsg').click();
    		e.preventDefault();
    	}
    });
    $('.username_form').submit(function(){
    	window.clientName = $('#namePick').val().length < 4 ? chance.name() : $('#namePick').val();
    });
});