<?php
include_once 'bootstrap.php';
use ostilton\Twitch\Eventsub;

$postbody = file_get_contents('php://input');
$ev = new Eventsub();
if(!$ev->checkSig(
    $_SERVER['HTTP_TWITCH_EVENTSUB_MESSAGE_SIGNATURE'],
    $_SERVER['HTTP_TWITCH_EVENTSUB_MESSAGE_ID'],
    $_SERVER['HTTP_TWITCH_EVENTSUB_MESSAGE_TIMESTAMP'],
    $postbody
)) {
    header('HTTP/1.1 403 Forbidden');
    return;
}

$ret = $ev->handleCallback(
    $_SERVER['HTTP_TWITCH_EVENTSUB_MESSAGE_TYPE'],
    $postbody
);
if (is_string($ret)) echo $ret;
