<?php

use Sisac\Database\Criteria;
use Sisac\Database\Record;
use Sisac\Database\Repository;

class Backup extends Record
{
    const TABLENAME = 'backup';

    public function getTipoBackup()
    {
        if (isset($this->id_tipo_backup)) {
            return TipoBackup::find($this->id_tipo_backup)->nome;
        }

        return '-';
    }

    public function getSwBackup()
    {
        if (isset($this->id_sw_backup)) {
            return SwBackup::find($this->id_sw_backup)->nome;
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

    public function getStatus()
    {
        if (isset($this->id_status)) {
            return Status::find($this->id_status)->nome;
        }

        return '-';
    }

    public function deleteAll()
    {
        $repository = new Repository('JobBackup');
        $criteria = new Criteria();
        $criteria->add('id_backup', '=', $this->id);
        $items = $repository->load($criteria);

        if ($items) {
            foreach ($items as $item) {
                $item->delete();
            }
        }

        return $this->delete();
    }
}
