<?php
spl_autoload_register(array(new LibraryLoader, 'loadClass'));
spl_autoload_register(array(new ApplicationLoader, 'loadClass'));

class LibraryLoader {
    public static function loadClass($class) {
        if (file_exists("Lib/{$class}.php")) {
            require_once "Lib/{$class}.php";
            return TRUE;
        }
    }
}
class ApplicationLoader {
    public function loadClass($class) {
        if (file_exists("App/{$class}.php")) {
            require_once "App/{$class}.php";
            return TRUE;
        }
    }
}
