<?php
use Sisac\Database\Record;
use Sisac\Utils\Convert;

class LicencasSeguranca extends Record
{
    const TABLENAME = 'licencas_seguranca';

    public function getProdSeguranca()
    {        
        if (isset($this->id_prod_seguranca)) {
            return ProdSeguranca::find($this->id_prod_seguranca)->nome;
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

    public function getExpiracao()
    {        
        if (isset($this->vitalicia)) {

            if ($this->vitalicia == '1') {
                return "Vitalícia";
            } else if ($this->vitalicia != '1') {
                return Convert::dateToPtBr($this->expiracao);
            }
        }

        return '-';
    }
}
