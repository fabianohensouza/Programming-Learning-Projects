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
class VisitasList extends Page
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
        $this->activeRecord = 'Visita';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_cooperativass'));
        $this->form->setTitle('Visitas Técnicas');
        
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
        $this->form->addAction('Novo', new Action(array("VisitasForm", 'onEdit')));
        
        // instancia a Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $id_coop     = new DatagridColumn('id_coop',   'Cooperativa',   'center', '10%');
        $data_ida   = new DatagridColumn('data_ida',     'Data inicio', 'center', '15%');
        $data_retorno   = new DatagridColumn('data_retorno', 'Data retorno', 'center', '15%');
        $responsavel_ic   = new DatagridColumn('responsavel_ic', 'Responsavel IC', 'center', '20%');
        $motivo   = new DatagridColumn('motivo', 'Motivo', 'center', '30%');
        $relatorio   = new DatagridColumn('relatorio', 'Relatório', 'center', '20%');

        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($id_coop);
        $this->datagrid->addColumn($data_ida);
        $this->datagrid->addColumn($data_retorno);
        $this->datagrid->addColumn($responsavel_ic);
        $this->datagrid->addColumn($motivo);
        $this->datagrid->addColumn($relatorio);

        $this->datagrid->addAction( 'Editar',  new Action(["VisitasForm", 'onEdit']),   'id', 'fa fa-edit fa-lg blue');
        
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
            $criteria->add('id_coop', 'like', "%{$dados->id_coop}%");
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

                $object->relatorio = (isset($object->id_anexo)) ? '<a target="_blank" rel="noopener noreferrer" href="' . $object->getFilePath() . '"> Visualizar </a>' : '-';
                $object->responsavel_ic = (isset($object->id_responsavel_ic)) ? Usuario::find($object->id_responsavel_ic)->nome : '-';
                $object->motivo = (isset($object->id_tipo_visita)) ? TipoVisita::find($object->id_tipo_visita)->nome : '-';

                $object->data_ida       = Convert::dateToPtBr($object->data_ida);
                $object->data_retorno   = Convert::dateToPtBr($object->data_retorno);
                
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
