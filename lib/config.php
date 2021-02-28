<?php
namespace ostilton\Twitch;

class Config {
    const CONFIG_PATH = 'config/twitch.json';
    protected static $vars = [];

    protected static function read() {
        $f = file_get_contents(self::CONFIG_PATH);
        if (!$f) {
            throw new Exception('Cannot read configuration at: '.self::CONFIG_PATH);
        }
        $j = json_decode($f, true);
        if (!$j) {
            throw new Exception('Malformed JSON in: '.self::CONFIG_PATH);
        }
        self::$vars = $j;
    }

    public static function get($key) {
        if (!count(self::$vars)) {
            self::read();
        }
        return isset(self::$vars[$key]) ? self::$vars[$key] : '[UNSET]';
    }
}
