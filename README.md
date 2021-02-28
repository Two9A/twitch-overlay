# ostilton's Twitch overlay

As used on ostilton's Twitch streams:

![Capture example](http://c64clicker.com/dump/20210228-twitch.gif)

This repository consists of three parts:

- index.php: The overlay itself, as served to OBS's BrowserSource;
- websocket: Websocket server script, to run locally on the webserver proxying the subscription receiver;
- event.php: Notification receiver.

The websocket proxy can be left running between runs of OBS.

## Configuration

Place the contents of this repository in a web-accessible directory, for example if your webserver is configured to serve from /var/www: /var/www/twitch-overlay/

First ensure that your webserver is configured to serve over TLSv1.2, then configure to forward requests from /twitch-ws to the Websocket server. For example with nginx:

```
location /twitch-ws {
    access_log off;
    proxy_pass http://localhost:16384;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header Host $host;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header Connection "upgrade";
    rewrite /twitch-ws/(.*) /$1 break;
    proxy_redirect off;
    proxy_read_timeout 99999s;
}
```

Copy config/twitch.json.example to config/twitch.json and add your registered app's client ID and secret, as well as updating the wsUri and eventUri.
