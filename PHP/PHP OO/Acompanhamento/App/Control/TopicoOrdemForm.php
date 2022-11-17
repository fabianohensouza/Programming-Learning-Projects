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
class TopicoOrdemForm extends Page
{
    private $form;     // formulário de buscas

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        Transaction::open('Sisac');  
        $repository = new Repository('Topico');
        $criteria = new Criteria();
        $criteria->add('id_status', '=', 1);
        $lista_topicos = $repository->load($criteria);
        $items = array();

        foreach ($lista_topicos as $obj_topicos) {
            $items[$obj_topicos->id] = $obj_topicos->nome;
        }

        $qtd_topicos = count($lista_topicos);

        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_ordemtopicos'));
        $this->form->setTitle('Organizar Tópicos');
        
        // cria os campos do formulário
        for ($i=1; $i <= $qtd_topicos; $i++) { 
            $$i   = new Combo($i);
            $$i->addItems($items);
            $this->form->addField("{$i}º", $$i, '50%');
        }

        $this->form->addAction('Salvar', new Action([$this, 'onSave']));
        $this->form->addAction('Retornar', new Action(['ConfiguraPainelList', '']));

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
            
            foreach ($_POST as $ordem => $id) {
                $topico = Topico::find($id);
                $topico->ordem = $ordem;
                $topico->store();
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
            Transaction::open('Sisac');  
            $repository = new Repository('Topico');
            $criteria = new Criteria();
            $criteria->add('id_status', '=', 1);
            $lista_topicos = $repository->load($criteria);
            $dados = new stdClass;
        
            foreach ($lista_topicos as $obj_topico) {
                $ordem = $obj_topico->ordem;
                $id = $obj_topico->id;
                $dados->$ordem = $id;
            }

            $this->form->setData($dados); // lança os dados da pessoa no formulário

            Transaction::close();
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