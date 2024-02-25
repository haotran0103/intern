import Echo from 'laravel-echo';
import io from 'socket.io-client';

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: 'http://localhost:6001',
    client: io,
    transports: ['websocket']
});
