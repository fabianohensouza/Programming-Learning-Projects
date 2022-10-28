<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Combo;
use Sisac\Database\Repository;
use Sisac\Database\Transaction;
use Sisac\Widgets\Container\VBox;
use Sisac\Widgets\Datagrid\Datagrid;
use Sisac\Widgets\Wrapper\FormWrapper;
use Sisac\Widgets\Datagrid\DatagridColumn;
use Sisac\Widgets\Wrapper\DatagridWrapper;

/**
 * Cadastro de cidades
 */
class AcompanhamentoList extends Page
{
    private $form;
    private $datagrid;
    private $loaded;
    private $connection;
    private $activeRecord;
    private $lista_topicos;
    
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
        $this->form->setTitle('Painel de Acompanhamento');
        
        // cria os campos do formulário
        $cooperativas    = new Combo('id');
        
        Transaction::open('Sisac');

        $repository = new Repository('Cooperativa');
        $criteria = new Criteria;
        $criteria->add('id', '!=', '0000');
        $criteria->add('ic', '=', 'Sim');
        $criteria->setProperty('order', 'id');
        $lista_coop = $repository->load($criteria);
        $items = array();
        foreach ($lista_coop as $obj_cooperativas)
        {
            $items[$obj_cooperativas->id] = $obj_cooperativas->id . " - " . $obj_cooperativas->nome;
        }
        
        $cooperativas->addItems($items);
        
        $this->form->addField('Selecionar Cooperativa', $cooperativas, '30%');
        
        $this->form->addAction('Filtrar', new Action(array($this, 'onReload')));
        
        // instancia a Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);
        $this->datagrid->setTitle('Evolução de Tópicos');
        $this->datagrid->setBorder(TRUE);

        $coop     = new DatagridColumn('coop',   'Cooperativa',   'center', '15%');
        $this->datagrid->addColumn($coop);

        $repository = new Repository('Topico');
        $criteria = new Criteria();
        $criteria->add('id_status', '=', 1);
        $criteria->setProperty('order', 'ordem');
        $this->lista_topicos = $repository->load($criteria);

        $width = 85 / (count($this->lista_topicos) + 1);
        $topico = array();
        foreach ($this->lista_topicos as $obj_topico) {
            $topico[$obj_topico->ordem] = new DatagridColumn(   "topico{$obj_topico->ordem}",
                                                                $obj_topico->ordem,
                                                                'center',  
                                                                "{$width}%");
            $this->datagrid->addColumn($topico[$obj_topico->ordem]);
        };

        $geral     = new DatagridColumn('geral',   'Geral',   'center', "{$width}%");
        $this->datagrid->addColumn($geral);

        $this->datagrid->addAction( 'Editar Painel',  new Action(["PainelForm", 'onEdit']),   'id', 'fa fa-edit fa-lg blue');
        
        Transaction::close();

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
            $criteria->add('id', '=', $dados->id);
        }   

        $criteria->add('ic', '=', 'Sim');
        $criteria->setProperty('order', 'id');
        
        // carreta os objetos que satisfazem o critério
        $objects = $repository->load($criteria);
        $this->datagrid->clear();
        
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $object->coop = $object->id . '<br>' . $object->nome;
                $object->coop = $object->id;

                foreach ($this->lista_topicos as $obj_topico) {
                    $topico = "topico{$obj_topico->ordem}";
                    $topico_cooperativa = $obj_topico->getTopicoCooperativa($object->id);
                    $object->$topico = "{$topico_cooperativa->evolucao}%";
                }

                $repository = new Repository('PainelCooperativa');
                $criteria = new Criteria;
                $criteria->add('id_coop', '=', $object->id);
                $lista_painel = $repository->load($criteria);

                foreach ($lista_painel as $painel) {
                    $object->geral = "{$painel->evolucao}%";
                }

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
