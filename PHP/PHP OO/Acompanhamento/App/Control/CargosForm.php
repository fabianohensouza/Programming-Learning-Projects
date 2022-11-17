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
use Sisac\Widgets\Wrapper\FormWrapper;

/**
 * Formulário de cargos
 */
class CargosForm extends Page
{
    private $form;
    private $cargos;

    /**
     * Construtor da página
     */
    public function __construct()
    {
        if (!isset($_GET['id_coop'])) {
            return;
        }
        parent::__construct();

        Transaction::open('Sisac');
        $cooperativa = Cooperativa::find($_GET['id_coop']);

        $repository = new Repository('Cargo');
        $criteria = new Criteria;
        $criteria->add('cod', '!=', "-");
        $cargos = $repository->load($criteria);

        $repository = new Repository('Pessoa');
        $criteria = new Criteria;
        $criteria->add('id_coop', '=', $_GET['id_coop']);
        $criteria->add('nome', '=', "-", "or");        
        $pessoas = $repository->load($criteria);
        foreach ($pessoas as $obj_pessoa) {
            $item_pessoas[$obj_pessoa->id] = $obj_pessoa->nome;
        }

        Transaction::close();

        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_cargos'));
        $titulo = 'Cargos: ' . $cooperativa->id . ' - ' . $cooperativa->nome;
        $this->form->setTitle($titulo);
        
        // cria os campos do formulário
        foreach ($cargos as $cargo) {
            $form_name = $cargo->cod;
            $nome = $cargo->nome;

            $$form_name    = new Combo($form_name);
            $$form_name->addItems($item_pessoas);
            $this->form->addField($nome, $$form_name, '50%');
        }

        $action = new Action(array($this, 'onSave'));
        $action->setParameter('id_coop', $_GET['id_coop']);
        $this->form->addAction('Salvar', $action);

        if (isset($_GET['id_coop'])) {
            $action = new Action(array("CooperativasForm", 'onEdit'));
            $action->setParameter('id', $_GET['id_coop']);
            $this->form->addAction('Voltar', $action);
        }
                
        // adiciona o formulário na página
        parent::add($this->form);
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
            foreach ($dados as $key => $value) {
                $dado = new stdClass;
                $dado->cod_cargo = $key;
                $dado->id = $value;

                $pessoa = new Pessoa; // instancia objeto
                $pessoa->fromArray( (array) $dado); // carrega os dados
                $pessoa->store(); // armazena o objeto no banco de dados
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
                $cooperativa = Cooperativa::find($id);
                $this->form->setData($cooperativa); // lança os dados da pessoa no formulário
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
