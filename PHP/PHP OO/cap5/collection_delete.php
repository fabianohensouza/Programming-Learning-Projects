<?php 
// carrega as classes necessárias 
require_once 'classes/api/Transaction.php'; 
require_once 'classes/api/Connection.php'; 
require_once 'classes/api/Criteria.php'; 
require_once 'classes/api/Repository.php'; 
require_once 'classes/api/Record.php'; 
require_once 'classes/api/Logger.php'; 
require_once 'classes/api/LoggerTXT.php'; 
require_once 'classes/model/Produto.php'; 

try { 
    // inicia a transação com a base de dados 
    Transaction::open('estoque');
    
    // define o arquivo para LOG 
    Transaction::setLogger(new LoggerTXT('/tmp/log_collection_delete.txt'));
    
    // define o critério de seleção 
    $criteria = new Criteria; 
    $criteria->add('descricao', 'like',  '%WEBC%'); 
    $criteria->add('descricao', 'like',  '%FILMAD%', 'or'); 
    
    // cria o repositório 
    $repository = new Repository('Produto'); 
    // exclui os objetos, conforme o critério 
    $repository->delete($criteria); 
    Transaction::close(); // fecha a transação 
} 
catch (Exception $e) { 
    echo $e->getMessage(); 
    Transaction::rollback(); 
}