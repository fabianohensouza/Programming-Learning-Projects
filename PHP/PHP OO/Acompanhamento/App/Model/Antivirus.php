<?php
use Sisac\Database\Record;

class Antivirus extends Record
{
    const TABLENAME = 'antivirus';

    public function getVersao()
    {
        if (isset($this->id_versao)) {
            return AntivirusVersao::find($this->id_versao)->nome;
        }

        return '-';
    }

    public function getServidor()
    {
        if (isset($this->id_servidor)) {
            return Servidor::find($this->id_servidor)->nome;
        }

        return '-';
    }
}
