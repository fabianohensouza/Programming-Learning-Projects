<?php

use Sisac\Utils\Convert;
use Sisac\Database\Criteria;
use Sisac\Database\Repository;
use Sisac\Database\Transaction;
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
            $this->dados['periodo'] = $periodo;
            $this->dados['avisos'] = $avisos;
            $this->dados['topicos'] = $this->dadosTopicos();

            Transaction::open('Sisac'); // inicia transação com o BD

            foreach ($coops as $coop) {
                $cooperativa = Cooperativa::find($coop);

                $this->dados['coop'][$coop] = $this->dadosCoop($cooperativa);
                $this->dados['coop'][$coop]['antivirus'] = $this->dadosAv($coop);
                $this->dados['coop'][$coop]['dominio'] = $this->dadosDominio($coop);

                foreach ($this->dados['topicos'] as $key => $value) {
                    $this->dados['coop'][$coop]['ev_topicos'][$key] = $this->dadosEvTopico($coop, ['id_topico', '=', $key]);
                    $this->dados['coop'][$coop]['ev_topicos'][$key]['itens'] = $this->dadosItens($coop);
                }
                $this->dados['coop'][$coop]['ev_geral'] = $this->dadosEv($coop);

                var_dump($this->dados['coop'][$coop]);exit;
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

    private function dadosCoop($coop) 
    {
        $dados_coop['id'] = $coop->id;
        $dados_coop['nome'] = $coop->nome;
        $dados_coop['cidade'] = $coop->get_cidade();
        return $dados_coop;
    }

    private function dadosTopicos() 
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

    private function carregaBDCoop($coop, $active_record, $filter = false) 
    {    
        $repository = new Repository($active_record);
        $criteria = new Criteria();
        $criteria->add('id_coop', '=', $coop);
        if ($filter) {
            $criteria->add($filter[0], $filter[1], $filter[2]);
        }
        return $repository->load($criteria);    
    }

    private function dadosAv($coop) 
    {        
        $dados = [];
        foreach ($this->carregaBDCoop($coop, 'Antivirus') as $value) {
            $dados['licencas'] = $value->licencas;
            $dados['expiracao'] = Convert::dateToPtBr($value->expiracao);
        }      
        return $dados;
    }

    private function dadosDominio($coop) 
    {        
        $dados = [];
        foreach ($this->carregaBDCoop($coop, 'Dominio') as $value) {
            $dados['nome'] = $value->nome;
            $dados['expiracao'] = Convert::dateToPtBr($value->expiracao);
        }        
        return $dados;
    }

    private function dadosEvTopico($coop, $filter) 
    {        
        foreach ($this->carregaBDCoop($coop, 'TopicoCooperativa', $filter) as $value) {
            return ['evolucao' => "{$value->evolucao}%", 'observacao' => $value->observacao];
        }        
        return;
    }

    private function dadosEv($coop) 
    {        
        foreach ($this->carregaBDCoop($coop, 'PainelCooperativa') as $value) {
            return "{$value->evolucao}%";
        }        
        return;
    }

    private function dadosServidor($coop) 
    {        
        $dados = [];
        foreach ($this->carregaBDCoop($coop, 'Servidor') as $value) {
            $dados['nome'] = $value->nome;
            $dados['expiracao'] = Convert::dateToPtBr($value->expiracao);
        }        
        return $dados;
    }

    private function dadosItens($coop) 
    {        
        $dados = [];
        foreach ($this->carregaBDCoop($coop, 'Servidor') as $value) {
            $dados['nome'] = $value->nome;
            $dados['expiracao'] = Convert::dateToPtBr($value->expiracao);
        }        
        return $dados;
    }
}