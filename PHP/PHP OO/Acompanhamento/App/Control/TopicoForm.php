<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Combo;
use Sisac\Widgets\Form\Entry;
use Sisac\Database\Repository;
use Sisac\Database\Transaction;
use Sisac\Widgets\Base\Element;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Container\Panel;
use Sisac\Widgets\Datagrid\Datagrid;
use Sisac\Widgets\Wrapper\FormWrapper;
use Sisac\Widgets\Datagrid\DatagridColumn;
use Sisac\Widgets\Form\Hidden;
use Sisac\Widgets\Form\Text;
use Sisac\Widgets\Wrapper\DatagridWrapper;

/**
 * Formulário de cooperativas
 */
class TopicoForm extends Page
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
        $this->form = new FormWrapper(new Form('form_topicos'));
        $this->form->setTitle('Editar Tópico');
        
        // cria os campos do formulário
        $id    = new Hidden('id');
        $nome      = new Entry('nome');
        $descricao    = new Text('descricao');
        $conformidade  = new Text('conformidade');
        $id_status     = new Combo('id_status');
        
        Transaction::open('Sisac');   
        $lista_status = Status::all();
        $items = array();
        foreach ($lista_status as $obj_status) {
            $items[$obj_status->id] = $obj_status->nome;
        }
        $id_status->addItems($items);
        
        $this->form->addField('', $id, '0%');
        $this->form->addField('Nome', $nome, '50%');
        $this->form->addField('Status', $id_status, '50%');
        $this->form->addField('Descrição', $descricao, '50%');
        $this->form->addField('Conformidade', $conformidade, '50%');

        $descricao->setSize('50%','150px');
        $conformidade->setSize('50%','150px');
        
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        $this->form->addAction('Retornar', new Action(array('ConfiguraPainelList', '')));

        parent::add($this->form);

        if (isset($_GET['id'])) {
            $url = http_build_query([
                                        'class'     => 'ItemForm',
                                        'method'    => 'onEdit',
                                        'id_topico'    => $_GET['id']
                                    ]);

            $link = new Element('a');
            $link->__set('href', 'index.php?' . $url);
            $link->add('Cadastrar Item');
            $link->__set('type', 'button');
            $link->__set('class', 'btn btn-info btn-lg btn-block');

            $painel = new Panel('Itens');
            $painel->add($link);
            $painel->add(new Element('hr'));

            $this->datagrid = new DatagridWrapper(new Datagrid);
    
            $descricao     = new DatagridColumn('descricao',       'Descrição',    'center', '40%');
            $topico     = new DatagridColumn('topico',       'Tópico',    'center', '30%');
            $multiplicador   = new DatagridColumn('multiplicador','Multiplicador', 'center', '15%');
            $status   = new DatagridColumn('status','Status', 'center', '15%');
    
            $this->datagrid->addColumn($descricao);
            $this->datagrid->addColumn($topico);
            $this->datagrid->addColumn($multiplicador);
            $this->datagrid->addColumn($status);
        
            $action = new Action(['ItemForm', 'onEdit']);
            $action->setParameter('id_topico', $_GET['id']);
            $this->datagrid->addAction( 'Editar', $action, 'id', 'fa fa-edit fa-lg blue');
            $this->datagrid->addAction( 'Deletar',  new Action([$this, 'onDelete']), 'id', 'fa fa-trash fa-lg red');
            
            $painel->add($this->datagrid);

            parent::add($painel);
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

            if ($dados->id_status != 1) {
                $dados->ordem = '-';
            }
            
            $topico = new Topico; // instancia objeto
            $topico->fromArray( (array) $dados); // carrega os dados
            $topico->store(); // armazena o objeto no banco de dados
            
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
                Transaction::open('Sisac');
                $topico = Topico::find($id);
                $this->form->setData($topico); // lança os dados da pessoa no formulário

                $repository = new Repository('Item');
                $criteria = new Criteria;
                $criteria->add('id_topico', '=', $id);
                $criteria->setProperty('order', 'id_status');

                $itens = $repository->load($criteria);
                $this->datagrid->clear();
                if ($itens)
                {
        
                    foreach ($itens as $item)
                    {
                        $item->topico = $item->get_topico();
                        $item->status = $item->get_status();
                        
                        // adiciona o objeto na Datagrid
                        $this->datagrid->addItem($item);
                    }
                }

                Transaction::close();
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