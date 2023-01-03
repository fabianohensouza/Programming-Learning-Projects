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

    public static function monthName(int $month)
    {
        if ($month < 1 || $month >12 ){
            return null;
        }

        $month_name = array(
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        );

        return $month_name[$month];
    }
}