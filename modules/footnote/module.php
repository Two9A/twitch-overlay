<?php
namespace ostilton\Twitch\Modules;
use ostilton\Twitch\Module;
use ostilton\Twitch\Config;

class Footnote extends Module {
    static public function send($body) {
        \Ratchet\Client\connect('ws://localhost:'.Config::get('wsPort'))->then(function($conn) use($body) {
            $conn->send(json_encode([
                'type' => 'NOTE',
                'content' => $body,
            ], JSON_UNESCAPED_SLASHES));
            $conn->close();
        });
    }
}
