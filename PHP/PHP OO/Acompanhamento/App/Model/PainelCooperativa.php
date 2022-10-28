<?php
use Sisac\Database\Record;
use Sisac\Database\Criteria;
use Sisac\Database\Repository;

class PainelCooperativa extends Record
{
    const TABLENAME = 'painel_cooperativa';

    public function getTopicos($status = 1)
    {        
        $repository = new Repository('Topico');
        $criteria = new Criteria();
        $criteria->add('id_status', '=', $status);
        return $repository->load($criteria);
    }

    public function getPontMax()
    {
        $topicos = $this->getTopicos();
        $pont_max = 0;

        foreach ($topicos as $topico) {
            $pont_max += $topico->getPontMax();
        }

        return $pont_max;
    }

    public function getPontObtida()
    {
        $topicos = $this->getTopicos();
        $pont_obtida = 0;

        foreach ($topicos as $topico) {
            $topico_cooperativa = $topico->getTopicoCooperativa($this->id_coop);
            $pont_obtida += $topico_cooperativa->getPontObtida();
        }
        
        return $pont_obtida;
    }

    public function getEvolucao()
    {
        $pont_max = $this->getPontMax();
        $pont_obtida = $this->getPontObtida();

        if ($pont_max != 0) {
            $this->evolucao = number_format((($pont_obtida / $pont_max) * 100), 2);
            return $this->evolucao;
        }

        $this->evolucao = 0;

        return $this->evolucao;
    }
}


