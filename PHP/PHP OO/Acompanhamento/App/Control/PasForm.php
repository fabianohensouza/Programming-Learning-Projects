<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Combo;
use Sisac\Widgets\Form\Entry;
use Sisac\Database\Repository;
use Sisac\Widgets\Form\Hidden;
use Sisac\Database\Transaction;
use Sisac\Widgets\Container\VBox;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Container\Panel;
use Sisac\Widgets\Form\CheckGroup;
use Sisac\Widgets\Datagrid\Datagrid;
use Sisac\Widgets\Wrapper\FormWrapper;
use Sisac\Widgets\Datagrid\DatagridColumn;
use Sisac\Widgets\Wrapper\DatagridWrapper;

/**
 * Formulário de cooperativas
 */
class PasForm extends Page
{
    private $form;     // formulário de buscas
    private $datagrid; // listagem

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_pas'));
        $this->form->setTitle('Ponto de Atendimento');
        
        // cria os campos do formulário
        $id    = new Hidden('id');
        $coop      = new Combo('id_coop');
        $numero    = new Entry('numero');
        $cidade    = new Combo('id_cidade');
        $tipo      = new Combo('id_tipo');
        $links     = new CheckGroup('links');
        
        // carrega as cidades do banco de dados
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
        
        $coop->addItems($items);

        $cidades = Cidade::all();
        $items = array();
        foreach ($cidades as $obj_cidade) {
            $items[$obj_cidade->id] = $obj_cidade->nome;
        }
        $cidade->addItems($items); 

        $tipos = TipoPa::all();
        $items = array();
        foreach ($tipos as $obj_tipo) {
            $items[$obj_tipo->id] = $obj_tipo->nome;
        }
        $tipo->addItems($items); 

        $links->addItems([  'mpls' => 'MPLS',
                            'sdwan' => 'SD-Wan' ]); 

        $this->form->addField('', $id, '0%');
        $this->form->addField('Cooperativa', $coop, '50%');
        $this->form->addField('Número', $numero, '20%');
        $this->form->addField('Cidade', $cidade, '50%');
        $this->form->addField('Tipo', $tipo, '20%');
        $this->form->addField('Links', $links, '20%');
        
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));

        parent::add($this->form); 
        
        $id = isset($_GET['id']) ? $_GET['id'] : false;

        if ($id) {

            $info_firewall = false;
            
            //Verificando se existe Firewall associado ao PA
            $repository = new Repository('FirewallPa');
            $criteria = new Criteria;
            $criteria->add('id_pa', '=', $id);
            $lista_firewalls = $repository->load($criteria);

            foreach ($lista_firewalls as $obj_firewall)
            {
                $info_firewall = "Firewall: {$obj_firewall->get_firewall_nome()} - Serial: $obj_firewall->serial";                
            }

            if ($info_firewall) {

                $painel = new Panel();
                $painel->add($info_firewall);
                
                // instancia a Datagrid
                $this->datagrid = new DatagridWrapper(new Datagrid);

                // instancia as colunas da Datagrid
                $interface     = new DatagridColumn('interface',   'Interface',   'center', '10%');
                $ip   = new DatagridColumn('ip',     'Endereço IP', 'center', '10%');
                $zona   = new DatagridColumn('zona', 'Zona', 'center', '35%');
                $ddns   = new DatagridColumn('ddns', 'DDNS', 'center', '35%');

                // adiciona as colunas à Datagrid
                $this->datagrid->addColumn($interface);
                $this->datagrid->addColumn($ip);
                $this->datagrid->addColumn($zona);
                $this->datagrid->addColumn($ddns);

                $this->datagrid->addAction( 'Editar',  new Action(["FirewallInterfaceForm", 'onEdit']),   'id', 'fa fa-edit fa-lg blue');
                
                // monta a página através de uma tabela
                $box = new VBox;
                $box->style = 'display:block';
                $box->add($painel);
                $box->add($this->datagrid);
                
                parent::add($box);
            }
        }   

        Transaction::close();
    }

    /**
     * Salva os dados do formulário
     */
    public function onSave()
    {
        try
        {
            // inicia transação com o BD
            Transaction::open('Sisac');

            $dados = $this->form->getData();

            $this->form->setData($dados); 

            //Redefinindo os links
            $dados->mpls = false;
            $dados->sdwan = false;

            if ($dados->links) {
                foreach ($dados->links as $key => $value) {
                    $dados->$value = true;
                }
            }
            
            unset($dados->links);

            $pa = new Pa; // instancia objeto
            $pa->fromArray( (array) $dados); // carrega os dados 
            $pa_id = $pa->store(); // armazena o objeto no banco de dados

            if ($dados->id == '') { //verifica se o o PA não existe no BD e cria as interfaces
            
                for ($i=1; $i <= 20; $i++) { 
                    $dados = new stdClass;
                    $dados->id_pa =  $pa_id;
                    $dados->interface = "X{$i}";
                    $interface = new FirewallInterfaces;
                    $interface->fromArray( (array) $dados);
                    $interface->store();
                }

            }
            
            Transaction::close(); // finaliza a transação
            new Message('info', 'Dados armazenados com sucesso');
        }
        catch (Exception $e)
        {
            // exibe a mensagem gerada pela exceção
            new Message('error', $e->getMessage());

            // desfaz todas alterações no banco de dados
            Transaction::rollback();
        }
    }
    
    /**
     * Carrega registro para edição
     */
    public function onEdit($param)
    {
        try
        {
            if (isset($param['id']))
            {
                $id = $param['id']; // obtém a chave
                Transaction::open('Sisac'); // inicia transação com o BD
                $pa = Pa::find($id);

                //recuperando informações de links
                //$links = array(0 => '', 1 => ''); 
                $links = array();                
                if ($pa->mpls == 1) {
                    $links[0] = 'mpls';
                }                
                if ($pa->sdwan == 1) {
                    $links[1] = 'sdwan';
                }
                $pa->links = $links;

                $this->form->setData($pa); // lança os dados da pessoa no formulário

                if ($this->datagrid) {

                    $this->datagrid->clear();
                    $loop_interfaces = 0;

                    $repository = new Repository('FirewallInterfaces');
                    $criteria = new Criteria;
                    $criteria->add('id_pa', '=', $id);
                    $lista_interfaces = $repository->load($criteria);

                    $repository = new Repository('FirewallPa');
                    $criteria = new Criteria;
                    $criteria->add('id_pa', '=', $id);
                    $lista_firewall = $repository->load($criteria); 

                    if ($lista_firewall)
                    {
                        foreach ($lista_firewall as $firewall)
                        {
                            // adiciona o objeto na DataGrid
                            $id_firewall = $firewall->id_firewall_modelo;
                            $numero_interfaces = FirewallModelo::find($id_firewall)->interfaces;
                        }
                    }

                    if ($lista_interfaces)
                    {
                        foreach ($lista_interfaces as $interface)
                        {
                            if (isset($interface->zona)) {
                                $interface->zona = strtoupper($interface->zona);
                            }

                            if (isset($interface->zona)) {
                                $interface->ddns = '<a href="https://' . $interface->ddns . ':3443">' . $interface->ddns . '</a>';
                            }

                            // adiciona o objeto na DataGrid
                            $this->datagrid->addItem($interface);

                            $loop_interfaces++;
                            if ($loop_interfaces == $numero_interfaces) {
                                break;
                            }
                        }
                    }
                }

                Transaction::close(); // finaliza a transação

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
