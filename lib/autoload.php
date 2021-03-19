<?php
spl_autoload_register(function($cls) {
    $parts = explode("\\", $cls);
    if (count($parts) === 3 &&
        $parts[0] === 'ostilton' &&
        $parts[1] === 'Twitch'
    ) {
        include_once strtolower($parts[2]).'.php';
    } else if (count($parts) === 4 &&
        $parts[0] === 'ostilton' &&
        $parts[1] === 'Twitch' &&
        $parts[2] === 'Modules'
    ) {
        include_once 'modules/'.strtolower($parts[3]).'/module.php';
    }
}, true);
