<?php 
// carrega as classes necessárias 
require_once 'classes/api/Criteria.php'; 

// Critério simples com OR, e filtros com valores inteiros 
$criteria = new Criteria;
$criteria->add('idade', '<', 16); 
$criteria->add('idade', '>', 60, 'or'); 
print $criteria->dump() . "<br>\n"; 

// Critério simples com AND, e filtros com vetores de inteiros
$criteria = new Criteria; 
$criteria->add('idade','IN',     array(24,25,26)); 
$criteria->add('idade','NOT IN', array(10));
print $criteria->dump() . "<br>\n"; 

// Critério simples com OR, e filtros com Like
$criteria = new Criteria; 
$criteria->add('nome', 'like', 'pedro%'); 
$criteria->add('nome', 'like', 'maria%', 'or'); 
print $criteria->dump() . "<br>\n";

// Critério simples com AND e filtros usando IS NOT NULL e "=" 
$criteria = new Criteria; 
$criteria->add('telefone', 'IS NOT', NULL);
$criteria->add('sexo',     '=',      'F'); 
print $criteria->dump() . "<br>\n";

// Critério simples com AND, e filtros usando IN/NOT IN sobre vetores de strings 
$criteria = new Criteria; 
$criteria->add('UF', 'IN',     array('RS', 'SC', 'PR'));
$criteria->add('UF', 'NOT IN', array('AC', 'PI'));
print $criteria->dump() . "<br>\n";
