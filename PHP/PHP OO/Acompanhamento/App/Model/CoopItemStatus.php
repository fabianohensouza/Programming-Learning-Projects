<?php
use Sisac\Database\Record;

class CoopItemStatus extends Record
{
    const TABLENAME = 'coop_item_status';
    public $pontos;

    public function getPontos()
    {
        $item = Item::find($this->id_item);
        $status = StatusItem::find($this->id_status_item);
        $this->pontos = $item->multiplicador * $status->pontos;

        return $this->pontos;
    }
}


