<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
use Sisac\Database\Repository;
use Sisac\Database\Transaction;
use Sisac\Widgets\Base\Element;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Container\Panel;
use Sisac\Widgets\Datagrid\Datagrid;
use Sisac\Widgets\Datagrid\DatagridColumn;
use Sisac\Widgets\Wrapper\DatagridWrapper;

/**
 * Formulário de cooperativas
 */
class ConfiguraPainelList extends Page
{
    //private $form;     // formulário de buscas
    private $datagrid; // listagem

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        $url = http_build_query([
            'class'     => 'TopicoForm',
            'method'    => 'onEdit'
        ]);

        $link_topico = new Element('a');
        $link_topico->__set('href', 'index.php?' . $url);
        $link_topico->add('Novo tópico');
        $link_topico->__set('type', 'button');
        $link_topico->__set('class', 'btn btn-primary btn-lg btn-block');

        $url = http_build_query([
            'class'     => 'TopicoOrdemForm',
            'method'    => 'onEdit'
        ]);

        $link_ordem = new Element('a');
        $link_ordem->__set('href', 'index.php?' . $url);
        $link_ordem->add('Organizar tópicos');
        $link_ordem->__set('type', 'button');
        $link_ordem->__set('class', 'btn btn-info btn-lg btn-block');

        $painel = new Panel('Configurar Painel de Acompanhamento - Tópicos');
        $painel->add($link_topico);
        $painel->add($link_ordem);
        $painel->add(new Element('hr'));

        $this->datagrid = new DatagridWrapper(new Datagrid);

        $ordem   = new DatagridColumn('ordem','Ordem', 'center', '10%');
        $nome     = new DatagridColumn('nome', 'Nome', 'center', '15%');
        $itens     = new DatagridColumn('itens', 'Itens Ativos',    'center', '15%');
        $inativos     = new DatagridColumn('inativos', 'Itens Inativos',    'center', '20%');
        $pontos   = new DatagridColumn('pontos','Pontuação Máxima', 'center', '20%');
        $status   = new DatagridColumn('status','Status', 'center', '20%');

        $this->datagrid->addColumn($ordem);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($itens);
        $this->datagrid->addColumn($inativos);
        $this->datagrid->addColumn($pontos);
        $this->datagrid->addColumn($status);

        $this->datagrid->addAction( 'Editar',  new Action(["TopicoForm", 'onEdit']), 'id', 'fa fa-edit fa-lg blue');
        $this->datagrid->addAction( 'Deletar',  new Action([$this, 'onDelete']), 'id', 'fa fa-trash fa-lg red');

        $painel->add($this->datagrid);

        parent::add($painel);
        
        try
        {
            Transaction::open('Sisac');
            $repository = new Repository('Topico');
            $criteria = new Criteria();
            $criteria->setProperty('order', 'id_status, ordem');
            $topicos = $repository->load($criteria);

            $this->datagrid->clear();

            if ($topicos)
            {
        
                foreach ($topicos as $topico)
                {
                    $topico->status = $topico->getStatus();
                    $topico->itens = $topico->getNumItens();
                    $topico->inativos = $topico->getNumItens(2);
                    $topico->pontos = $topico->pont_max;

                    // adiciona o objeto na Datagrid
                    $this->datagrid->addItem($topico);
                }
            }

        }
        catch (Exception $e)		    // em caso de exceção
        {
            // exibe a mensagem gerada pela exceção
            new Message('error', $e->getMessage());
            // desfaz todas alterações no banco de dados
            Transaction::rollback();
        }
    }
}
