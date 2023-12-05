var io = require('socket.io')(6001)
console.log('connected port 6001');
io.on('error', function(socket){
    console.log('error');
})
io.on('connection', function(socket){

})
var Redis = require('ioredis');
var redis = Redis(6666);
redis.psubscribe('*',function(error,count){

})
redis.on('pmessage', function(partner, channel, message){
    console.log(partner);
    console.log(channel);
    console.log(message);
    message = JSON.parse(message)
    io.emit(channel+":"+message.event, message.data.message)
    console.log('sent');
})