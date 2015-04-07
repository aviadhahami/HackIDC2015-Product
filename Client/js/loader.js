
//$(document).ready(function () {
$(".username_form").submit(function ( event ){
    event.preventDefault();
    $(".username").fadeOut();
    $(".loader").fadeIn();
    setTimeout( function(){
        $('.shader').fadeOut();
    },0005);

});
