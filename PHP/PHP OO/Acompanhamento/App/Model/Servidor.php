<?php
use Sisac\Utils\Convert;
use Sisac\Database\Record;

class Servidor extends Record
{
    const TABLENAME = 'servidores';
    private $pa;
    private $tipo;
    private $fabricante;
    private $sistema_op;
    private $status_hw;
    
    public function getPa()
    {
        if (empty($this->pa))
        {
            $this->pa = Pa::find($this->id_pa);
        }
        
        return $this->pa->numero;
    }
    
    public function getTipoServidor()
    {
        if (empty($this->tipo))
        {
            $this->tipo = TipoServidor::find($this->id_tipo);
        }
        
        return $this->tipo->nome;
    }
    
    public function getFabricante()
    {
        if (empty($this->fabricante))
        {
            $this->fabricante = Fabricante::find($this->id_fabricante);
        }
        
        return $this->fabricante->nome;
    }
    
    public function getSistOp()
    {
        if (empty($this->sistema_op))
        {
            $this->sistema_op = SistemaOperacional::find($this->id_sistema_op);
        }
        
        return $this->sistema_op->nome;
    }
    
    public function getStatusHw()
    {
        $status =  StatusHardware::find($this->id_status_hardware);
        var_dump($status);
        //return $status;
    }
    
    public function getGarantia()
    {
        $today = date('Y-m-d');
        $garantia = (!is_null($this->dt_garantia)) ? Convert::dateToPtBr($this->dt_garantia) : '00/00/0000';

        if ($today > $this->dt_garantia) return "Garantia expirada {$garantia}";

        return $garantia;
    }
}