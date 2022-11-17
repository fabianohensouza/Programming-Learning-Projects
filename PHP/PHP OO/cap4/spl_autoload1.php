<?php
spl_autoload_register( function($class) {
    if (file_exists("App/{$class}.php")) {
        require_once "App/{$class}.php";
        return TRUE;
    }
});

var_dump(new Pessoa);
