<?php
namespace ostilton\Twitch;

abstract class Module {
    protected $classname;

    public function __construct() {
        $cls = explode("\\", get_class($this));
        $this->classname = strtolower($cls[3]);
    }

    public function getName() {
        return $this->classname;
    }

    public function getBase() {
        return "modules/{$this->classname}/";
    }

    public static function factory() {
        $modules = [];
        foreach (glob('modules/*') as $dir) {
            $dir = basename($dir);
            $classname = "\\ostilton\\Twitch\\Modules\\" . ucfirst($dir);
            $modules[$dir] = new $classname;
        }
        return $modules;
    }
}
