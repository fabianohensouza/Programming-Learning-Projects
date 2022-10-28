<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Traits\EditTrait;
use Sisac\Traits\SaveTrait;
use Sisac\Database\Criteria;
use Sisac\Widgets\Form\Form;

use Sisac\Traits\DeleteTrait;

use Sisac\Traits\ReloadTrait;

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
class FirewallList extends Page
{
    private $form;
    private $datagrid;
    private $loaded;
    private $connection;
    private $activeRecord;
    
    use DeleteTrait;
    
    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        $this->connection   = 'Sisac';
        $this->activeRecord = 'FirewallPa';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_firewall_pas'));
        $this->form->setTitle('Equipamentos de Firewall');
        
        // cria os campos do formulário
        $cooperativas    = new Combo('id');
        
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
        $this->form->addAction('Novo', new Action(array("FirewallCoopForm", 'onEdit')));
        
        // instancia a Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $id_coop     = new DatagridColumn('id_coop',   'Cooperativa',   'center', '10%');
        $id_pa   = new DatagridColumn('id_pa',     'PA', 'center', '10%');
        $id_firewall_modelo   = new DatagridColumn('id_firewall_modelo', 'Modelo Firewall', 'center', '35%');
        $serial   = new DatagridColumn('serial', 'Serial', 'center', '35%');
        $interfaces   = new DatagridColumn('interfaces', 'Interfaces', 'center', '10%');

        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($id_coop);
        $this->datagrid->addColumn($id_pa);
        $this->datagrid->addColumn($id_firewall_modelo);
        $this->datagrid->addColumn($serial);
        $this->datagrid->addColumn($interfaces);

        $this->datagrid->addAction( 'Editar',  new Action(["FirewallForm", 'onEdit']),   'id', 'fa fa-edit fa-lg blue');
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
        if ($dados->id)
        {
            $criteria->add('id_coop', 'like', "%{$dados->id}%");
        }
        
        $criteria->setProperty('order', 'id');
        
        // carreta os objetos que satisfazem o critério
        $objects = $repository->load($criteria);
        $this->datagrid->clear(); 
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $pa = Pa::find( $object->id_pa);
                $object->id_pa = $pa->numero;

                $firewall = FirewallModelo::find( $object->id_firewall_modelo);
                $object->id_firewall_modelo = $firewall->nome;
                $object->interfaces = $firewall->interfaces;

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
            
            $class = $this->activeRecord;
            
            $object = $class::find($id); // instancia objeto
            
            $object->delete(); // deleta objeto do banco de dados
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
