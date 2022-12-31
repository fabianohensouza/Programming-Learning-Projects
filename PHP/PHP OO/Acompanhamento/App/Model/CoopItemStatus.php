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

    public function getStatusNome()
    {
        $status_nome = StatusItem::find($this->id_status_item)->nome;
        if ($status_nome) return $status_nome;

        return '-';
    }

    public function getStatusPontos()
    {
        $status_pontos = StatusItem::find($this->id_status_item)->pontos;
        if ($status_pontos) return $status_pontos;

        return '-';
    }
}


