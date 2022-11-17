<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
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
class ArquivosForm extends Page
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
            if (isset($_REQUEST['id_coop'])) {
                $coop = $_REQUEST['id_coop'];
            } else {
                $coop = Antivirus::find($_REQUEST['id'])->id_coop;
            }
            $coop =  Cooperativa::find($coop);

            // instancia um formulário
            $this->form = new FormWrapper(new Form('form_file_server'));
            $this->form->setTitle("Servidor de Arquivos: $coop->id - $coop->nome");
            
            // cria os campos do formulário
            $id             = new Hidden('id');
            $id_coop        = new Entry('id_coop');
            $id_tipo        = new Combo('id_tipo');
            $id_servidor    = new Combo('id_servidor');
            $descricao      = new Text('descricao'); 
            
            $id_coop->setValue($coop->id);
            $id_coop->setEditable(FALSE);
            $descricao->setSize('50%', '150px');

            $tipos = TipoFileServer::all();
            $items = array();
            foreach ($tipos as $obj_tipo) {
                $items[$obj_tipo->id] = $obj_tipo->nome;
            }
            $id_tipo->addItems($items);

            $repository = new Repository('Servidor');
            $criteria = new Criteria();
            $criteria->add('id_coop', '=', $coop->id);
            $servidores = $repository->load($criteria);
            $items = array();
            foreach ($servidores as $obj_servidor) {
                $items[$obj_servidor->id] = $obj_servidor->nome;
            }
            $id_servidor->addItems($items);

            $this->form->addField('', $id, '0%');
            $this->form->addField('Cooperativa', $id_coop, '40%');
            $this->form->addField('Tipo File Server', $id_tipo, '40%');
            $this->form->addField('Servidor', $id_servidor, '40%');
            $this->form->addField('Descrição', $descricao, '40%');
            
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

            $dados = $this->form->getData();echo"<pre>";var_dump($dados);

            if ($dados->id_tipo == '1') {
                $dados->id_servidor = null;
            }
            var_dump($dados);exit;

            $this->form->setData($dados); 

            $pa = new FileServer(); // instancia objeto
            $pa->fromArray( (array) $dados); // carrega os dados
            $pa->store(); // armazena o objeto no banco de dados
            
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
                $pa = FileServer::find($id);

                $this->form->setData($pa); // lança os dados da pessoa no formulário

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
