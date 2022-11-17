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
use Sisac\Widgets\Wrapper\FormWrapper;
use Sisac\Widgets\Form\Text;
/**
 * Formulário de cooperativas
 */
class AdForm extends Page
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
                $coop = Ad::find($_GET['id'])->id_coop;
            }
            $coop =  Cooperativa::find($coop);

            //Verificando se a Cooperativa possui servidores cadastrados
            $repository = new Repository('Servidor');
            $criteria = new Criteria();
            $criteria->add('id_coop', '=', $coop->id);
            $servidores = $repository->load($criteria);

            if(!$servidores) {

                ob_start();
                new Message('error', 'Esta Cooperativa não possui servidores cadastrados');
                $message = ob_get_contents();
                ob_clean();
                parent::add($message); 
                return;

            }

            // instancia um formulário
            $this->form = new FormWrapper(new Form('form_pas'));
            $this->form->setTitle("Dominio Active Directory: $coop->id - $coop->nome");
            
            // cria os campos do formulário
            $id    = new Hidden('id');
            $id_coop      = new Entry('id_coop');
            $nome      = new Entry('nome');
            $dc_primario    = new Combo('id_dcprimario');
            $dc_secundario    = new Combo('id_dcsecundario');
            $dns_primario    = new Combo('id_dnsprimario');
            $dns_secundario    = new Combo('id_dnssecundario');
            $obs     = new Text('obs');   
            
            $id_coop->setValue($coop->id);            
            $id_coop->setEditable(FALSE);

            $items = array();
            foreach ($servidores as $obj_servidor) {
                $items[$obj_servidor->id] = $obj_servidor->nome;
            }
            
            $dc_primario->addItems($items); 
            $dc_secundario->addItems($items); 
            $dns_primario->addItems($items); 
            $dns_secundario->addItems($items); 

            $this->form->addField('', $id, '0%');
            $this->form->addField('Cooperativa', $id_coop, '50%');
            $this->form->addField('Nome', $nome, '50%');
            $this->form->addField('DC Primário', $dc_primario, '50%');
            $this->form->addField('DC Secundário', $dc_secundario, '50%');
            $this->form->addField('DNS Primário', $dns_primario, '50%');
            $this->form->addField('DNS Secundário', $dns_secundario, '50%');
            $this->form->addField('Observações', $obs, '50%');

            $obs->setSize('50%','150px');
            
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
            
            foreach ($dados as $key => $value) {
                if ($value == '0') unset($dados->$key);                
            }

            $servers = ['dc', 'dns'];

            foreach ($servers as $key => $value) {
                $primario = "id_${value}primario";
                $secundario = "id_${value}secundario";
                if (isset($dados->$primario) && isset($dados->$secundario) && $dados->$primario == $dados->$secundario) {
                    unset($dados->$secundario);
                }
            }

            $this->form->setData($dados);

            $ad = new Ad; // instancia objeto
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
