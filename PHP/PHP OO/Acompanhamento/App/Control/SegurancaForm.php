<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
use Sisac\Widgets\Form\Date;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Text;
use Sisac\Widgets\Form\Combo;
use Sisac\Widgets\Form\Entry;
use Sisac\Widgets\Form\Hidden;
use Sisac\Widgets\Form\Number;
use Sisac\Database\Transaction;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Form\CheckGroup;
use Sisac\Widgets\Wrapper\FormWrapper;
/**
 * Formulário de cooperativas
 */
class SegurancaForm extends Page
{
    private $form;     // formulário de buscas
    private $datagrid; // listagem

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        if(isset($_REQUEST['id']) || $_REQUEST['id_coop'] != 0) {
            
            Transaction::open('Sisac');

            //Carregando dados da Cooperativa
            if (isset($_REQUEST['id']) && $_REQUEST['id'] != '') {

                $this->segcoop  = LicencasSeguranca::find($_REQUEST['id']);
                $this->coop     = Cooperativa::find($this->segcoop->id_coop);

            } else if (isset($_REQUEST['id_coop'])) {

                $this->coop =  Cooperativa::find($_REQUEST['id_coop']);

            }

            // instancia um formulário
            $this->form = new FormWrapper(new Form('form_seguranca'));
            $this->form->setTitle("Recursos de Segurança: {$this->coop->id} - {$this->coop->nome}");
            
            // cria os campos do formulário
            $id                 = new Hidden('id');
            $id_coop            = new Entry('id_coop');
            $id_prod_seguranca  = new Combo('id_prod_seguranca');
            $licencas           = new Number('licencas');
            $expiracao          = new Date('expiracao');
            $vitalicia          = new CheckGroup('vitalicia');
            $id_status          = new Combo('id_status');
            $obs                = new Text('obs');
            
            $id_coop->setValue($this->coop->id);            
            $id_coop->setEditable(FALSE);                  

            $produtos = ProdSeguranca::all();
            $items = array();
            foreach ($produtos as $obj_produto) {
                $items[$obj_produto->id] = $obj_produto->nome;
            }
            $id_prod_seguranca->addItems($items);        

            $status = Status::all();
            $items = array();
            foreach ($status as $obj_status) {
                $items[$obj_status->id] = $obj_status->nome;
            }
            $id_status->addItems($items);

            $vitalicia->addItems(["1" => "Vitalícia"]);

            $this->form->addField('', $id, '0%');
            $this->form->addField('Cooperativa', $id_coop, '30%');
            $this->form->addField('Produto', $id_prod_seguranca, '30%');
            $this->form->addField('Licenças', $licencas, '30%');
            $this->form->addField('Expiração', $expiracao, '30%');
            $this->form->addField('Status', $id_status, '30%');
            $this->form->addField('', $vitalicia, '30%');
            $this->form->addField('Observações', $obs, '50%');
            $obs->setSize('50%', '150px');
            
            $action = new Action(array($this, 'onSave'));
            $action->setParameter('id_coop', $this->coop->id);
            $this->form->addAction('Salvar', $action);

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

            if (isset($dados->vitalicia[0]) && $dados->vitalicia[0] == '1') {

                    $dados->vitalicia = 1;
                    $dados->expiracao = '9999-01-01';

            } else  {

                $dados->vitalicia = 0;

            }

            $seguranca = new LicencasSeguranca(); // instancia objeto
            $seguranca->fromArray( (array) $dados); // carrega os dados 
            $seguranca->store(); // armazena o objeto no banco de dados
            
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

            Transaction::open('Sisac'); // inicia transação com o BD$
            
            if (isset($_REQUEST['id'])) {                
                $this->form->setData(LicencasSeguranca::find($_REQUEST['id']));             
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
