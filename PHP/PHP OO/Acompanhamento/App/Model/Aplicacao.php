<?php
use Sisac\Database\Record;

class Aplicacao extends Record
{
    const TABLENAME = 'aplicacoes';

    public function getTipo()
    {
        if (isset($this->id_tipo)) {
            return TipoHospedagem::find($this->id_tipo)->nome;
        }

        return '-';
    }

    public function getServidor()
    {
        if (isset($this->id_servidor) || $this->id_servidor != '') {
            return Servidor::find($this->id_servidor)->nome;
        }

        return '-';
    }
}