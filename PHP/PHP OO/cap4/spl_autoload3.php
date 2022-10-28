<?php
$al = new ApplicationLoader;
$al->register();

class ApplicationLoader {
    public function register() {
        spl_autoload_register(array($this, 'loadClass'));
    }
    public function loadClass($class) {
        if (file_exists("App/{$class}.php")) {
            require_once "App/{$class}.php";
            return TRUE;
        }
    }
}
