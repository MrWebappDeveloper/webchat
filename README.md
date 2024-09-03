# How to install this package

First run below composer command to install package in the Laravel application:

`composer require mrwebappdeveloper/webchat`

Then run install artisan command:

`php artisan webchat:install`

Then add below changes on vite.config.js file:

```
import {fileURLToPath, URL} from 'node:url';
import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vuePlugin from "@vitejs/plugin-vue";
import {createRequire} from 'node:module';

const require = createRequire(import.meta.url);

export default defineConfig({
    plugins: [
        vuePlugin(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/vendor/webchat/js/register_form.js',
                'resources/vendor/webchat/js/admin.js',
                'resources/vendor/webchat/js/user.js',
                'resources/vendor/webchat/js/chat.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url))
        }
    }
});

```

After this, run npm install and npm run build for bundle assets:

```
npm install
npm run build
```

Open .env and Set config values which published by install command:

```
PUSHER_APP_ID=ABCDEF
PUSHER_APP_KEY=GHIJKL
PUSHER_APP_SECRET=TEST
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_CLIENT_PORT=6001
PUSHER_SCHEME=http
PUSHER_CLIENT_SCHEME=http
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_CLIENT_PORT="${PUSHER_CLIENT_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_CLIENT_SCHEME="${PUSHER_CLIENT_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

WEBCHAT_TELEGRAM_BOT_TOKEN=
WEBCHAT_TELEGRAM_CHANNEL_CHAT_ID=
```

Open config/app.php file of Laravel application and un-comment below line for enable socket broadcast provider:

`
App\Providers\BroadcastServiceProvider::class,
`

Add below line to your client-side layout blade page for display chat box to user:

`@include('vendor.webchat.user')`

Also you can visit webchat panel on `/webchat-panel` url. If you want check and control permission on access to this page, you can create a middleware for do this. As default this url
has not any authorization and anybody can access to this url.

Execute below command for processing dispatched listeners. This command should run for always. You can make a Linux service for this command:

`php artisan queue:work --queue=webchat`

Now you should serve websocket server. You can serve websocket server by execute blow command:

`php artisan websockets:server`

If you wanna to server socket server in product application and VPS server, you should use Supervisor Linux service to keep serving socket server for always. 
For more information to do this work, please visit information on below address:

https://beyondco.de/docs/laravel-websockets/basic-usage/starting#keeping-the-socket-server-running-with-supervisord

Also for enable SSL/TLS connection through websocket server, You can see information on below link:

https://beyondco.de/docs/laravel-websockets/basic-usage/ssl
