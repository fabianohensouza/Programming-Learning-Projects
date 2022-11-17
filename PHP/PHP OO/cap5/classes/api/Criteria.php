<?php
class Criteria
{
    private $filters; // armazena a lista de filtros
    
    function __construct()
    {
        $this->filters = array();
    }
    
    public function add($variable, $compare_operator, $value, $logic_operator = 'and')
    {
        // na primeira vez, não precisamos concatenar
        if (empty($this->filters))
        {
            $logic_operator = NULL;
        }
        
        $this->filters[] = [$variable, $compare_operator, $this->transform($value), $logic_operator];
    }
    
    private function transform($value)
    {
        // caso seja um array
        if (is_array($value)) {
            foreach ($value as $x) {
                if (is_integer($x)) {
                    $foo[]= $x;
                }
                else if (is_string($x)) {
                    $foo[]= "'$x'";
                }
            }
            // converte o array em string separada por ","
            $result = '(' . implode(',', $foo) . ')';
        }
        else if (is_string($value)) {
            $result = "'$value'";
        }
        else if (is_null($value)) {
            $result = 'NULL';
        }
        else if (is_bool($value)) {
            $result = $value ? 'TRUE' : 'FALSE';
        }
        else {
            $result = $value;
        }
        // retorna o valor
        return $result;
    }
    
    public function dump()
    {
        // concatena a lista de expressões
        if (is_array($this->filters) and count($this->filters) > 0) {
            $result = '';
            foreach ($this->filters as $filter) {
                $result .= $filter[3] . ' ' . $filter[0] . ' ' . $filter[1] . ' '. $filter[2] . ' ';
            }
            $result = trim($result);
            return "({$result})";
        }
    }
    
    public function setProperty($property, $value)
    {
        if (isset($value)) {
            $this->properties[$property] = $value;
        }
        else {
            $this->properties[$property] = NULL;
        }
    }
    
    public function getProperty($property)
    {
        if (isset($this->properties[$property])) {
            return $this->properties[$property];
        }
    }
}
