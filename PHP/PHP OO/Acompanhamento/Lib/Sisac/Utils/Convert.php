<?php
namespace Sisac\Utils;

use Sisac\Widgets\Form\Date;

/**
 * Classe suporte para tags
 * @author Fabiano Souza
 */
class Convert
{

    public static function dateToPtBr(String $date)
    {
        return date('d/m/Y',strtotime($date));
    }
}