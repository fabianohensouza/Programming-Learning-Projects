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
class PasList extends Page
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
        $this->activeRecord = 'Pa';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_pas'));
        $this->form->setTitle('Pontos de Atendimento');
        
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
        $this->form->addAction('Novo', new Action(array("PasForm", 'onEdit')));
        
        // instancia a Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $coop     = new DatagridColumn('id_coop',   'Cooperativa',   'center', '10%');
        $numero   = new DatagridColumn('numero',     'Número', 'center', '10%');
        $cidade   = new DatagridColumn('cidade', 'Cidade', 'center', '30%');
        $tipo   = new DatagridColumn('tipo', 'Tipo', 'center', '10%');
        $firewall   = new DatagridColumn('firewall', 'Firewall', 'center', '10%');
        $serial   = new DatagridColumn('serial', 'Serial', 'center', '30%');

        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($coop);
        $this->datagrid->addColumn($numero);
        $this->datagrid->addColumn($cidade);
        $this->datagrid->addColumn($tipo);
        $this->datagrid->addColumn($firewall);
        $this->datagrid->addColumn($serial);

        $this->datagrid->addAction( 'Editar',  new Action(["PasForm", 'onEdit']),   'id', 'fa fa-edit fa-lg blue');
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
            $criteria->add('id', 'like', "%{$dados->id}%");
        }
        
        $criteria->add('id_coop', '!=', '0000');
        $criteria->setProperty('order', 'id_coop');
        $criteria->setProperty('order', 'numero');
        
        // carreta os objetos que satisfazem o critério
        $objects = $repository->load($criteria);
        $this->datagrid->clear(); 
        
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $object->cidade = $object->get_cidade();

                $tipo_pa = TipoPa::find( $object->id_tipo);
                $object->tipo = $tipo_pa->nome;

                $repository = new Repository('FirewallPa');
                $criteria = new Criteria;
                $criteria->add('id_pa', '=', $object->id);
                $firewall_pa = $repository->load($criteria);

                $object->firewall = '-';
                $object->serial = '-';

                foreach ($firewall_pa as $value) {
                    $firewall = FirewallModelo::find($value->id_firewall_modelo);

                    $object->firewall = (isset($firewall->nome)) ? $firewall->nome : $object->firewall;
                    $object->serial = (isset($value->serial)) ? $value->serial : $object->serial;
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
            
            $pa = Pa::find($id); // instancia objeto

            //Removendo referência do PA no registro do Firewall
            $repository = new Repository('FirewallPa');
            $criteria = new Criteria();
            $criteria->add('id_pa', '=', $id);
            $firewalls = $repository->load($criteria);

            if ($firewalls) {
                foreach ($firewalls as $firewall) {
                    $firewall->id_pa = 1;
                    $firewall->store();
                }
            }

            //Removendo interfaces relacionadas ao PA
            $repository = new Repository('FirewallInterfaces');
            $criteria = new Criteria();
            $criteria->add('id_pa', '=', $id);
            $interfaces = $repository->load($criteria);

            if ($interfaces) {
                foreach ($interfaces as $interface) {
                    $interface->delete();
                }
            }

            if ($interfaces) {
                $pa->delete(); // deleta objeto do banco de dados
                Transaction::close(); // finaliza a transação
                $this->onReload(); // recarrega a datagrid
                new Message('info', "Registro excluído com sucesso");
            }
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
