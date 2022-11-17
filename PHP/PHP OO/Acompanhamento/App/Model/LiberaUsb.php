<?php
use Sisac\Database\Record;

class LiberaUsb extends Record
{
    const TABLENAME = 'libera_usb';

    public $id;
    public $id_coop;
    public $chamado;
    public $data;
    public $equipamento;
    public $obs;
}
