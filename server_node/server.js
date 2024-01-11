var server = require('http').Server();

var io = require('socket.io')(server);

var Redis = require('ioredis');
var redis = new Redis(6666);

redis.subscribe('chat', function (err, count) {
});
redis.on('message', function (channel, message) {
    console.log(channel, message);
    message = JSON.parse(message);
    io.emit(message.event, message.data);
});

server.listen(6001);
