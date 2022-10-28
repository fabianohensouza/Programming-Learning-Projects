<?php

use Sisac\Database\Criteria;
use Sisac\Database\Record;
use Sisac\Database\Repository;

class Topico extends Record
{
    const TABLENAME = 'topicos';
    private $status;
    private $itens;
    
    public function getStatus()
    {
        if (empty($this->status))
        {
           $this->status = Status::find($this->id_status);
        }
        
        return $this->status->nome;
    }
    
    public function getItens($status = 1)
    {
        if ($this->id_status != 1)
        {
            return '-';
        }
        
        $repository = new Repository('Item');
        $criteria = new Criteria();
        $criteria->add('id_topico', '=', $this->id);
        $criteria->add('id_status', '=', $status);
        $this->itens = $repository->load($criteria);
      
        return $this->itens;
    }
    
    public function getNumItens($status = 1)
    {
        return count((array) $this->getItens($status));
    }
     
    public function getPontMax()
    {
        if ($this->id_status != 1)
        {
            return '-';
        }

        $this->getItens();
        $this->pont_max = 0;

        foreach ($this->itens as $item) {
            $this->pont_max += $item->multiplicador * 4;
        }
        
        return $this->pont_max;
    }

    public function getTopicoCooperativa($coop)
    {
        $repository = new Repository('TopicoCooperativa');
        $criteria = new Criteria();
        $criteria->add('id_coop', '=', $coop);
        $criteria->add('id_topico', '=', $this->id);
        $topicos = $repository->load($criteria);
        
        if ($topicos) {
                
            foreach ($topicos as $topico) {
                return $topico;
            }

        }

        $topico = new TopicoCooperativa();
        $topico->id_coop = $coop;
        $topico->id_topico = $this->id;
        $topico->evolucao = 0;

        return $topico;
    }
	
}
