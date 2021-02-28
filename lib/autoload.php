<?php
spl_autoload_register(function($cls) {
    $parts = explode("\\", $cls);
    if (count($parts) === 3 &&
        $parts[0] === 'ostilton' &&
        $parts[1] === 'Twitch'
    ) {
        include_once strtolower($parts[2]).'.php';
    }
}, true);
