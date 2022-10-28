<?php
use Sisac\Database\Record;

class Servidor extends Record
{
    const TABLENAME = 'servidores';
    private $pa;
    private $tipo;
    private $fabricante;
    private $sistema_op;
    
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
}