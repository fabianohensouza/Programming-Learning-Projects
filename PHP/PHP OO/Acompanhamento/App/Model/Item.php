<?php
use Sisac\Database\Record;
use Sisac\Database\Criteria;
use Sisac\Database\Repository;

class Item extends Record
{
    const TABLENAME = 'itens';
    private $topico;
    private $status;
    
    public function get_topico()
    {
        if (empty($this->topico))
        {
            $this->topico = Topico::find($this->id_topico);
        }
        
        return $this->topico->nome;
    }
    
    public function get_status()
    {
        if (empty($this->status))
        {
            $this->status = Status::find($this->id_status);
        }
        
        return $this->status->nome;
    }

    public function getItemCooperativa($coop)
    {
        $repository = new Repository('CoopItemStatus');
        $criteria = new Criteria();
        $criteria->add('id_coop', '=', $coop);
        $criteria->add('id_item', '=', $this->id);
        $itens = $repository->load($criteria);
        
        if ($itens) {
                
            foreach ($itens as $item) {
                return $item;
            }

        }

        $item = new CoopItemStatus();
        $item->id_coop = $coop;
        $item->id_item = $this->id;

        return $item;
    }
	
}
