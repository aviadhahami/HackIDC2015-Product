var express = require('express');
var app = express();
//express config


app.use(express.static("public"));

var http = require('http').Server(app);
var io = require('socket.io')(http);

app.get('/', function(req, res){
	res.sendfile('index.html');
});

io.on('connection', function(socket){
	socket.on('sendMessage', function(msg){
		io.emit('incomingMessage', msg);
	});
});

http.listen(3000, function(){
	console.log('listening on *:3000');
});