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
use Sisac\Widgets\Base\Element;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Container\Panel;
use Sisac\Widgets\Wrapper\FormWrapper;

/**
 * Formulário de cooperativas
 */
class ServidoresForm extends Page
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
            
            $title = "Servidor";
            if (isset($_GET['id'])) {
                $servidor = Servidor::find($_GET['id']);
                $coop = Cooperativa::find($servidor->id_coop);
            } else {
                $coop = Cooperativa::find($_POST['id_coop']);
            }
            
            $title =  $title . ": {$coop->id} - {$coop->nome}";

            // instancia um formulário
            $this->form = new FormWrapper(new Form('form_servidores'));
            $this->form->setTitle($title);
            
            // cria os campos do formulário
            $id    = new Hidden('id');
            $id_coop    = new Entry('id_coop');
            $id_pa    = new Combo('id_pa');
            $nome      = new Entry('nome');
            $id_fabricante    = new Combo('id_fabricante');
            $modelo        = new Entry('modelo');
            $serial      = new Entry('serial');
            $id_tipo  = new Combo('id_tipo');
            $id_sistema_op    = new Combo('id_sistema_op');
            $status_hardware     = new Combo('id_status_hardware');
            $dt_garantia     = new Date('dt_garantia');
            $ip_lan        = new Entry('ip_lan');
            $ip_idrac        = new Entry('ip_idrac');
            $obs        = new Text('obs');
            
            $id_coop->setValue($coop->id);
            
            $pas = Pa::all();
            $items = array();
            foreach ($pas as $obj_pa) {
                $items[$obj_pa->id] = $obj_pa->numero;
            }
            $id_pa->addItems($items);
            
            $fabricantes = Fabricante::all();
            $items = array();
            foreach ($fabricantes as $obj_fabricante) {
                $items[$obj_fabricante->id] = $obj_fabricante->nome;
            }
            $id_fabricante->addItems($items);
            
            $sistemas = SistemaOperacional::all();
            $items = array();
            foreach ($sistemas as $obj_sistema) {
                $items[$obj_sistema->id] = $obj_sistema->nome;
            }
            $id_sistema_op->addItems($items);
            
            $tipos = TipoServidor::all();
            $items = array();
            foreach ($tipos as $obj_tipo) {
                $items[$obj_tipo->id] = $obj_tipo->nome;
            }                        
            $id_tipo->addItems($items);
            
            $hardwares = StatusHardware::all();
            $items = array();
            foreach ($hardwares as $obj_hardware) {
                $items[$obj_hardware->id] = $obj_hardware->nome;
            }                        
            $status_hardware->addItems($items);
            
            $this->form->addField('', $id, '0%');
            $this->form->addField('Cooperativa', $id_coop, '50%');
            $this->form->addField('PA', $id_pa, '20%');
            $this->form->addField('Nome', $nome, '50%');
            $this->form->addField('Fabricante', $id_fabricante, '50%');
            $this->form->addField('Modelo', $modelo, '50%');
            $this->form->addField('Número de Série', $serial, '50%');
            $this->form->addField('Tipo', $id_tipo, '20%');
            $this->form->addField('Sistema Operacional', $id_sistema_op, '50%');
            $this->form->addField('Garantia', $dt_garantia, '20%');
            $this->form->addField('Status do Hardware', $status_hardware, '50%');
            $this->form->addField('IP Lan', $ip_lan, '50%');
            $this->form->addField('IP IDrac', $ip_idrac, '50%');
            $this->form->addField('Observações', $obs, '50%');

            $id_coop->setEditable(FALSE);
            $ip_lan->setProperty('placeholder', 'XXX.XXX.XXX.XXX');
            $ip_idrac->setProperty('placeholder', 'XXX.XXX.XXX.XXX');
            $obs->setSize('50%','150px');
            
            $this->form->addAction('Salvar', new Action(array($this, 'onSave')));

            parent::add($this->form);

            if (isset($servidor->id)) {
                $action = new Action(['NotasServidoresForm', 'onedit']);
                $action->setParameter('id_servidor', $servidor->id);
                $this->form->addAction('Inserir Nota', $action);
            
                $painel = new Panel("Notas: {$servidor->nome}");

                $repository = new Repository('NotasServidores');
                $criteria = new Criteria();
                $criteria->add('id_servidor', '=', $servidor->id);
                $notas = $repository->load($criteria);
                foreach ($notas as $nota) {
                    
                    $nota->data_hora = date('d/m/Y - H:i:s', strtotime($nota->data_hora));
                    $painel2 = new Panel($nota->data_hora);
                    $painel2->add($nota->descricao);
                
                    $painel->add($painel2);

                }
                
                parent::add($painel);
            }      

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
            $servidor = new Servidor(); // instancia objeto
            $servidor->fromArray( (array) $dados); // carrega os dados
            $servidor->store(); // armazena o objeto no banco de dados
            
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
                $servidor = Servidor::find($id);
                $this->form->setData($servidor); // lança os dados da pessoa no formulário

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
