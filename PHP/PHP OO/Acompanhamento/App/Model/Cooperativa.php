<?php
use Sisac\Database\Record;

class Cooperativa extends Record
{
    const TABLENAME = 'cooperativas';
    private $cidade;
    private $responsavel_ic;
    
    public function get_cidade()
    {
        if (empty($this->cidade))
        {
            $this->cidade = Cidade::find($this->id_cidade);
        }
        
        return $this->cidade->nome;
    }
    
    public function get_responsavel_ic()
    {
        if (empty($this->responsavel_ic))
        {
            $this->responsavel_ic = Usuario::find($this->id_responsavel_ic);
        }
        
        return $this->responsavel_ic->nome;
    }
}