<?php
use Sisac\Database\Record;

class Pa extends Record
{
    const TABLENAME = 'pas';
    private $cidade;
    
    public function get_cidade()
    {
        if (empty($this->cidade))
        {
            $this->cidade = Cidade::find($this->id_cidade);
        }
        
        return $this->cidade->nome;
    }
}