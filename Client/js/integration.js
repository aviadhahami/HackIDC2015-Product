$(document).ready(function(){
    var socket = io();
    $('myButt').click(function(e){
        alert(typeof e);
    });
});
