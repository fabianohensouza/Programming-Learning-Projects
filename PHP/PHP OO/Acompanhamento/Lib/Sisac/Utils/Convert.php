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
            return false;
        }

        $month_name = array(
            'Janeiro',
            'Fevereiro',
            'Março',
            'Abril',
            'Maio',
            'Junho',
            'Julho',
            'Agosto',
            'Setembro',
            'Outubro',
            'Novembro',
            'Dezembro'
        );

        return $month_name[$month];
    }
}