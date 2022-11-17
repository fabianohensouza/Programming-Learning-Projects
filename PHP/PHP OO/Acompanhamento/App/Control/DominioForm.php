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
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Form\Date;
use Sisac\Widgets\Wrapper\FormWrapper;
use Sisac\Widgets\Form\Text;
/**
 * Formulário de cooperativas
 */
class DominioForm extends Page
{
    private $form;     // formulário de buscas
    private $datagrid; // listagem

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        if(isset($_GET['id']) || $_POST['id_coop'] != 0) {
            
            Transaction::open('Sisac'); 

            //Carregando dados da Cooperativa
            if (isset($_POST['id_coop'])) {
                $coop = $_POST['id_coop'];
            } else {
                $coop = Dominio::find($_GET['id'])->id_coop;
            }
            $coop =  Cooperativa::find($coop);

            // instancia um formulário
            $this->form = new FormWrapper(new Form('form_dominio'));
            $this->form->setTitle("Dominio Internet: $coop->id - $coop->nome");
            
            // cria os campos do formulário
            $id    = new Hidden('id');
            $id_coop      = new Entry('id_coop');
            $expiracao    = new Date('expiracao');
            $nome      = new Entry('nome'); 
            
            $id_coop->setValue($coop->id);            
            $id_coop->setEditable(FALSE);

            $this->form->addField('', $id, '0%');
            $this->form->addField('Cooperativa', $id_coop, '50%');
            $this->form->addField('Expiração', $expiracao, '50%');
            $this->form->addField('Nome', $nome, '50%');
            
            $this->form->addAction('Salvar', new Action(array($this, 'onSave')));

            parent::add($this->form); 
                
            Transaction::close();

        } else {

            ob_start();
            new Message('error', 'Favor selecionar a Cooperativa na tela anterior');
            $message = ob_get_contents();
            ob_clean();
            parent::add($message); 
        }
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

            $ad = new Dominio; // instancia objeto
            $ad->fromArray( (array) $dados); // carrega os dados 
            $ad->store(); // armazena o objeto no banco de dados
            
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
        Transaction::open('Sisac'); // inicia transação com o BD$
        $dominio = new Dominio();

        try
        {
            if (isset($_POST['id_coop'])) {
                $repository = new Repository('Dominio');
                $criteria = new Criteria();
                $criteria->add('id_coop', '=', $_POST['id_coop']);
                $lista = $repository->load($criteria);
                
                if(!empty($lista)) {
                    foreach ($lista as $obj) {
                        $dominio = $obj;
                    }

                    $this->form->setData($dominio);
                }

            } else if (isset($param['id']))
            {
                $id = $param['id']; // obtém a chave
                $dominio = Dominio::find($id);
                $this->form->setData($dominio);

            }

            Transaction::close(); // finaliza a transação

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
