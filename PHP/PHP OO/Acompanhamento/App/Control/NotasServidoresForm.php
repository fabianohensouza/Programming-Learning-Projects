<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Combo;
use Sisac\Widgets\Form\Entry;
use Sisac\Database\Repository;
use Sisac\Widgets\Form\Hidden;
use Sisac\Widgets\Form\Number;
use Sisac\Database\Transaction;
use Sisac\Widgets\Base\Element;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Container\Panel;
use Sisac\Widgets\Form\CheckGroup;
use Sisac\Widgets\Form\Text;
use Sisac\Widgets\Wrapper\FormWrapper;

/**
 * Formulário de Notas de Servidores
 */
class NotasServidoresForm extends Page
{
    private $form;

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        Transaction::open('Sisac'); 
            $servidor = Servidor::find($_GET['id_servidor']);
            
            // instancia um formulário
            $this->form = new FormWrapper(new Form('form_notas_servidores'));
            $this->form->setTitle("Notas: {$servidor->nome} | {$servidor->id_coop}");
            
            // cria os campos do formulário
            $id    = new Hidden('id');
            $id_servidor      = new Hidden('id_servidor');
            $descricao    = new Text('descricao');

            Transaction::close();
            
            $this->form->addField('', $id, '0%');
            $this->form->addField('', $id_servidor, '0%');
            $this->form->addField('Descrição', $descricao, '75%');

            $id_servidor->setValue($servidor->id);
            $descricao->setSize('75%','200px');
            
            $action = new Action([$this, 'onSave']);
            $action->setParameter('id_servidor', $servidor->id);
            $this->form->addAction('Salvar', $action);

            $action = new Action(['ServidoresForm', 'onEdit']);
            $action->setParameter('id', $servidor->id);
            $this->form->addAction('Retornar', $action);
                    
            // adiciona o formulário na página
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
            $nota = new NotasServidores(); // instancia objeto
            $nota->fromArray( (array) $dados); // carrega os dados
            $nota->store(); // armazena o objeto no banco de dados
            
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
                $nota = NotasServidores::find($id);
                $this->form->setData($nota); // lança os dados da pessoa no formulário
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
