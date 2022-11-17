<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
use Sisac\Widgets\Form\Date;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Text;
use Sisac\Widgets\Form\Combo;
use Sisac\Widgets\Form\Entry;
use Sisac\Database\Repository;
use Sisac\Widgets\Form\Hidden;
use Sisac\Database\Transaction;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Wrapper\FormWrapper;
/**
 * Formulário de cooperativas
 */
class LiberacaoForm extends Page
{
    private $form;     // formulário de buscas

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        if(isset($_GET['id']) || isset($_GET['id_coop'])) {
            
            Transaction::open('Sisac');

            //Carregando dados da Cooperativa
            if (isset($_GET['id_coop'])) {
                $coop = $_GET['id_coop'];
            } else {
                $coop = LiberaUsb::find($_GET['id'])->id_coop;
            }
            $coop =  Cooperativa::find($coop);

            // instancia um formulário
            $this->form = new FormWrapper(new Form('form_liberacao_usb'));
            $this->form->setTitle("Liberação USB");
            
            // cria os campos do formulário
            $id             = new Hidden('id');
            $id_coop        = new Entry('id_coop');
            $chamado        = new Entry('chamado');
            $data           = new Date('data');
            $equipamento    = new Entry('equipamento');  
            $obs            = new Text('obs');
            
            $id_coop->setValue($coop->id);            
            $id_coop->setEditable(FALSE);

            $this->form->addField('', $id, '0%');
            $this->form->addField('Cooperativa', $id_coop, '50%');
            $this->form->addField('Chamado', $chamado, '50%');
            $this->form->addField('Data da Solicitação', $data, '50%');
            $this->form->addField('Equipamento', $equipamento, '50%');
            $this->form->addField('Observações', $obs, '50%');

            $obs->setSize('50%', '150px');
            
            $action1 = new Action(array($this, 'onSave'));
            $action1->setParameter('id_coop', $coop->id);
            $this->form->addAction('Salvar', $action1);

            $action2 = new Action(array("AntivirusForm", 'onEdit'));
            $action2->setParameter('id_coop', $coop->id);
            $this->form->addAction('Retornar', $action2);

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

            $liberacao = new LiberaUsb(); // instancia objeto
            $liberacao->fromArray( (array) $dados); // carrega os dados 
            $liberacao->store(); // armazena o objeto no banco de dados
            
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
        $ad = new Ad();

        try
        {
            if (isset($_POST['id_coop'])) {
                $repository = new Repository('Ad');
                $criteria = new Criteria();
                $criteria->add('id_coop', '=', $_POST['id_coop']);
                $lista = $repository->load($criteria);
                
                if(!empty($lista)) {
                    foreach ($lista as $obj) {
                        $ad = $obj;
                    }

                    $this->form->setData($ad);
                }

            } else if (isset($param['id']))
            {
                $id = $param['id']; // obtém a chave
                $ad = Ad::find($id);
                $this->form->setData($ad);

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
