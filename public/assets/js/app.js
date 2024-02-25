const Echo = require('laravel-echo');
const io = require('socket.io-client');

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: 'http://localhost:6001',
    client: io,
    transports: ['websocket']
});
