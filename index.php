<?php
include_once 'bootstrap.php';
use ostilton\Twitch\Config;
use ostilton\Twitch\Eventsub;
use ostilton\Twitch\Module;

$modules = Module::factory();
$sub = new Eventsub();
$sub->refreshSubscription();
?>
<!doctype html>
<html>
 <head>
  <link rel="stylesheet" href="index.css">
  <?php foreach ($modules as $module) { ?>
   <link rel="stylesheet" href="<?= $module->getBase() ?>module.css">
  <?php } ?>
  <script>
window.__CONFIG = {
    wsUri: '<?= Config::get('wsUri') ?>'
};
  </script>
  <script src="index.js"></script>
  <?php foreach ($modules as $module) { ?>
   <script src="<?= $module->getBase() ?>module.js"></script>
  <?php } ?>
 </head>
 <body>
  <div class="body">
   <?php foreach ($modules as $module) { ?>
    <?php include_once $module->getBase() . 'template.php'; ?>
   <?php } ?>
  </div>
 </body>
</html>
