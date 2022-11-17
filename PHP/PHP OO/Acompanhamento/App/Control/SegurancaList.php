<?php
use Sisac\Control\Page;
use Sisac\Utils\Convert;
use Sisac\Control\Action;
use Sisac\Database\Criteria;

use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Combo;
use Sisac\Database\Repository;

use Sisac\Database\Transaction;
use Sisac\Widgets\Container\VBox;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Dialog\Question;
use Sisac\Widgets\Datagrid\Datagrid;
use Sisac\Widgets\Wrapper\FormWrapper;
use Sisac\Widgets\Datagrid\DatagridColumn;
use Sisac\Widgets\Wrapper\DatagridWrapper;

/**
 * Cadastro de cidades
 */
class SegurancaList extends Page
{
    private $form;
    private $datagrid;
    private $loaded;
    private $connection;
    private $activeRecord;
    
    //use DeleteTrait;
    
    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        $this->connection   = 'Sisac';
        $this->activeRecord = 'LicencasSeguranca';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_licencas_seguranca'));
        $this->form->setTitle('Recursos de Segurança');
        
        // cria os campos do formulário
        $cooperativas   = new Combo('id_coop');
        $produtos       = new Combo('id_prod_seguranca');
        
        Transaction::open('Sisac');

        $repository = new Repository('Cooperativa');
        $criteria = new Criteria;
        $criteria->add('id', '!=', '0000');
        $criteria->setProperty('order', 'id');
        $lista_coop = $repository->load($criteria);
        $items = array();

        foreach ($lista_coop as $obj_cooperativas)
        {
            $items[$obj_cooperativas->id] = $obj_cooperativas->id . " - " . $obj_cooperativas->nome;
        }        
        $cooperativas->addItems($items);

        $lista_prod = ProdSeguranca::all();
        $items = array();

        foreach ($lista_prod as $obj_prod)
        {
            $items[$obj_prod->id] = $obj_prod->nome;
        }        
        $produtos->addItems($items);

        Transaction::close();
        
        $this->form->addField('Filtrar por Cooperativa', $cooperativas, '30%');
        $this->form->addField('Filtrar por Produto', $produtos, '30%');
        
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));

        $action = new Action(array("CoopToForm", 'onEdit'));
        $action->setParameter('form', 'SegurancaForm');
        $this->form->addAction('Novo', $action);
        
        // instancia a Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $id_coop     = new DatagridColumn('id_coop',   'Cooperativa',   'center', '10%');
        $id_prod_seguranca   = new DatagridColumn('id_prod_seguranca',     'Produtos de Segurança', 'center', '30%');
        $licencas   = new DatagridColumn('licencas', 'Licenças', 'center', '20%');
        $expiracao   = new DatagridColumn('expiracao', 'Expiração', 'center', '25%');
        $id_status   = new DatagridColumn('id_status', 'Status', 'center', '10%');

        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($id_coop);
        $this->datagrid->addColumn($id_prod_seguranca);
        $this->datagrid->addColumn($licencas);
        $this->datagrid->addColumn($expiracao);
        $this->datagrid->addColumn($id_status);

        $this->datagrid->addAction( 'Editar',  new Action(["SegurancaForm", 'onEdit']),   'id', 'fa fa-edit fa-lg blue');
        $this->datagrid->addAction( 'Excluir', new Action([$this, 'onDelete']), 'id', 'fa fa-trash fa-lg red');
        
        // monta a página através de uma tabela
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);
        
        parent::add($box);
    }

    /**
     * Carrega os dados
     */
    public function onReload()
    {
        Transaction::open( $this->connection );
        $repository = new Repository( $this->activeRecord );

        $criteria = new Criteria;
        $dados = $this->form->getData();
        
        if ($dados->id_coop)    $criteria->add('id_coop', 'like', "%{$dados->id_coop}%");
        if ($dados->id_prod_seguranca)  $criteria->add('id_prod_seguranca', 'like', "%{$dados->id_prod_seguranca}%");
        
        $criteria->add('id_coop', '!=', '0000');
        $criteria->setProperty('order', 'id_coop');
        
        // carreta os objetos que satisfazem o critério
        $objects = $repository->load($criteria);
        $this->datagrid->clear(); 
        
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $object->id_prod_seguranca  = $object->getProdSeguranca();
                $object->id_status          = $object->getStatus();
                $object->expiracao          = $object->getExpiracao();

                // adiciona o objeto na DataGrid
                $this->datagrid->addItem($object);
            }
        }

        // finaliza a transação
        Transaction::close();
        $this->loaded = true;
    }
    
    /**
     * Pergunta sobre a exclusão de registro
     */
    function onDelete($param)
    {
        $id = $param['id']; // obtém o parâmetro $id
        $action1 = new Action(array($this, 'Delete'));
        $action1->setParameter('id', $id);
        
        new Question('Deseja realmente excluir o registro?', $action1);
    }

    /**
     * Exclui um registro
     */
    function Delete($param)
    {
        try
        {
            $id = $param['id']; // obtém a chave
            Transaction::open( $this->connection ); // inicia transação com o BD
            
            $ad = Backup::find($id); // instancia objeto

            $ad->deleteAll(); // deleta objeto do banco de dados
            Transaction::close(); // finaliza a transação
            $this->onReload(); // recarrega a datagrid
            new Message('info', "Registro excluído com sucesso");
        }
        catch (Exception $e)
        {
            new Message('error', $e->getMessage());
        }
    }

    /**
     * exibe a página
     */
    public function show()
    {
        // se a listagem ainda não foi carregada
        if (!$this->loaded)
        {
            $this->onReload();
        }
        parent::show();
    }
}
