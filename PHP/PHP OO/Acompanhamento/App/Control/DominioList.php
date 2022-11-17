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
class DominioList extends Page
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
        $this->activeRecord = 'Dominio';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_dominio'));
        $this->form->setTitle('Domínios Internet');
        
        // cria os campos do formulário
        $cooperativas    = new Combo('id_coop');
        
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
        Transaction::close();
        
        $cooperativas->addItems($items);
        
        $this->form->addField('Filtrar por Cooperativa', $cooperativas, '30%');
        
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Novo', new Action(array("DominioCoopForm", 'onEdit')));
        
        // instancia a Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $coop     = new DatagridColumn('id_coop',   'Cooperativa',   'center', '15%');
        $expiracao   = new DatagridColumn('expiracao', 'Expiração', 'center', '25%');
        $nome   = new DatagridColumn('nome',     'Nome', 'center', '60%');

        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($coop);
        $this->datagrid->addColumn($expiracao);
        $this->datagrid->addColumn($nome);

        $this->datagrid->addAction( 'Editar',  new Action(["DominioForm", 'onEdit']),   'id', 'fa fa-edit fa-lg blue');
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
        if ($dados->id_coop)
        {
            $criteria->add('id_coop', 'like', "%{$dados->id}%");
        }
        
        $criteria->add('id_coop', '!=', '0000');
        $criteria->setProperty('order', 'id_coop');
        
        // carreta os objetos que satisfazem o critério
        $objects = $repository->load($criteria);
        $this->datagrid->clear(); 
        
        if ($objects)
        {
            foreach ($objects as $object)
            {                
                $object->expiracao = Convert::dateToPtBr($object->expiracao);
                
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
            
            $ad = Ad::find($id); // instancia objeto

            $ad->delete(); // deleta objeto do banco de dados
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
