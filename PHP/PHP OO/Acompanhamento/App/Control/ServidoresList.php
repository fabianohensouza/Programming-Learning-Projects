<?php
use Sisac\Control\Page;
use Sisac\Utils\Convert;
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
use Sisac\Widgets\Datagrid\Datagrid;
use Sisac\Widgets\Wrapper\FormWrapper;
use Sisac\Widgets\Datagrid\DatagridColumn;
use Sisac\Widgets\Wrapper\DatagridWrapper;

/**
 * Cadastro de cidades
 */
class ServidoresList extends Page
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
        $this->activeRecord = 'Servidor';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_servidores'));
        $this->form->setTitle('Servidores');
        
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
        $this->form->addAction('Novo', new Action(array("ServidorCoopForm", 'onEdit')));
        
        // instancia a Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $id_coop   = new DatagridColumn('id_coop',     'Cooperativa', 'center', '10%');
        $pa     = new DatagridColumn('pa',   'PA',   'center', '7%');
        $tipo   = new DatagridColumn('tipo', 'Tipo', 'center', '8%');
        $modelo   = new DatagridColumn('modelo', 'Fabricante/Modelo', 'center', '20%');
        $sistema_op   = new DatagridColumn('sistema_op', 'Sistema Operacional', 'center', '25%');
        $dt_garantia   = new DatagridColumn('dt_garantia', 'Garantia', 'center', '10%');
        $ip_lan   = new DatagridColumn('ip_lan', 'IP', 'center', '20%');

        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($id_coop);
        $this->datagrid->addColumn($pa);
        $this->datagrid->addColumn($tipo);
        $this->datagrid->addColumn($modelo);
        $this->datagrid->addColumn($sistema_op);
        $this->datagrid->addColumn($dt_garantia);
        $this->datagrid->addColumn($ip_lan);

        $this->datagrid->addAction( 'Editar',  new Action(["ServidoresForm", 'onEdit']),   'id', 'fa fa-edit fa-lg blue');
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
        
        $criteria->add('id', 'NOT LIKE', '0000');
        $criteria->setProperty('order', 'id');
        
        // carreta os objetos que satisfazem o critério
        $objects = $repository->load($criteria);
        $this->datagrid->clear(); 
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $object->pa = $object->getPa();
                $object->tipo = $object->getTipoServidor();
                $object->modelo = $object->getFabricante() . " - " . $object->modelo;
                $object->sistema_op = $object->getSistOp();
                $object->dt_garantia = Convert::dateToPtBr($object->dt_garantia);

                // adiciona o objeto na DataGrid
                $this->datagrid->addItem($object);
            }
        }

        // finaliza a transação
        Transaction::close();
        $this->loaded = true;
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
