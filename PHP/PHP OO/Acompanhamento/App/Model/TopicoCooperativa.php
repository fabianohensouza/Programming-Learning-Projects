<?php
use Sisac\Database\Record;
use Sisac\Database\Criteria;
use Sisac\Database\Repository;

class TopicoCooperativa extends Record
{
    const TABLENAME = 'topico_cooperativa';

    public function getPontObtida()
    {
        $pont_obtida = 0;

        $topico = Topico::find($this->id_topico);
        $itens = $topico->getItens();

        foreach ($itens as $item) {

            $repository = new Repository('CoopItemStatus');
            $criteria = new Criteria();
            $criteria->add('id_coop', '=', $_GET['id']);
            $criteria->add('id_item', '=', $item->id);
            $lista = $repository->load($criteria);

            foreach ($lista as $obj) {
                $pont_obtida += $obj->getPontos();
            }
            
        }

        return $pont_obtida;

    }

    public function getEvolucao()
    {
        $topico = Topico::find($this->id_topico);
        $max = $topico->getPontMax();
        $obtida = $this->getPontObtida();

        if ($max != 0) {
            $this->evolucao = number_format((($obtida / $max) * 100), 2);
            return $this->evolucao;
        }

        $this->evolucao = 0;

        return $this->evolucao;

    }
}


