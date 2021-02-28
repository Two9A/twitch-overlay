<?php
include_once 'bootstrap.php';
use ostilton\Twitch\Config;
use ostilton\Twitch\Eventsub;
$sub = new Eventsub();
$sub->refreshSubscription();
?>
<!doctype html>
<html>
 <head>
  <link rel="stylesheet" href="index.css">
  <script src="index.js"></script>
  <script>
window.__CONFIG = {
    wsUri: '<?= Config::get('wsUri') ?>'
};
  </script>
 </head>
 <body>
  <div class="body">
   <ul>
     <li><strong>twitch.tv/<?= Config::get('userName') ?></strong></li>
   </ul>
   <ul>
    <li id="uptime"></li>
   </ul>
   <ul id="events"></ul>
  </div>
 </body>
</html>
