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
use Sisac\Widgets\Form\Number;
use Sisac\Widgets\Form\Text;
use Sisac\Widgets\Wrapper\DatagridWrapper;

/**
 * Formulário de cooperativas
 */
class ItemForm extends Page
{
    private $form;     // formulário de buscas

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_itens'));
        $this->form->setTitle('Editar Item');
        
        // cria os campos do formulário
        $id    = new Hidden('id');
        $descricao      = new Entry('descricao');
        $id_topico    = new Combo('id_topico');
        $multiplicador  = new Number('multiplicador');
        $id_status     = new Combo('id_status');
        
        Transaction::open('Sisac');   
        $lista_status = Status::all();
        $items = array();
        foreach ($lista_status as $obj_status) {
            $items[$obj_status->id] = $obj_status->nome;
        }
        $id_status->addItems($items);

        $topicos = Topico::all();
        $items = array();
        foreach ($topicos as $obj_topico) {
            $items[$obj_topico->id] = $obj_topico->nome;
        }
        $id_topico->addItems($items);

        if (isset($_GET['id_topico'])) {
            $id_topico->setValue($_GET['id_topico']);
        }

        $multiplicador->setProperty('min', 1);
        $multiplicador->setProperty('max', 4);
        $multiplicador->setValue(1);
        
        $this->form->addField('', $id, '0%');
        $this->form->addField('Descrição', $descricao, '50%');
        $this->form->addField('Tópico', $id_topico, '50%');
        $this->form->addField('Multiplicador', $multiplicador, '15%');
        $this->form->addField('Status', $id_status, '15%');

        $id_topico->getEditable(FALSE);
        
        $action = new Action([$this, 'onSave']);
        $action->setParameter('id_topico', $_GET['id_topico']);
        $this->form->addAction('Salvar', $action);

        $action = new Action(['TopicoForm', 'onEdit']);
        $action->setParameter('id', $_GET['id_topico']);
        $this->form->addAction('Retornar', $action);

        parent::add($this->form);    

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
            $item = new Item; // instancia objeto
            $item->fromArray( (array) $dados); // carrega os dados
            $item->store(); // armazena o objeto no banco de dados

            $topico = Topico::find($dados->id_topico);
            $topico->getPontMax();
            $topico->store();
            
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
                $topico = Item::find($id);
                $this->form->setData($topico); // lança os dados da pessoa no formulário

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