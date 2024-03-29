<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Sao_Paulo');

if (version_compare(PHP_VERSION, '7.0.0') == -1)
{
    die('A versão mínima do PHP para rodar esta aplicação é: 7.0.0');
}

// Lib loader
require_once 'Lib/Sisac/Core/ClassLoader.php';
$al= new Sisac\Core\ClassLoader;
$al->addNamespace('Sisac', 'Lib/Sisac');
$al->register();

// App loader
require_once 'Lib/Sisac/Core/AppLoader.php';
$al= new Sisac\Core\AppLoader;
$al->addDirectory('App/Control');
$al->addDirectory('App/Model');
$al->register();

// Vendor
$loader = require 'vendor/autoload.php';
$loader->register();

$template = file_get_contents('App/Templates/template.html');
$content = '';
$class   = 'Home';

if ($_GET)
{
    $class = $_GET['class'];
    if (class_exists($class))
    {
        try
        {
            $pagina = new $class;
            ob_start();
            $pagina->show();
            $content = ob_get_contents();
            ob_end_clean();
        }
        catch (Exception $e)
        {
            $content = $e->getMessage() . '<br>' .$e->getTraceAsString();
        }
    }
    else
    {
        $content = "Class <b>{$class}</b> not found"; 
    }
}
$output = str_replace('{content}', $content, $template);
$output = str_replace('{class}',   $class, $output);
echo $output;
