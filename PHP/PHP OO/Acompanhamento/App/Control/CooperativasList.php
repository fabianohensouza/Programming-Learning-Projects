<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Traits\EditTrait;
use Sisac\Traits\SaveTrait;
use Sisac\Widgets\Form\Form;
use Sisac\Traits\DeleteTrait;

use Sisac\Traits\ReloadTrait;

use Sisac\Widgets\Form\Combo;

use Sisac\Database\Repository;
use Sisac\Database\Criteria;
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
class CooperativasList extends Page
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
        $this->activeRecord = 'Cooperativa';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_cooperativas'));
        $this->form->setTitle('Cooperativas');
        
        // cria os campos do formulário
        $responsavel    = new Combo('id');
        
        Transaction::open('Sisac');
        $responsaveis = Usuario::all();
        $items = array();
        foreach ($responsaveis as $obj_responsavel)
        {
            $items[$obj_responsavel->id] = $obj_responsavel->nome;
        }
        Transaction::close();
        
        $responsavel->addItems($items);
        
        $this->form->addField('Filtrar por Responsável', $responsavel, '30%');
        
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Novo', new Action(array("CooperativasForm", 'onEdit')));
        
        // instancia a Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $codigo   = new DatagridColumn('id',     'Código', 'center', '10%');
        $nome     = new DatagridColumn('nome',   'Nome',   'left', '25%');
        $cidade   = new DatagridColumn('cidade', 'Cidade', 'left', '20%');
        $ic   = new DatagridColumn('ic', 'IC', 'center', '10%');
        $responsavel_ic   = new DatagridColumn('responsavel_ic', 'Responsável', 'left', '20%');
        $equipamentos   = new DatagridColumn('equipamentos', 'Equipamentos', 'center', '10%');

        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($cidade);
        $this->datagrid->addColumn($ic);
        $this->datagrid->addColumn($responsavel_ic);
        $this->datagrid->addColumn($equipamentos);

        $this->datagrid->addAction( 'Editar',  new Action(["CooperativasForm", 'onEdit']),   'id', 'fa fa-edit fa-lg blue');
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
            $criteria->add('id_responsavel_ic', 'like', "%{$dados->id}%");
        }
        
        $criteria->add('id', 'NOT LIKE', '0000');
        $criteria->setProperty('order', 'ic DESC, id');
        
        // carreta os objetos que satisfazem o critério
        $objects = $repository->load($criteria);
        $this->datagrid->clear(); 
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $object->equipamentos = $object->equipamentos == 0 ? "-" : $object->equipamentos;
                $object->cidade = $object->get_cidade();
                $object->responsavel_ic = $object->get_responsavel_ic();

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
