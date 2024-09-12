import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster:import.meta.env.VITE_PUSHER_APP_CLUSTER,
    wsHost: import.meta.env.VITE_PUSHER_HOST,
    wsPort: import.meta.env.VITE_PUSHER_CLIENT_PORT,
    wssPort: import.meta.env.VITE_PUSHER_CLIENT_PORT,
    enabledTransports: ['ws', 'wss'],
    forceTLS: import.meta.env.VITE_PUSHER_CLIENT_SCHEME === 'https',
    disableStats: true,
});
