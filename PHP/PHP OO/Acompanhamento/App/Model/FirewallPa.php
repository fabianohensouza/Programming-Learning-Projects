<?php
use Sisac\Database\Record;

class FirewallPa extends Record
{
    const TABLENAME = 'firewall_pa';
    private $firewall_nome;
    
    public function get_firewall_nome()
    {
        if (empty($this->firewall_nome))
        {
            $this->firewall_nome = FirewallModelo::find($this->id_firewall_modelo);
        }
        
        return $this->firewall_nome->nome;
    }
}