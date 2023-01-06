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
            //$this->dados['topicos'] = $this->getTopicos();

            Transaction::open('Sisac'); // inicia transação com o BD
            Transaction::setLogger(new LoggerTXT('App/Log/Log.txt'));

            $relatorio = [];

            foreach ($coops as $coop) {
                $cooperativa = Cooperativa::find($coop);

                $this->dados['coop'][$coop] = $this->getDadosCoop($cooperativa);
                $this->dados['coop'][$coop]['antivirus'] = $this->getAv($coop);
                $this->dados['coop'][$coop]['dominio'] = $this->getDominio($coop);
                $this->dados['coop'][$coop]['servidores'] = $this->getServidor($coop);

                foreach ($this->getTopicos() as $topico) {
                    $id_tipoco =$topico['id'];

                    $this->dados['coop'][$coop]['topicos'][$id_tipoco] = $topico;                    

                    $ev = $this->getEvTopico($coop, ['id_topico', '=', $id_tipoco]);
                    $this->dados['coop'][$coop]['topicos'][$id_tipoco]['ev_topico'] = $ev['ev_topico'];
                    $this->dados['coop'][$coop]['topicos'][$id_tipoco]['observacao'] = $ev['observacao'];

                    foreach ($this->dados['coop'][$coop]['topicos'][$id_tipoco]['itens'] as $key => $value) {
                        $this->dados['coop'][$coop]['topicos'][$id_tipoco]['itens'][$key]['status'] = $this->getItemStatus($coop,$key);                        
                    }
                }  
                
                $this->dados['coop'][$coop]['ev_coop'] = $this->getEvCoop($coop);//var_dump($this->dados);exit;
                
                $relatorio[$coop] = $this->getHTML();   

            }
            exit;               

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
        return [    'ev_topico' => "{$value->evolucao}%", 
                    'observacao' => $value->observacao];
        }        
        
        return [    'ev_topico' => '-', 
                    'observacao' => '-'];
    }

    private function getEvCoop($coop) 
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

    private function getItemStatus($coop, $item) 
    {     
        $status = '-';

        foreach ($this->getBDCoop($coop, 'CoopItemStatus', ['id_item', '=', $item]) as $value) {
            $status = $value->getStatusNome(); 
        }   
              
        return $status;
    }

    private function getHTML() 
    {     
        $dados = [];
        $dados['dados'] = $this->dados;

        $loader = new \Twig\Loader\FilesystemLoader('App/Resources');
        $twig = new \Twig\Environment($loader);

        echo $twig->render('acpmt_report.html', $dados);
        
        //$html = $twig->render('acpmt_report.html', $dados);
        //return $html;
    }
}