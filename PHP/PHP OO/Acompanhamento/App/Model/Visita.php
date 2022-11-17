<?php
use Sisac\Database\Record;

class Visita extends Record
{
    const TABLENAME = 'visitas';

    public function getFilePath()
    {
        $anexo = Anexo::find($this->id_anexo);
        return $anexo->caminho;
    }
}