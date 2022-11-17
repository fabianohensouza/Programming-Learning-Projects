<?php
use Sisac\Database\Record;

class FileServer extends Record
{
    const TABLENAME = 'file_server';

    public function getTipo()
    {
        if (isset($this->id_tipo)) {
            return TipoFileServer::find($this->id_tipo)->nome;
        }

        return '-';
    }

    public function getServidor()
    {
        if ($this->id_tipo != '1' && isset($this->id_servidor)) {
            return Servidor::find($this->id_servidor)->nome;
        }

        return '-';
    }
}
