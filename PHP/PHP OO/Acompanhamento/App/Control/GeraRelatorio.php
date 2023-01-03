<?php

use Sisac\Utils\Convert;
use Sisac\Database\Criteria;
use Sisac\Database\Repository;
use Sisac\Database\Transaction;
use Sisac\Log\LoggerTXT;
use Sisac\Widgets\Dialog\Message;

/**
 * Formulário de pessoas
 */
class GeraRelatorio
{
    private $dados;

    /**
     * Carrega daos do acompanhameto das Cooperativas
     */
    public function __construct($coops,  $avisos, $periodo)
    {
        try
        {
            $this->dados = array();

            unset($avisos['id']);
            $this->dados['periodo'] = $this->getPeriodo($periodo);
            $this->dados['avisos'] = $avisos;
            $this->dados['topicos'] = $this->getTopicos();

            Transaction::open('Sisac'); // inicia transação com o BD
            Transaction::setLogger(new LoggerTXT('App/Log/Log.txt'));

            $relatorio = [];

            foreach ($coops as $coop) {
                $cooperativa = Cooperativa::find($coop);

                $this->dados['coop'][$coop] = $this->getDadosCoop($cooperativa);
                $this->dados['coop'][$coop]['antivirus'] = $this->getAv($coop);
                $this->dados['coop'][$coop]['dominio'] = $this->getDominio($coop);
                $this->dados['coop'][$coop]['servidores'] = $this->getServidor($coop);

                foreach ($this->dados['topicos'] as $key => $value) {
                    $this->dados['coop'][$coop]['ev_topicos'][$key] = $this->getEvTopico($coop, ['id_topico', '=', $key]);
                    $this->dados['coop'][$coop]['ev_topicos'][$key]['itens'] = $this->getItens( $coop, 
                                                                                                $this->dados['topicos'][$key]['itens']);
                    
                }
            
                $relatorio[$coop] = $this->getHTML();                
            }

            var_dump($this->dados);exit;
               

            Transaction::close(); // finaliza a transação
        }
        catch (Exception $e)		    // em caso de exceção
        {
            // exibe a mensagem gerada pela exceção
            new Message('error', $e->getMessage());
            // desfaz todas alterações no banco de dados
            Transaction::rollback();
        }
    }

    private function getDadosCoop($coop) 
    {
        $dados_coop['id'] = $coop->id;
        $dados_coop['nome'] = $coop->nome;
        $dados_coop['cidade'] = $coop->get_cidade();
        return $dados_coop;
    }

    private function getPeriodo($periodo) 
    {
        $dados = [];
        $array = explode("-",$periodo);

        $dados['mes'] = Convert::monthName((int) $array[1]);
        $dados['ano'] = $array[0];
        
        return $dados;
    }

    private function getTopicos() 
    {
        $repository = new Repository('Topico');
        $criteria = new Criteria();
        $criteria->add('id_status', '=', 1);
        $criteria->setProperty('order', 'ordem');
        $topicos = $repository->load($criteria);
        $lista_topicos = array();
        foreach ($topicos as $topico) {
            $lista_topicos[$topico->id]['id'] = $topico->id;
            $lista_topicos[$topico->id]['nome'] = $topico->nome;
            $lista_topicos[$topico->id]['descricao'] = $topico->descricao;
            $lista_topicos[$topico->id]['conformidade'] = $topico->conformidade;
            $lista_topicos[$topico->id]['ordem'] = $topico->ordem;

            $repository2 = new Repository('Item');
            $criteria2 = new Criteria();
            $criteria2->add('id_topico', '=', $topico->id);
            $criteria2->add('id_status', '=', 1);
            $itens = $repository2->load($criteria2);
            $lista_itens = array();
            foreach ($itens as $item) {
                $lista_itens[$item->id]['id'] = $item->id;
                $lista_itens[$item->id]['descricao'] = $item->descricao;
                $lista_itens[$item->id]['multiplicador'] = $item->multiplicador;
            }

            $lista_topicos[$topico->id]['itens'] = $lista_itens;
        }

        return $lista_topicos;
    }

    private function getBDCoop($coop, $active_record, $filter = false) 
    {    
        $repository = new Repository($active_record);
        $criteria = new Criteria();
        $criteria->add('id_coop', '=', $coop);
        if ($filter) {
            $criteria->add($filter[0], $filter[1], $filter[2]);
        }
        return $repository->load($criteria);    
    }

    private function getAv($coop) 
    {        
        $dados = [];
        foreach ($this->getBDCoop($coop, 'Antivirus') as $value) {
            $dados['licencas'] = $value->licencas;
            $dados['expiracao'] = (!is_null($value->expiracao)) ? Convert::dateToPtBr($value->expiracao) : '-';
        }      
        return $dados;
    }

    private function getDominio($coop) 
    {        
        $dados = [];
        foreach ($this->getBDCoop($coop, 'Dominio') as $value) {
            $dados['nome'] = $value->nome;
            $dados['expiracao'] = (!is_null($value->expiracao)) ? Convert::dateToPtBr($value->expiracao) : '-';
        }        
        return $dados;
    }

    private function getEvTopico($coop, $filter) 
    {        
        foreach ($this->getBDCoop($coop, 'TopicoCooperativa', $filter) as $value) {
            return ['id' => $filter[2],
                    'evolucao' => "{$value->evolucao}%", 
                    'observacao' => $value->observacao];
        }        
        return;
    }

    private function getEv($coop) 
    {        
        foreach ($this->getBDCoop($coop, 'PainelCooperativa') as $value) {
            return "{$value->evolucao}%";
        }        
        return;
    }

    private function getServidor($coop) 
    {        
        $dados = [];
        foreach ($this->getBDCoop($coop, 'Servidor') as $value) {
            $dados['nome'] = $value->nome;
            $dados['so'] = $value->getSistOp();
            $dados['fabricante'] = $value->getFabricante();
            $dados['modelo'] = $value->nome;
            $dados['serial'] = $value->serial;
            $dados['tipo'] = $value->getTipoServidor();
            $dados['status'] = $value->getStatusHw();
            $dados['garantia'] = $value->getGarantia();
        }        
        return $dados;
    }

    private function getItens($coop, $itens) 
    {     
        $dados = [];

        foreach ($itens as $item) {
            foreach ($this->getBDCoop($coop, 'CoopItemStatus', ['id_item', '=', $item['id']]) as $value) {
                $dados[$item['id']]['id'] = $value->id;
                $dados[$item['id']]['status'] = $value->getStatusNome();
            } 
        }   
              
        return $dados;
    }

    private function getHTML() 
    {     
        /*$loader = new \Twig\Loader\ArrayLoader($this->dados);
        $twig = new \Twig\Environment($loader);
        
        echo $twig->render('App/Templates/template.html', ['name' => 'Fabien']);*/
        $dados = [];
        $dados['dados'] = $this->dados;

        $loader = new \Twig\Loader\FilesystemLoader('App/Resources');
        $twig = new \Twig\Environment($loader);

        echo $twig->render('acpmt_report.html', $dados);
            
        exit;//return $html;
    }
}